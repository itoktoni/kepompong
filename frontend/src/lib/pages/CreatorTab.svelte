<script>
  import { get } from 'svelte/store'
  import { isAuthenticated } from '../stores/authStore.js'
  import * as api from '../services/api.js'

  let isAuth = $state(false)
  let myAddons = $state([])
  let loading = $state(false)
  let selectedAddon = $state(null)
  let addonWorksheets = $state([])
  let addonActivities = $state([])
  let showAddonForm = $state(false)
  let showWorksheetForm = $state(false)
  let showActivityForm = $state(false)

  let addonForm = $state({
    nama: '', desc: '', harga: 0, ages: [], agama: [], bg: '#E8F5E9', icon: '📦',
  })

  let wsForm = $state({
    key: '', title: '', desc: '', ages: [], agama: [], bg: '#E8F5E9', icon: '📝',
  })
  let wsFile = $state(null)

  let actForm = $state({
    type: 'storytelling', title: '', desc: '', image: '', moral: '', ages: [],
  })

  $effect(() => {
    const u = isAuthenticated.subscribe(v => isAuth = v)
    return () => u()
  })

  $effect(() => { if (isAuth) loadMyAddons() })

  async function loadMyAddons() {
    loading = true
    try { myAddons = await api.getMyAddons().catch(() => []) } catch (_) {}
    loading = false
  }

  async function selectAddon(addon) {
    selectedAddon = addon
    showWorksheetForm = false
    showActivityForm = false
    try {
      const [ws, act] = await Promise.all([
        api.getAddonWorksheets(addon.id).catch(() => []),
        api.getAddonActivities(addon.id).catch(() => []),
      ])
      addonWorksheets = ws || []
      addonActivities = act || []
    } catch (_) {}
  }

  function backToList() {
    selectedAddon = null
    addonWorksheets = []
    addonActivities = []
  }

  function resetAddonForm() {
    addonForm = { nama: '', desc: '', harga: 0, ages: [], agama: [], bg: '#E8F5E9', icon: '📦' }
    showAddonForm = false
  }

  function resetWsForm() {
    wsForm = { key: '', title: '', desc: '', ages: [], agama: [], bg: '#E8F5E9', icon: '📝' }
    wsFile = null
    showWorksheetForm = false
  }

  function resetActForm() {
    actForm = { type: 'storytelling', title: '', desc: '', image: '', moral: '', ages: [] }
    showActivityForm = false
  }

  async function saveAddon() {
    if (!addonForm.nama) return
    try {
      await api.createAddon(addonForm)
      resetAddonForm()
      await loadMyAddons()
    } catch (e) { console.error('Create addon failed:', e) }
  }

  async function saveWorksheet() {
    if (!wsForm.title || !selectedAddon) return
    try {
      const fd = new FormData()
      if (wsForm.key) fd.append('key', wsForm.key)
      else fd.append('key', wsForm.title.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '') + '_' + Date.now().toString(36))
      fd.append('title', wsForm.title)
      if (wsForm.desc) fd.append('desc', wsForm.desc)
      if (wsForm.bg) fd.append('bg', wsForm.bg)
      if (wsForm.icon) fd.append('icon', wsForm.icon)
      wsForm.ages.forEach(a => fd.append('ages[]', JSON.stringify(a)))
      wsForm.agama.forEach(a => fd.append('agama[]', a))
      if (wsFile) fd.append('file', wsFile)
      await api.createAddonWorksheet(selectedAddon.id, fd)
      resetWsForm()
      await selectAddon(selectedAddon)
    } catch (e) { console.error('Create worksheet failed:', e) }
  }

  async function saveActivity() {
    if (!actForm.title || !selectedAddon) return
    try {
      await api.createAddonActivity(selectedAddon.id, actForm)
      resetActForm()
      await selectAddon(selectedAddon)
    } catch (e) { console.error('Create activity failed:', e) }
  }

  function toggleAge(target, value) {
    const key = JSON.stringify(value)
    const idx = target.findIndex(a => JSON.stringify(a) === key)
    if (idx >= 0) return target.filter((_, i) => i !== idx)
    return [...target, value]
  }

  const ageOptions = [
    { value: [1,2,3], label: '1-3' },
    { value: [3,4,5], label: '3-5' },
    { value: [4,5,6,7], label: '4-7' },
    { value: [6,7,8,9], label: '6-9' },
    { value: [7,8,9,10,11], label: '7+' },
  ]

  const bgOptions = ['#E3F2FD','#F3E5F5','#E8F5E9','#FFF3E0','#FCE4EC','#E0F2F1']
  const iconOptions = ['📦','📝','📚','🧮','🎨','🧠','🔬','🎵','✏️','🔢','🌟','🖍️','📖']
  const typeOptions = [
    { value: 'storytelling', label: '📖 Story Telling' },
    { value: 'bermain_peran', label: '🎭 Bermain Peran' },
    { value: 'permainan', label: '🎲 Permainan' },
    { value: 'puzzle', label: '🧩 Puzzle' },
    { value: 'monolog', label: '🎤 Monolog' },
    { value: 'proyek_kreatif', label: '🎨 Proyek Kreatif' },
    { value: 'musik_gerak', label: '🎵 Musik & Gerak' },
    { value: 'mindfulness', label: '🧘 Mindfulness' },
    { value: 'outdoor', label: '🌳 Outdoor' },
    { value: 'ilmu_pengetahuan', label: '🔬 Ilmu Pengetahuan' },
  ]

  function hasAge(target, value) {
    return target.some(a => JSON.stringify(a) === JSON.stringify(value))
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main leading-tight mb-2 flex items-center gap-2">
    <span class="w-10 h-10 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-xl">🏪</span> Creator
  </h2>
  <p class="font-body-md text-body-md text-on-surface-variant mb-5">Buat addon, tambahkan worksheet dan aktivitas.</p>

  {#if !selectedAddon}
    <!-- ADDON LIST -->
    <button onclick={() => { resetAddonForm(); showAddonForm = true }}
      class="mb-4 px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold transition-all active:scale-95">
      ➕ Buat Addon
    </button>

    {#if showAddonForm}
      <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] mb-4 space-y-3">
        <h3 class="font-bold text-sm text-text-main">Buat Addon Baru</h3>

        <div>
          <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Icon</label>
          <div class="flex flex-wrap gap-1.5">
            {#each iconOptions as ic}
              <button onclick={() => addonForm.icon = ic}
                class="w-8 h-8 rounded-lg flex items-center justify-center text-base border-2 transition-all"
                class:border-primary={addonForm.icon === ic}
                class:border-[#B7D9BC]={addonForm.icon !== ic}
                class:bg-success-soft={addonForm.icon === ic}>{ic}</button>
            {/each}
          </div>
        </div>

        <div>
          <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Warna</label>
          <div class="flex flex-wrap gap-1.5">
            {#each bgOptions as c}
              <button onclick={() => addonForm.bg = c}
                class="w-7 h-7 rounded-lg border-2 transition-all"
                style="background: {c}"
                class:border-primary={addonForm.bg === c}
                class:border-[#B7D9BC]={addonForm.bg !== c}></button>
            {/each}
          </div>
        </div>

        <input type="text" bind:value={addonForm.nama} placeholder="Nama addon (contoh: Kelas Berhitung)"
          class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs" />

        <textarea bind:value={addonForm.desc} placeholder="Deskripsi addon..." rows="2"
          class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs"></textarea>

        <div>
          <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Harga (Rp) — 0 = gratis</label>
          <input type="number" bind:value={addonForm.harga} min="0"
            class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs" />
        </div>

        <div>
          <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Usia</label>
          <div class="flex flex-wrap gap-1.5">
            {#each ageOptions as opt}
              <button onclick={() => addonForm.ages = toggleAge(addonForm.ages, opt.value)}
                class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all"
                class:bg-primary={hasAge(addonForm.ages, opt.value)}
                class:text-white={hasAge(addonForm.ages, opt.value)}
                class:border-primary={hasAge(addonForm.ages, opt.value)}
                class:border-[#B7D9BC]={!hasAge(addonForm.ages, opt.value)}>{opt.label}</button>
            {/each}
          </div>
        </div>

        <div class="flex gap-2 pt-1">
          <button onclick={saveAddon} class="flex-1 py-2.5 rounded-xl bg-primary text-white text-xs font-bold">💾 Simpan</button>
          <button onclick={resetAddonForm} class="px-4 py-2.5 rounded-xl bg-white border-2 border-[#B7D9BC] text-xs font-bold">Batal</button>
        </div>
      </div>
    {/if}

    {#if myAddons.length === 0 && !showAddonForm}
      <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
        <div class="text-5xl mb-3">📦</div>
        <p class="font-label-lg text-text-main mb-1">Belum Punya Addon</p>
        <p class="text-sm text-on-surface-variant">Buat addon untuk mulai menambahkan worksheet dan aktivitas.</p>
      </div>
    {:else}
      <div class="space-y-3">
        {#each myAddons as addon}
          <button onclick={() => selectAddon(addon)}
            class="w-full bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] hover:border-primary transition-all text-left">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0" style="background: {addon.bg || '#E8F5E9'}">
                {addon.icon || '📦'}
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-bold text-sm text-text-main">{addon.nama}</h3>
                <div class="flex gap-2 mt-1">
                  <span class="text-[10px] px-2 py-0.5 rounded-full bg-success-soft text-primary font-bold">📄 {addon.activity_count} konten</span>
                  <span class="text-[10px] px-2 py-0.5 rounded-full bg-success-soft text-primary font-bold">👥 {addon.buyer_count} pembeli</span>
                  <span class="text-[10px] px-2 py-0.5 rounded-full bg-white border border-[#B7D9BC] font-bold">
                    {addon.harga ? 'Rp ' + addon.harga.toLocaleString() : 'Gratis'}
                  </span>
                </div>
              </div>
              <span class="text-xl text-on-surface-variant">→</span>
            </div>
          </button>
        {/each}
      </div>
    {/if}

  {:else}
    <!-- ADDON DETAIL -->
    <button onclick={backToList}
      class="flex items-center gap-2 text-primary font-label-lg mb-4 hover:opacity-80 bg-success-soft px-4 py-2 rounded-full border-2 border-[#B7D9BC]">
      <span class="text-xl">⬅</span> Kembali
    </button>

    <div class="flex items-center gap-3 mb-5">
      <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0" style="background: {selectedAddon.bg || '#E8F5E9'}">
        {selectedAddon.icon || '📦'}
      </div>
      <div>
        <h3 class="font-bold text-base text-text-main">{selectedAddon.nama}</h3>
        <p class="text-xs text-on-surface-variant">{selectedAddon.harga ? 'Rp ' + selectedAddon.harga.toLocaleString() : 'Gratis'}</p>
      </div>
    </div>

    <!-- WORKSHEETS -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-sm text-text-main flex items-center gap-2">📝 Worksheet <span class="text-xs font-normal text-on-surface-variant">({addonWorksheets.length})</span></h3>
        <button onclick={() => { resetWsForm(); showWorksheetForm = true }}
          class="px-3 py-1.5 rounded-lg bg-primary text-white text-[11px] font-bold">➕ Tambah</button>
      </div>

      {#if showWorksheetForm}
        <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] mb-3 space-y-3">
          <h4 class="font-bold text-xs text-text-main">Buat Worksheet Baru</h4>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Icon</label>
            <div class="flex flex-wrap gap-1.5">
              {#each iconOptions.filter(i => i !== '📦') as ic}
                <button onclick={() => wsForm.icon = ic}
                  class="w-8 h-8 rounded-lg flex items-center justify-center text-base border-2 transition-all"
                  class:border-primary={wsForm.icon === ic}
                  class:border-[#B7D9BC]={wsForm.icon !== ic}
                  class:bg-success-soft={wsForm.icon === ic}>{ic}</button>
              {/each}
            </div>
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Warna</label>
            <div class="flex flex-wrap gap-1.5">
              {#each bgOptions as c}
                <button onclick={() => wsForm.bg = c}
                  class="w-7 h-7 rounded-lg border-2 transition-all"
                  style="background: {c}"
                  class:border-primary={wsForm.bg === c}
                  class:border-[#B7D9BC]={wsForm.bg !== c}></button>
              {/each}
            </div>
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Nama Worksheet</label>
            <input type="text" bind:value={wsForm.title} placeholder="Contoh: Mewarnai Buah"
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs" />
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Deskripsi</label>
            <textarea bind:value={wsForm.desc} placeholder="Deskripsi worksheet..." rows="2"
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs"></textarea>
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Usia</label>
            <div class="flex flex-wrap gap-1.5">
              {#each ageOptions as opt}
                <button onclick={() => wsForm.ages = toggleAge(wsForm.ages, opt.value)}
                  class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all"
                  class:bg-primary={hasAge(wsForm.ages, opt.value)}
                  class:text-white={hasAge(wsForm.ages, opt.value)}
                  class:border-primary={hasAge(wsForm.ages, opt.value)}
                  class:border-[#B7D9BC]={!hasAge(wsForm.ages, opt.value)}>{opt.label}</button>
              {/each}
            </div>
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Upload PDF</label>
            <label class="flex items-center justify-center gap-2 w-full py-3 rounded-xl border-2 border-dashed border-[#B7D9BC] hover:border-primary cursor-pointer transition-all bg-white text-xs text-on-surface-variant">
              {#if wsFile}
                <span>📄 {wsFile.name}</span>
              {:else}
                <span>⬆️ Pilih file PDF</span>
              {/if}
              <input type="file" accept=".pdf" class="hidden" onchange={(e) => wsFile = e.target.files?.[0] || null} />
            </label>
          </div>

          <div class="flex gap-2 pt-1">
            <button onclick={saveWorksheet} class="flex-1 py-2 rounded-xl bg-primary text-white text-xs font-bold">💾 Simpan</button>
            <button onclick={resetWsForm} class="px-3 py-2 rounded-xl bg-white border-2 border-[#B7D9BC] text-xs font-bold">Batal</button>
          </div>
        </div>
      {/if}

      {#if addonWorksheets.length === 0 && !showWorksheetForm}
        <p class="text-xs text-on-surface-variant bg-canvas-cream rounded-xl p-3 border border-[#B7D9BC]/50">Belum ada worksheet.</p>
      {:else}
        <div class="grid grid-cols-2 gap-2">
          {#each addonWorksheets as ws}
            <div class="bg-canvas-cream rounded-xl p-3 border-2 border-[#B7D9BC]">
              <div class="flex items-center gap-2 mb-1">
                <span class="text-lg">{ws.worksheet_icon || '📝'}</span>
                <h4 class="font-bold text-xs text-text-main line-clamp-1">{ws.worksheet_title}</h4>
              </div>
              {#if ws.worksheet_desc}
                <p class="text-[10px] text-on-surface-variant line-clamp-2">{ws.worksheet_desc}</p>
              {/if}
            </div>
          {/each}
        </div>
      {/if}
    </div>

    <!-- ACTIVITIES -->
    <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-sm text-text-main flex items-center gap-2">🎯 Aktivitas <span class="text-xs font-normal text-on-surface-variant">({addonActivities.length})</span></h3>
        <button onclick={() => { resetActForm(); showActivityForm = true }}
          class="px-3 py-1.5 rounded-lg bg-primary text-white text-[11px] font-bold">➕ Tambah</button>
      </div>

      {#if showActivityForm}
        <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] mb-3 space-y-3">
          <h4 class="font-bold text-xs text-text-main">Buat Aktivitas Baru</h4>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Tipe</label>
            <select bind:value={actForm.type}
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs">
              {#each typeOptions as t}
                <option value={t.value}>{t.label}</option>
              {/each}
            </select>
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Judul</label>
            <input type="text" bind:value={actForm.title} placeholder="Judul aktivitas"
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs" />
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Deskripsi</label>
            <textarea bind:value={actForm.desc} placeholder="Deskripsi aktivitas..." rows="2"
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs"></textarea>
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">URL Gambar</label>
            <input type="text" bind:value={actForm.image} placeholder="https://..."
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs" />
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Pelajaran / Moral</label>
            <input type="text" bind:value={actForm.moral} placeholder="Pelajaran dari aktivitas ini..."
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-xs" />
          </div>

          <div>
            <label class="text-[10px] font-bold text-on-surface-variant mb-1 block">Usia</label>
            <div class="flex flex-wrap gap-1.5">
              {#each ageOptions as opt}
                <button onclick={() => actForm.ages = toggleAge(actForm.ages, opt.value)}
                  class="px-2.5 py-1 rounded-lg text-[10px] font-bold border transition-all"
                  class:bg-primary={hasAge(actForm.ages, opt.value)}
                  class:text-white={hasAge(actForm.ages, opt.value)}
                  class:border-primary={hasAge(actForm.ages, opt.value)}
                  class:border-[#B7D9BC]={!hasAge(actForm.ages, opt.value)}>{opt.label}</button>
              {/each}
            </div>
          </div>

          <div class="flex gap-2 pt-1">
            <button onclick={saveActivity} class="flex-1 py-2 rounded-xl bg-primary text-white text-xs font-bold">💾 Simpan</button>
            <button onclick={resetActForm} class="px-3 py-2 rounded-xl bg-white border-2 border-[#B7D9BC] text-xs font-bold">Batal</button>
          </div>
        </div>
      {/if}

      {#if addonActivities.length === 0 && !showActivityForm}
        <p class="text-xs text-on-surface-variant bg-canvas-cream rounded-xl p-3 border border-[#B7D9BC]/50">Belum ada aktivitas.</p>
      {:else}
        <div class="space-y-2">
          {#each addonActivities as act}
            <div class="bg-canvas-cream rounded-xl p-3 border-2 border-[#B7D9BC] flex items-center gap-3">
              {#if act.image}
                <img src={act.image} alt="" class="w-10 h-10 rounded-lg object-cover shrink-0" />
              {:else}
                <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-lg shrink-0">📄</div>
              {/if}
              <div class="flex-1 min-w-0">
                <h4 class="font-bold text-xs text-text-main line-clamp-1">{act.title}</h4>
                <span class="text-[10px] text-on-surface-variant">{act.type}</span>
              </div>
              <span class="text-[10px] px-2 py-0.5 rounded-full font-bold"
                class:bg-green-100={act.status === 'approved'}
                class:text-green-700={act.status === 'approved'}
                class:bg-yellow-100={act.status === 'pending'}
                class:text-yellow-700={act.status === 'pending'}>
                {act.status}
              </span>
            </div>
          {/each}
        </div>
      {/if}
    </div>
  {/if}
</div>
