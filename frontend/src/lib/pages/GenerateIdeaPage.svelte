<script>
  import { onMount } from 'svelte'
  import MultiSelect from 'svelte-multiselect'
  import { generateIdea, getIdeas, updateIdea, deleteIdea, getAiProviders, getActivityTypeOptions, getSkillsList } from '../services/api.js'
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

  let editingIdea = $state(null)
  let editForm = $state({ idea_nama: '', idea_keterangan: '', idea_moral: '', idea_type: '' })
  let editSaving = $state(false)

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
  ]

  const ageOptions = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11]

  let form = $state({
    type: 'storytelling',
    theme: '',
    count: 20,
    provider: '',
    ages: [3, 4, 5, 6, 7, 8],
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

  async function fetchIdeas() {
    ideasLoading = true
    try {
      const params = {}
      if (searchQuery) params.search = searchQuery
      if (filterType) params.type = filterType
      const res = await getIdeas(params)
      const list = res?.data || res || []
      ideas = Array.isArray(list) ? list.filter(Boolean) : []
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
      const res = await generateIdea({
        type: form.type,
        theme: form.theme,
        count: form.count,
        provider: form.provider || undefined,
        ages: form.ages,
        skills: form.selectedSkills.map(s => s.value),
        agama: form.agama || undefined,
      })
      resultMsg = res?.message || 'Job dispatched!'
      setTimeout(fetchIdeas, 3000)
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
      idea_moral: idea.idea_moral || '',
      idea_type: idea.idea_type || '',
    }
  }

  async function saveEdit() {
    if (!editingIdea) return
    editSaving = true
    try {
      await updateIdea(editingIdea.idea_id, editForm)
      editingIdea = null
      fetchIdeas()
    } catch (e) { /* ignore */ }
    editSaving = false
  }

  async function handleDelete(idea) {
    if (!confirm(`Hapus "${idea.idea_nama}"?`)) return
    try {
      await deleteIdea(idea.idea_id)
      fetchIdeas()
    } catch (e) { /* ignore */ }
  }

  function getTypeEmoji(type) {
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

        <div class="lg:col-span-1">
          <div class="bg-white rounded-[24px] border-4 border-[#B7D9BC] p-5 shadow-md space-y-4 sticky top-4">
            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Tema</label>
              <textarea bind:value={form.theme} rows="3"
                placeholder="contoh: tema laut, ikan, kehidupan bawah laut"
                class="w-full px-4 py-3 rounded-2xl border-2 border-[#B7D9BC] focus:border-primary outline-none transition bg-white text-sm resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Type</label>
                <select bind:value={form.type}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
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
                    class="px-3 py-1.5 rounded-xl text-xs font-bold border-2 transition-all"
                    style="background: {form.ages.includes(age) ? '#E1F2E5' : 'white'};
                           border-color: {form.ages.includes(age) ? '#176c33' : '#B7D9BC'};
                           color: {form.ages.includes(age) ? '#176c33' : '#666'}">
                    {age}
                  </button>
                {/each}
              </div>
            </div>

            <div>
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
                <span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                Generating...
              {:else}
                <span class="material-symbols-outlined text-lg">auto_awesome</span>
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
          <div class="flex items-center gap-2">
            <div class="relative flex-1">
              <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
              <input type="text" bind:value={searchQuery} oninput={() => fetchIdeas()}
                placeholder="Cari ide..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none transition bg-white text-sm" />
            </div>
            <select bind:value={filterType} onchange={() => fetchIdeas()}
              class="px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
              <option value="">Semua Type</option>
              {#each activityTypes as t}
                <option value={t.value}>{t.emoji} {t.label}</option>
              {/each}
            </select>
            <button onclick={() => fetchIdeas()}
              class="w-10 h-10 rounded-xl border-2 border-[#B7D9BC] bg-white flex items-center justify-center hover:border-primary transition-colors">
              <span class="material-symbols-outlined text-lg text-on-surface-variant">refresh</span>
            </button>
          </div>

          {#if ideasLoading}
            <div class="bg-canvas-cream rounded-[24px] p-6 text-center border-4 border-[#B7D9BC]">
              <div class="text-2xl animate-spin">⏳</div>
            </div>
          {:else if ideas.length === 0}
            <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
              <div class="text-5xl mb-3">💡</div>
              <p class="font-label-lg text-text-main mb-1">Belum Ada Ide</p>
              <p class="text-sm text-on-surface-variant">Generate ide baru atau gunakan command artisan.</p>
            </div>
          {:else}
            <div class="space-y-3">
              {#each ideas as idea, idx (idea?.idea_id ?? idx)}
                <div class="bg-white rounded-[20px] p-4 border-2 border-[#B7D9BC] shadow-sm hover:shadow-md transition-shadow">
                  <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl bg-success-soft border-2 border-[#B7D9BC] shrink-0">
                      {getTypeEmoji(idea.idea_type)}
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2 mb-1.5">
                        <h3 class="text-base font-bold text-text-main leading-snug">{idea.idea_nama}</h3>
                      </div>
                      <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-success-soft text-primary mb-2">{idea.idea_type}</span>
                      <p class="text-sm text-on-surface-variant leading-relaxed">{idea.idea_keterangan}</p>
                      {#if idea.idea_moral}
                        <div class="mt-2 bg-success-soft/50 rounded-xl px-3 py-2 border border-[#B7D9BC]/50">
                          <p class="text-sm text-primary font-medium">💬 {idea.idea_moral}</p>
                        </div>
                      {/if}
                    </div>
                    <div class="flex flex-col gap-1 shrink-0">
                      <button onclick={() => openEdit(idea)}
                        class="w-8 h-8 rounded-lg border-2 border-[#B7D9BC] bg-white flex items-center justify-center hover:border-primary transition-colors">
                        <span class="material-symbols-outlined text-sm text-on-surface-variant">edit</span>
                      </button>
                      <button onclick={() => handleDelete(idea)}
                        class="w-8 h-8 rounded-lg border-2 border-[#B7D9BC] bg-white flex items-center justify-center hover:border-error transition-colors">
                        <span class="material-symbols-outlined text-sm text-error">delete</span>
                      </button>
                    </div>
                  </div>
                </div>
              {/each}
            </div>
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
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Moral</label>
            <input type="text" bind:value={editForm.idea_moral}
              class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Type</label>
            <select bind:value={editForm.idea_type}
              class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
              {#each activityTypes as t}
                <option value={t.value}>{t.emoji} {t.label}</option>
              {/each}
            </select>
          </div>
        </div>
        <div class="p-5 pt-0">
          <button onclick={saveEdit} disabled={editSaving}
            class="w-full py-3 rounded-2xl text-white text-sm font-bold disabled:opacity-50"
            style="background: #176C33; box-shadow: 0 6px 0 #0d4a22;">
            {editSaving ? 'Menyimpan...' : 'Simpan'}
          </button>
        </div>
      </div>
    </div>
  {/if}
{/if}
