<script>
  import { anakList } from '../stores/anakStore.js'
  import { toolsAnakId, toolsData, addChallenge, addPoint, removePoint, editChallenge, deleteChallenge, addChallengeHistory } from '../stores/toolsStore.js'
  import { kategoriChallenge } from '../data/challenge.js'
  import AppModal from '../components/AppModal.svelte'
  import AppInput from '../components/AppInput.svelte'
  import AnakDropdown from '../components/AnakDropdown.svelte'
  import { shareProgress, shareChallenge } from '../utils/share.js'

  let toolsDataVal = $state({ challenges: [], challengeHistory: [] })
  let anakListVal = $state([])
  let toolsAnakIdVal = $state(null)

  $effect(() => {
    const u1 = toolsData.subscribe(v => toolsDataVal = v)
    const u2 = anakList.subscribe(v => anakListVal = v)
    const u3 = toolsAnakId.subscribe(v => toolsAnakIdVal = v)
    return () => { u1(); u2(); u3() }
  })

  let showHistory = $state(false)
  let showAddForm = $state(false)
  let showEditForm = $state(false)
  let editingId = $state(null)

  let errors = $state({ category: '', title: '', maxPoints: '' })

  const defaultForm = { category: '', title: '', notes: '', maxPoints: 10 }
  let form = $state({ ...defaultForm })
  let editFormState = $state({ ...defaultForm })

  const activeChallenges = $derived(toolsDataVal.challenges.filter(c => c.points < c.maxPoints))

  const historyByCategory = $derived.by(() => {
    const grouped = {}
    for (const c of toolsDataVal.challengeHistory) {
      if (!grouped[c.category]) grouped[c.category] = []
      grouped[c.category].push(c)
    }
    return grouped
  })

  const selectedAnakName = $derived.by(() => {
    const a = anakListVal.find(a => a.id === toolsAnakIdVal)
    return a ? a.nama : 'Anak'
  })

  function pointPercent(c) {
    return Math.min(100, Math.round((c.points / c.maxPoints) * 100))
  }

  function handleAddPoint(c) {
    addPoint({ id: c.id, amount: 1 })
    const newPoints = Math.min(c.maxPoints, c.points + 1)
    if (newPoints >= c.maxPoints) {
      addChallengeHistory({ ...c, points: newPoints, maxPoints: c.maxPoints })
      deleteChallenge({ id: c.id })
    }
  }

  function handleRemovePoint(c) {
    removePoint({ id: c.id })
  }

  function handleShareProgress(c) {
    shareProgress({
      title: c.title,
      category: c.category,
      emoji: c.emoji,
      color: c.color,
      points: c.points,
      maxPoints: c.maxPoints,
      notes: c.notes,
      childName: selectedAnakName,
      isComplete: false
    })
  }

  function handleShareChallenge(c) {
    shareChallenge({
      title: c.title,
      category: c.category,
      emoji: c.emoji,
      maxPoints: c.maxPoints,
      childName: selectedAnakName
    })
  }

  function handleDeleteChallenge(c) {
    deleteChallenge({ id: c.id })
  }

  function openEdit(c) {
    editingId = c.id
    editFormState = {
      category: c.category,
      title: c.title,
      notes: c.notes || '',
      maxPoints: c.maxPoints
    }
    showEditForm = true
  }

  function closeEdit() {
    showEditForm = false
    editingId = null
    errors = { category: '', title: '', maxPoints: '' }
  }

  function validateForm(data) {
    errors = { category: '', title: '', maxPoints: '' }
    let valid = true
    if (!data.category) {
      errors.category = 'Kategori wajib dipilih'
      valid = false
    }
    if (!data.title || !data.title.trim()) {
      errors.title = 'Nama challenge wajib diisi'
      valid = false
    }
    const pts = Number(data.maxPoints)
    if (!data.maxPoints || !Number.isInteger(pts) || pts < 1) {
      errors.maxPoints = 'Target poin harus angka bulat minimal 1'
      valid = false
    }
    return valid
  }

  function saveEdit() {
    if (!validateForm(editFormState)) return
    const cat = kategoriChallenge[editFormState.category] || kategoriChallenge['Lainnya']
    editChallenge({
      id: editingId,
      category: editFormState.category,
      title: editFormState.title.trim(),
      notes: editFormState.notes.trim(),
      emoji: cat.emoji,
      bg: cat.bg,
      color: cat.color,
      maxPoints: Number(editFormState.maxPoints) || 10
    })
    closeEdit()
  }

  function closeForm() {
    showAddForm = false
    form = { ...defaultForm }
    errors = { category: '', title: '', maxPoints: '' }
  }

  function saveForm() {
    if (!validateForm(form)) return
    const max = Number(form.maxPoints) || 10
    const cat = kategoriChallenge[form.category] || kategoriChallenge['Lainnya']
    addChallenge({
      id: Date.now(),
      category: form.category,
      title: form.title.trim(),
      notes: form.notes.trim(),
      emoji: cat.emoji,
      bg: cat.bg,
      color: cat.color,
      points: 0,
      maxPoints: max
    })
    closeForm()
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  <AnakDropdown anakList={anakListVal} value={toolsAnakIdVal} onselect={(id) => toolsAnakId.set(id)} />
  <div class="flex items-center justify-between mb-4 mt-5">
    <h3 class="font-headline-md text-text-main flex items-center gap-2">
      <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">🏆</span> Challenge
    </h3>
    <button onclick={() => showHistory = !showHistory}
      class="flex items-center gap-1.5 text-sm font-bold text-primary transition-colors bg-success-soft px-3 py-1.5 rounded-full">
      <span class="material-symbols-outlined text-lg">{showHistory ? 'close' : 'history'}</span>
      {showHistory ? 'Tutup' : 'History'}
    </button>
  </div>

  {#if !showHistory}
    <div class="space-y-4">
      {#each activeChallenges as c (c.id)}
        <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md cursor-pointer hover:shadow-lg hover:scale-[1.01] transition-all"
          role="button" tabindex="0" onclick={() => openEdit(c)} onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openEdit(c); } }}>
          <div class="flex items-end gap-4">
            <div class="flex-1 min-w-0">
                <div class="mb-1">
                  <p class="text-[11px] font-bold uppercase tracking-wider mb-0.5" style="color: {c.color}">{c.category}</p>
                  <p class="font-label-lg font-bold text-text-main">{c.title}</p>
                  <p class="text-xs text-on-surface-variant mt-0.5">{pointPercent(c)}% progress ({c.points}/{c.maxPoints} poin)</p>
                  {#if c.notes}
                    <p class="text-xs text-on-surface-variant mt-0.5">{c.notes}</p>
                  {/if}
                </div>

              <div class="flex items-center gap-2 mt-4">
                <button onclick={(e) => { e.stopPropagation(); handleRemovePoint(c) }}
                  class="h-9 px-3 rounded-xl text-xs font-bold border-2 transition-all active:scale-95"
                  style="border-color: {c.color}80; color: {c.color}">
                  -1 Poin
                </button>
                <button onclick={(e) => { e.stopPropagation(); handleAddPoint(c) }}
                  class="h-9 px-3 rounded-xl text-xs font-bold text-white transition-all active:scale-95"
                  style="background: {c.color}">
                  +1 Poin
                </button>
                <button onclick={(e) => { e.stopPropagation(); handleShareProgress(c) }}
                  class="h-9 w-9 rounded-xl text-xs font-bold border-2 transition-all active:scale-95 flex items-center justify-center"
                  style="border-color: {c.color}80; color: {c.color}">
                  <span class="material-symbols-outlined text-base">share</span>
                </button>
                <button onclick={(e) => { e.stopPropagation(); handleDeleteChallenge(c) }}
                  class="h-9 w-9 rounded-xl text-xs font-bold border-2 transition-all active:scale-95 flex items-center justify-center border-error/30 text-error">
                  <span class="material-symbols-outlined text-base">delete</span>
                </button>
              </div>
            </div>

            <div class="celengan shrink-0 flex flex-col items-center self-stretch">
              <div class="celengan-tube relative w-16 h-full rounded-xl overflow-hidden border-2 shadow-lg"
                style="border-color: {c.color}60; background: {c.color}10; box-shadow: 0 4px 16px {c.color}40">
                <div class="absolute bottom-0 left-0 right-0 transition-all duration-700 rounded-b-xl"
                  style="height: {pointPercent(c)}%; background: linear-gradient(to top, {c.color}, {c.color}CC)">
                </div>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                  <span class="text-lg font-extrabold leading-none"
                    style="color: {pointPercent(c) > 50 ? '#FFF9F3' : c.color}">{pointPercent(c)}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      {/each}

      {#if !activeChallenges.length}
        <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
          <p class="text-3xl mb-2">🏅</p>
          <p class="text-sm text-on-surface-variant font-medium">Belum ada challenge aktif</p>
        </div>
      {/if}
      <button onclick={() => showAddForm = true}
        class="w-full py-3 rounded-2xl text-sm font-bold text-white btn-pop-green flex items-center justify-center gap-2">
        <span class="material-symbols-outlined text-lg">add</span>
        Tambah Challenge
      </button>
    </div>
  {:else}
    <div class="space-y-5">
      {#each Object.entries(historyByCategory) as [cat, items] (cat)}
        <div>
          <div class="flex items-center gap-2 mb-2">
            <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">{cat}</h4>
            <span class="text-xs font-bold text-primary bg-success-soft px-2 py-0.5 rounded-full">{items.length}</span>
          </div>
          <div class="space-y-2">
            {#each items as c (c.id)}
              <div class="bg-canvas-cream rounded-2xl p-4 border-2 border-[#B7D9BC] shadow-sm flex items-center gap-3">
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-text-main">{c.title}</p>
                  <p class="text-xs text-on-surface-variant">{c.maxPoints} poin terkumpul</p>
                </div>
                <button onclick={() => handleShareChallenge(c)}
                  class="w-9 h-9 rounded-full flex items-center justify-center transition-all active:scale-95 bg-success-soft text-primary">
                  <span class="material-symbols-outlined text-xl">share</span>
                </button>
              </div>
            {/each}
          </div>
        </div>
      {/each}

      {#if !toolsDataVal.challengeHistory.length}
        <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
          <p class="text-3xl mb-2">📜</p>
          <p class="text-sm text-on-surface-variant font-medium">Belum ada riwayat challenge</p>
        </div>
      {/if}
    </div>
  {/if}
</div>

<AppModal show={showAddForm} title="Tambah Challenge" onclose={closeForm}>
  <div class="space-y-4">
    <div class="mb-4">
      <label class="block text-sm font-semibold text-text-main mb-1.5">Kategori</label>
      <select bind:value={form.category}
        class="w-full px-4 py-3 rounded-xl border {errors.category ? 'border-error' : 'border-outline-variant'} bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors">
        <option value="">Pilih kategori</option>
        {#each Object.keys(kategoriChallenge) as key (key)}
          <option value={key}>{key}</option>
        {/each}
      </select>
      {#if errors.category}
        <p class="text-xs text-error mt-1">{errors.category}</p>
      {/if}
    </div>
    <AppInput bind:value={form.title} label="Nama Challenge" placeholder="Contoh: Perkalian 1-10" error={errors.title} />
    <div class="mb-4">
      <label class="block text-sm font-semibold text-text-main mb-1.5">Catatan</label>
      <textarea bind:value={form.notes} rows="2" placeholder="Catatan tambahan..."
        class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors resize-none"></textarea>
    </div>
    <AppInput bind:value={form.maxPoints} label="Target Poin" type="number" placeholder="10" error={errors.maxPoints} />
  </div>
  <div class="flex gap-3 mt-6">
    <button onclick={closeForm}
      class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray">Batal</button>
    <button onclick={saveForm}
      class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green">Simpan</button>
  </div>
</AppModal>

<AppModal show={showEditForm} title="Edit Challenge" onclose={closeEdit}>
  <div class="space-y-4">
    <div class="mb-4">
      <label class="block text-sm font-semibold text-text-main mb-1.5">Kategori</label>
      <select bind:value={editFormState.category}
        class="w-full px-4 py-3 rounded-xl border {errors.category ? 'border-error' : 'border-outline-variant'} bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors">
        <option value="">Pilih kategori</option>
        {#each Object.keys(kategoriChallenge) as key (key)}
          <option value={key}>{key}</option>
        {/each}
      </select>
      {#if errors.category}
        <p class="text-xs text-error mt-1">{errors.category}</p>
      {/if}
    </div>
    <AppInput bind:value={editFormState.title} label="Nama Challenge" placeholder="Contoh: Perkalian 1-10" error={errors.title} />
    <div class="mb-4">
      <label class="block text-sm font-semibold text-text-main mb-1.5">Catatan</label>
      <textarea bind:value={editFormState.notes} rows="2" placeholder="Catatan tambahan..."
        class="w-full px-4 py-3 rounded-xl border border-outline-variant bg-white text-text-main focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-colors resize-none"></textarea>
    </div>
    <AppInput bind:value={editFormState.maxPoints} label="Target Poin" type="number" placeholder="10" error={errors.maxPoints} />
  </div>
  <div class="flex gap-3 mt-6">
    <button onclick={closeEdit}
      class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray">Batal</button>
    <button onclick={saveEdit}
      class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green">Simpan</button>
  </div>
</AppModal>

<style>
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 4px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(4px);
    box-shadow: 0 0px 0 #176c33;
  }
  .btn-pop-gray {
    background-color: #E5E5E5;
    box-shadow: 0 4px 0 #B0B0B0;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(4px);
    box-shadow: 0 0px 0 #B0B0B0;
  }
</style>
