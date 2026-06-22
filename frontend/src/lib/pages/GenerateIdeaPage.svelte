<script>
  import { onMount } from 'svelte'
  import MultiSelect from 'svelte-multiselect'
  import { generateIdea, getIdeas, updateIdea, deleteIdea, ideaToActivity, getAiProviders, getActivityTypeOptions, getSkillsList, batchDeleteIdeas } from '../services/api.js'
  import { userRole } from '../stores/authStore.js'

  let userRoleVal = $state('')
  let generating = $state(false)
  let resultMsg = $state('')

  let activityTypes = $state([])
  let providers = $state([{ value: '', label: 'Default' }])
  let skills = $state([])
  let loading = $state(true)

  let ideas = $state([])
  let ideasLoading = $state(false)
  let searchQuery = $state('')
  let filterType = $state('')
  let currentPage = $state(1)
  let totalPages = $state(1)
  let totalItems = $state(0)
  let perPage = $state(20)

  let editingIdea = $state(null)
  let editForm = $state({ idea_nama: '', idea_keterangan: '', idea_informasi: '', idea_type: '' })
  let editSaving = $state(false)
  let editGenerating = $state(false)

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })

  const agamaOptions = [
    { value: '', label: '-' },
    { value: 'islam', label: 'Islam' },
    { value: 'kristen', label: 'Kristen' },
    { value: 'katholik', label: 'Katholik' },
    { value: 'hindu', label: 'Hindu' },
    { value: 'budha', label: 'Budha' },
    { value: 'konghucu', label: 'Konghucu' },
  ]

  const ageOptions = [3, 4, 5, 6, 7, 8, 9, 10]

  let form = $state({
    type: '',
    theme: '',
    count: 10,
    provider: '',
    ages: [3, 4, 5, 6, 7, 8, 9, 10],
    selectedSkills: [],
    agama: '',
  })

  onMount(async () => {
    const [typesRes, provRes, skillsRes] = await Promise.all([
      getActivityTypeOptions().catch(() => []),
      getAiProviders().catch(() => ({})),
      getSkillsList().catch(() => []),
    ])

    if (Array.isArray(typesRes)) activityTypes = typesRes
    if (provRes && typeof provRes === 'object') {
      providers = [
        { value: '', label: 'Default' },
        ...Object.keys(provRes).map(k => ({ value: k, label: k.charAt(0).toUpperCase() + k.slice(1) })),
      ]
    }
    if (Array.isArray(skillsRes)) skills = skillsRes
    else if (skillsRes && typeof skillsRes === 'object') skills = Object.values(skillsRes).flat()

    loading = false
    fetchIdeas()
  })

  async function fetchIdeas(page = 1) {
    ideasLoading = true
    try {
      const params = { page, per_page: perPage }
      if (searchQuery) params.search = searchQuery
      if (filterType) params.type = filterType
      const res = await getIdeas(params)
      if (res?.data) {
        ideas = Array.isArray(res.data) ? res.data.filter(Boolean) : []
        currentPage = res.current_page || 1
        totalPages = res.last_page || 1
        totalItems = res.total || 0
      } else {
        ideas = Array.isArray(res) ? res.filter(Boolean) : []
      }
    } catch (e) { /* ignore */ }
    ideasLoading = false
  }

  function toggleAge(age) {
    form.ages = form.ages.includes(age)
      ? form.ages.filter(a => a !== age)
      : [...form.ages, age].sort()
  }

  async function handleGenerate() {
    if (!form.theme.trim()) {
      resultMsg = 'Isi tema terlebih dahulu'
      return
    }
    generating = true
    resultMsg = ''
    try {
      const payload = {
        theme: form.theme,
        count: form.count,
        provider: form.provider || undefined,
        ages: form.ages,
        skills: form.selectedSkills.map(s => s.value),
        agama: form.agama || undefined,
      }
      if (form.type) payload.type = form.type
      const res = await generateIdea(payload)
      resultMsg = res?.message || 'Job dispatched!'
      fetchIdeas(1)
    } catch (e) {
      resultMsg = 'Gagal: ' + (e.message || 'Error')
    }
    generating = false
  }

  function openEdit(idea) {
    editingIdea = idea
    editForm = {
      idea_nama: idea.idea_nama || '',
      idea_keterangan: idea.idea_keterangan || '',
      idea_informasi: idea.idea_informasi || '',
      idea_type: idea.idea_type || '',
      idea_qty: idea.idea_qty || 10,
      idea_prompt: idea.idea_prompt || '',
    }
  }

  async function saveEdit() {
    if (!editingIdea) return
    editSaving = true
    try {
      await updateIdea(editingIdea.idea_id, editForm)
      editingIdea = null
      fetchIdeas(currentPage)
    } catch (e) { /* ignore */ }
    editSaving = false
  }

  async function handleEditGenerateActivity() {
    if (!editingIdea) return
    const type = editForm.idea_type || filterType || form.type
    if (!type) {
      alert('Pilih type terlebih dahulu')
      return
    }
    editGenerating = true
    try {
      await ideaToActivity(editingIdea.idea_id, { type })
      editingIdea = null
      fetchIdeas(currentPage)
    } catch (e) { /* ignore */ }
    editGenerating = false
  }

  async function handleDelete(idea) {
    if (!confirm(`Hapus "${idea.idea_nama}"?`)) return
    try {
      await deleteIdea(idea.idea_id)
      fetchIdeas(currentPage)
    } catch (e) { /* ignore */ }
  }

  let generatingActivity = $state(null)
  let selectedIdeas = $state(new Set())
  let batchGenerating = $state(false)
  let batchDeleting = $state(false)

  const allSelected = $derived(ideas.length > 0 && ideas.every(i => selectedIdeas.has(i.idea_id)))
  const selectedCount = $derived(selectedIdeas.size)

  function toggleSelect(ideaId) {
    const next = new Set(selectedIdeas)
    if (next.has(ideaId)) next.delete(ideaId)
    else next.add(ideaId)
    selectedIdeas = next
  }

  function toggleSelectAll() {
    if (allSelected) {
      selectedIdeas = new Set()
    } else {
      selectedIdeas = new Set(ideas.map(i => i.idea_id))
    }
  }

  async function handleGenerateActivity(idea) {
    if (!idea.idea_type && !filterType && !form.type) {
      alert('Set type terlebih dahulu di edit atau pilih type di filter')
      return
    }
    generatingActivity = idea.idea_id
    try {
      await ideaToActivity(idea.idea_id, { type: idea.idea_type || filterType || form.type })
    } catch (e) { /* ignore */ }
    generatingActivity = null
    fetchIdeas(currentPage)
  }

  async function handleBatchGenerate() {
    if (selectedIdeas.size === 0) return
    batchGenerating = true
    const ids = [...selectedIdeas]
    for (const id of ids) {
      const idea = ideas.find(i => i.idea_id === id)
      try { await ideaToActivity(id, { type: idea?.idea_type || filterType || form.type }) } catch (e) { /* ignore */ }
    }
    batchGenerating = false
    selectedIdeas = new Set()
    fetchIdeas(currentPage)
  }

  async function handleBatchDelete() {
    if (selectedIdeas.size === 0) return
    if (!confirm(`Hapus ${selectedIdeas.size} ide yang dipilih?`)) return
    batchDeleting = true
    try {
      await batchDeleteIdeas([...selectedIdeas])
      selectedIdeas = new Set()
      fetchIdeas(currentPage)
    } catch (e) { /* ignore */ }
    batchDeleting = false
  }

  function getTypeEmoji(type) {
    if (!type) return '💡'
    const found = activityTypes.find(t => t.value === type)
    return found?.emoji || '💡'
  }
</script>

{#if userRoleVal !== 'developer'}
  <div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
    <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
      <div class="text-5xl mb-3">🔒</div>
      <p class="font-label-lg text-text-main mb-1">Akses Terbatas</p>
      <p class="text-sm text-on-surface-variant">Halaman ini hanya untuk developer.</p>
    </div>
  </div>
{:else}
  <div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
    <section class="mb-stack-lg">
      <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main leading-tight mb-2 flex items-center gap-2">
        <span class="w-10 h-10 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-xl">✨</span> Generate Idea
      </h2>
    </section>

    {#if loading}
      <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-[#B7D9BC]">
        <div class="text-3xl mb-2 animate-spin">⏳</div>
        <p class="text-sm text-on-surface-variant">Memuat data...</p>
      </div>
    {:else}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="lg:col-span-1 mb-5 relative z-10">
          <div class="bg-white rounded-[24px] border-4 border-[#B7D9BC] p-5 shadow-md space-y-4 sticky top-4 overflow-visible">
            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Tema</label>
              <textarea bind:value={form.theme} rows="3"
                placeholder="contoh: tema laut, ikan, kehidupan bawah laut"
                class="w-full px-4 py-3 rounded-2xl border-2 border-[#B7D9BC] focus:border-primary outline-none transition bg-white text-sm resize-none"></textarea>
            </div>

            <div class="grid grid-cols-[7fr_3fr] gap-3">
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Type <span class="text-on-surface-variant/50">(opsional)</span></label>
                <select bind:value={form.type}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
                  <option value="">💡 Global</option>
                  {#each activityTypes as t}
                    <option value={t.value}>{t.emoji} {t.label}</option>
                  {/each}
                </select>
              </div>
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Jumlah</label>
                <input type="number" bind:value={form.count} min="1" max="50"
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">AI Provider</label>
                <select bind:value={form.provider}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
                  {#each providers as p}
                    <option value={p.value}>{p.label}</option>
                  {/each}
                </select>
              </div>
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Agama</label>
                <select bind:value={form.agama}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
                  {#each agamaOptions as a}
                    <option value={a.value}>{a.label}</option>
                  {/each}
                </select>
              </div>
            </div>

            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Ages</label>
              <div class="flex flex-wrap gap-1.5">
                {#each ageOptions as age}
                  <button onclick={() => toggleAge(age)}
                    class="px-2 py-1 rounded-xl text-xs font-bold border-2 transition-all"
                    style="background: {form.ages.includes(age) ? '#E1F2E5' : 'white'};
                           border-color: {form.ages.includes(age) ? '#176c33' : '#B7D9BC'};
                           color: {form.ages.includes(age) ? '#176c33' : '#666'}">
                    {age}
                  </button>
                {/each}
              </div>
            </div>

            <div class="relative z-20">
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Skills</label>
              <MultiSelect
                options={skills.map(s => ({ value: s.key, label: `${s.label} (${s.pilar})` }))}
                bind:selected={form.selectedSkills}
                placeholder="Pilih skills..."
                maxSelect={10}
                --sms-border="2px solid #B7D9BC"
                --sms-border-radius="12px"
                --sms-focus-border="2px solid #176c33"
                --sms-min-height="42px"
                --sms-padding="8px 12px"
              />
            </div>

            <button onclick={handleGenerate} disabled={generating}
              class="w-full py-3 rounded-2xl text-white text-sm font-bold disabled:opacity-50 flex items-center justify-center gap-2"
              style="background: #176C33; box-shadow: 0 6px 0 #0d4a22;">
              {#if generating}
                <span class="text-lg animate-spin">⏳</span>
                Generating...
              {:else}
                <span class="text-lg">✨</span>
                Generate Idea
              {/if}
            </button>

            {#if resultMsg}
              <div class="rounded-xl p-3 text-xs font-bold"
                class:bg-success-soft={resultMsg.includes('dispatched')}
                class:text-primary={resultMsg.includes('dispatched')}
                class:bg-error-soft={resultMsg.includes('Gagal')}
                class:text-error={resultMsg.includes('Gagal')}>
                {resultMsg}
              </div>
            {/if}
          </div>
        </div>

        <div class="lg:col-span-2 space-y-3">
          <div class="space-y-2">
            <select bind:value={filterType} onchange={() => fetchIdeas(1)}
              class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
              <option value="">Semua Type</option>
              <option value="global">💡 Global</option>
              {#each activityTypes as t}
                <option value={t.value}>{t.emoji} {t.label}</option>
              {/each}
            </select>
            <div class="flex items-center gap-2">
              <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">🔍</span>
                <input type="text" bind:value={searchQuery} oninput={() => fetchIdeas(1)}
                  placeholder="Cari ide..."
                  class="w-full z-0 pl-10 pr-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none transition bg-white text-sm" />
              </div>
              <button onclick={() => fetchIdeas(currentPage)}
                class="w-10 h-10 rounded-xl border-2 border-[#B7D9BC] bg-white flex items-center justify-center hover:border-primary transition-colors shrink-0">
                <span class="text-lg text-on-surface-variant">💾</span>
              </button>
            </div>
          </div>

          {#if ideasLoading}
            <div class="bg-canvas-cream rounded-[24px] p-6 text-center border-4 border-[#B7D9BC]">
              <div class="text-2xl animate-spin">⏳</div>
            </div>
          {:else if ideas.length === 0}
            <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
              <div class="text-5xl mb-3">💡</div>
              <p class="font-label-lg text-text-main mb-1">Belum Ada Ide</p>
              <p class="text-sm text-on-surface-variant">Generate ide baru.</p>
            </div>
          {:else}
            <div class="flex items-center gap-2 mb-2">
              <button onclick={toggleSelectAll}
                class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-[#B7D9BC] bg-white text-xs font-bold text-on-surface-variant hover:border-primary transition-colors">
                <span class="text-base">
                  {allSelected ? '☑' : selectedCount > 0 ? '☒' : '☐'}
                </span>
                {allSelected ? 'Batal Pilih' : 'Pilih Semua'}
              </button>
              {#if selectedCount > 0}
                <button onclick={handleBatchGenerate} disabled={batchGenerating}
                  class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white disabled:opacity-70"
                  style="background: #176C33; box-shadow: 0 4px 0 #0d4a22;">
                  <span class="text-base" class:animate-spin={batchGenerating}>
                    {batchGenerating ? '⏳' : '✨'}
                  </span>
                  {batchGenerating ? 'Process...' : `Generate ${selectedCount} Activity`}
                </button>
                <button onclick={handleBatchDelete} disabled={batchDeleting}
                  class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white disabled:opacity-70"
                  style="background: #C62828; box-shadow: 0 4px 0 #8e1c1c;">
                  <span class="text-base" class:animate-spin={batchDeleting}>
                    {batchDeleting ? '⏳' : '❌'}
                  </span>
                  {batchDeleting ? 'Menghapus...' : `Hapus ${selectedCount}`}
                </button>
              {/if}
            </div>
            <div class="space-y-3">
              {#each ideas as idea, idx (idea?.idea_id ?? idx)}
                <div class="bg-white rounded-[20px] p-4 border-2 shadow-sm hover:shadow-md transition-shadow {selectedIdeas.has(idea.idea_id) ? 'border-primary ring-2 ring-primary/20' : 'border-[#B7D9BC]'}">
                  <div class="flex items-center gap-2 mb-2">
                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-success-soft text-primary">
                      <span class="text-xs">{getTypeEmoji(idea.idea_type)}</span>
                      {idea.idea_type || 'global'}
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">
                      x{idea.idea_qty || 10}
                    </span>
                    {#if idea.idea_prompt}
                      <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-0.5 rounded-full bg-primary/10 text-primary" title="Prompt tersimpan">
                        📋
                      </span>
                    {/if}
                  </div>
                  <h3 class="text-base font-bold text-text-main leading-snug mb-1.5">{idea.idea_nama}</h3>
                  <p class="text-sm text-on-surface-variant leading-relaxed">{idea.idea_keterangan}</p>
                  {#if idea.idea_informasi}
                    <div class="mt-2 bg-success-soft/50 rounded-xl px-3 py-2 border border-[#B7D9BC]/50">
                      <p class="text-sm text-primary font-medium">💬 {idea.idea_informasi}</p>
                    </div>
                  {/if}
                  <div class="flex items-center gap-2 mt-3 pt-3 border-t-2 border-[#B7D9BC]/50">
                    <button onclick={() => toggleSelect(idea.idea_id)}
                      class="shrink-0">
                      <span class="text-xl {selectedIdeas.has(idea.idea_id) ? 'text-primary' : 'text-on-surface-variant/40'}">
                        {selectedIdeas.has(idea.idea_id) ? '☑' : '☐'}
                      </span>
                    </button>
                    <div class="flex-1"></div>
                    <button onclick={() => handleGenerateActivity(idea)} disabled={generatingActivity === idea.idea_id}
                      class="px-3 py-1.5 rounded-lg border-2 border-primary bg-primary/10 flex items-center gap-1 text-xs font-bold text-primary hover:bg-primary/20 transition-colors disabled:opacity-70">
                      <span class="text-sm" class:animate-spin={generatingActivity === idea.idea_id}>
                        {generatingActivity === idea.idea_id ? '⏳' : '✨'}
                      </span>
                      {generatingActivity === idea.idea_id ? 'Process...' : 'AI'}
                    </button>
                    <button onclick={() => openEdit(idea)}
                      class="px-3 py-1.5 rounded-lg border-2 border-[#B7D9BC] bg-white flex items-center gap-1 text-xs font-bold text-on-surface-variant hover:border-primary transition-colors">
                      <span class="text-sm">✏️</span>

                    </button>
                    <button onclick={() => handleDelete(idea)}
                      class="px-3 py-1.5 rounded-lg border-2 border-[#B7D9BC] bg-white flex items-center gap-1 text-xs font-bold text-error hover:border-error transition-colors">
                      <span class="text-sm">❌</span>

                    </button>
                  </div>
                </div>
              {/each}
            </div>

            {#if totalPages > 1}
              <div class="flex items-center justify-between pt-3 border-t-2 border-[#B7D9BC]/50">
                <span class="text-xs text-on-surface-variant">
                  {totalItems} ide • Hal {currentPage}/{totalPages}
                </span>
                <div class="flex gap-2">
                  <button onclick={() => fetchIdeas(currentPage - 1)} disabled={currentPage <= 1}
                    class="px-3 py-1.5 rounded-lg border-2 border-[#B7D9BC] bg-white text-xs font-bold text-on-surface-variant disabled:opacity-40 hover:border-primary transition-colors">
                    ← Prev
                  </button>
                  <button onclick={() => fetchIdeas(currentPage + 1)} disabled={currentPage >= totalPages}
                    class="px-3 py-1.5 rounded-lg border-2 border-[#B7D9BC] bg-white text-xs font-bold text-on-surface-variant disabled:opacity-40 hover:border-primary transition-colors">
                    Next →
                  </button>
                </div>
              </div>
            {/if}
          {/if}
        </div>

      </div>
    {/if}
  </div>

  {#if editingIdea}
    <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
    <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center lg:p-4" onclick={() => editingIdea = null}>
      <div class="w-full max-w-md bg-canvas-cream rounded-t-[32px] lg:rounded-[32px] shadow-2xl border-4 border-[#B7D9BC] overflow-hidden"
        onclick={(e) => e.stopPropagation()}>
        <div class="p-5 flex items-center justify-between border-b-2 border-[#B7D9BC]/50">
          <h3 class="font-bold text-lg text-text-main">Edit Idea</h3>
          <button onclick={() => editingIdea = null}
            class="w-10 h-10 rounded-full bg-error text-white flex items-center justify-center text-lg shadow-md">
            ✕
          </button>
        </div>
        <div class="p-5 space-y-4 max-h-[60vh] overflow-y-auto">
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Nama</label>
            <input type="text" bind:value={editForm.idea_nama}
              class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Keterangan</label>
            <textarea bind:value={editForm.idea_keterangan} rows="3"
              class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white resize-none"></textarea>
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Informasi</label>
            <input type="text" bind:value={editForm.idea_informasi}
              class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Type</label>
            <select bind:value={editForm.idea_type}
              class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
              <option value="">💡 Global</option>
              {#each activityTypes as t}
                <option value={t.value}>{t.emoji} {t.label}</option>
              {/each}
            </select>
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Qty (jumlah activity yang dibuat)</label>
            <input type="number" bind:value={editForm.idea_qty} min="1" max="100"
              class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
          </div>
          {#if editForm.idea_prompt}
            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Prompt</label>
              <textarea value={editForm.idea_prompt} rows="6" readonly
                class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] outline-none text-xs bg-gray-50 text-on-surface-variant resize-none font-mono"></textarea>
            </div>
          {/if}
        </div>
        <div class="p-5 pt-0 flex gap-3">
          <button onclick={saveEdit} disabled={editSaving || editGenerating}
            class="flex-1 py-3 rounded-2xl text-white text-sm font-bold disabled:opacity-50"
            style="background: #176C33; box-shadow: 0 6px 0 #0d4a22;">
            {editSaving ? 'Menyimpan...' : 'Simpan'}
          </button>
          <button onclick={handleEditGenerateActivity} disabled={editGenerating || editSaving}
            class="flex-1 py-3 rounded-2xl text-white text-sm font-bold disabled:opacity-50 flex items-center justify-center gap-2"
            style="background: #FF9800; box-shadow: 0 6px 0 #c77700;">
            {#if editGenerating}
              <span class="text-lg animate-spin">⏳</span>
              Generating...
            {:else}
              <span class="text-lg">✨</span>
              Generate AI
            {/if}
          </button>
        </div>
      </div>
    </div>
  {/if}
{/if}


<style>
  :global(.multiselect .options) {
    position: absolute !important;
    z-index: 50 !important;
  }
</style>