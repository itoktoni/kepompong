<script>
  import { get } from 'svelte/store'
  import { aktivitasData, buildAktivitasDataFromAPI, setAktivitasData, filterActivities, initializeActivitiesFromCache } from '../data/activities.js'
  import { activitiesCache, serverCount, localCount, downloading, downloadMessage, downloadActivities } from '../stores/activityStore.js'
  import { isAuthenticated, userRole, userPlan, plans as planList } from '../stores/authStore.js'
  import { switchCounter, activeTab, selectedAnakId, selectedSkillKey, selectedAge, selectedAgama, selectedPlanId } from '../stores/appStore.js'
  import { syncActivitiesByType } from '../services/api.js'
  import { resolveActivityCoverImage } from '../utils/images.js'
  import { anakList } from '../stores/anakStore.js'
  import { calcAge } from '../utils/age.js'
  import AnakDropdown from '../components/AnakDropdown.svelte'
  import { StoryCard, RoleplayCard, GameCard, ScriptCard, ProjectCard, SongCard, PuzzleCard, ExerciseCard, OutdoorCard, ExperimentCard, WorksheetCard, GuessCard, HandGameCard, BrainTrainCard, ComicCard } from './activity/index.js'
  import { openWorksheetByType, hasWorksheetTemplate } from '../utils/worksheetRenderer.js'
  import { saveActivitiesByType } from '../db.js'
  import DevPanel from '../components/DevPanel.svelte'

  const cardMap = {
    storytelling: StoryCard,
    bermain_peran: RoleplayCard,
    permainan: GameCard,
    monolog: ScriptCard,
    proyek_kreatif: ProjectCard,
    musik_gerak: SongCard,
    puzzle: PuzzleCard,
    mindfulness: ExerciseCard,
    outdoor: OutdoorCard,
    ilmu_pengetahuan: ExperimentCard,
    worksheet: WorksheetCard,
    tebak_teakan: GuessCard,
    permainan_tangan: HandGameCard,
    latihan_otak: BrainTrainCard,
    komik: ComicCard,
  }

  let aktData = $state([])
  let isAuth = $state(false)
  let dl = $state(false)
  let dlMsg = $state('')
  let srvCount = $state(0)
  let locCount = $state(0)
  let selectedType = $state(null)
  let searchQuery = $state('')
  let detailSearchQuery = $state('')
  let activeItem = $state(null)
  let puzzleQIndex = $state(0)
  let puzzleShowHint = $state(false)
  let puzzleShowAnswer = $state(false)
  let puzzleScore = $state({ correct: 0, wrong: 0 })
  let switchCount = $state(0)
  let anakListVal = $state([])
  let selectedAnakIdVal = $state(null)
  let devPanel = $state(null)

  const statusColors = {
    approved: { bg: '#E1F2E5', text: '#176c33', label: 'Approved' },
    pending: { bg: '#FFF3E0', text: '#E65100', label: 'Pending' },
    rejected: { bg: '#FFEBEE', text: '#C62828', label: 'Rejected' },
  }
  let selectedSkillKeyVal = $state(null)
  let selectedAgeVal = $state(null)
  let selectedAgamaVal = $state(null)
  let selectedPlanIdVal = $state(null)
  let activeTabVal = $state('')
  let userPlanVal = $state(null)
  let planListVal = $state([])
  let userRoleVal = $state('')
  let initialized = $state(false)

  // Initialize activities from local cache on mount for offline support
  $effect(() => {
    if (!initialized) {
      initialized = true
      initializeActivitiesFromCache()
    }
  })

  $effect(() => {
    const u1 = aktivitasData.subscribe(v => aktData = v)
    const u2 = isAuthenticated.subscribe(v => isAuth = v)
    const u3 = downloading.subscribe(v => dl = v)
    const u4 = downloadMessage.subscribe(v => dlMsg = v)
    const u5 = serverCount.subscribe(v => srvCount = v)
    const u6 = localCount.subscribe(v => locCount = v)
    const u7 = switchCounter.subscribe(v => switchCount = v)
    const u8 = anakList.subscribe(v => anakListVal = v)
    const u9 = selectedAnakId.subscribe(v => selectedAnakIdVal = v)
    const u10 = selectedSkillKey.subscribe(v => selectedSkillKeyVal = v)
    const u11 = selectedAge.subscribe(v => selectedAgeVal = v)
    const u12 = selectedAgama.subscribe(v => selectedAgamaVal = v)
    const u13 = selectedPlanId.subscribe(v => selectedPlanIdVal = v)
    const u14 = activeTab.subscribe(v => activeTabVal = v)
    return () => { u1(); u2(); u3(); u4(); u5(); u6(); u7(); u8(); u9(); u10(); u11(); u12(); u13(); u14() }
  })

  $effect(() => {
    if (switchCount > 0 && activeTabVal === 'activity') {
      selectedType = null
      activeItem = null
      detailSearchQuery = ''
    }
  })

  const contentKeyMap = {
    storytelling: 'stories', bermain_peran: 'roles', permainan: 'games',
    monolog: 'scripts', proyek_kreatif: 'projects', musik_gerak: 'songs',
    puzzle: 'puzzles', mindfulness: 'exercises', outdoor: 'activities',
    ilmu_pengetahuan: 'experiments', worksheet: 'worksheets',
    tebak_teakan: 'guesses', permainan_tangan: 'handgames', latihan_otak: 'braintrains',
    komik: 'comics'
  }

  const selectedChild = $derived(anakListVal.find(a => a.id === selectedAnakIdVal))
  const childAge = $derived(selectedChild?.umur ? Number(selectedChild.umur) : (selectedChild?.tahun ? new Date().getFullYear() - Number(selectedChild.tahun) : null))
  const childAgama = $derived(selectedChild?.agama || null)

  $effect(() => {
    if (childAge != null) selectedAge.set(childAge)
    else selectedAge.set(null)
  })

  $effect(() => {
    if (childAgama) selectedAgama.set(childAgama)
    else selectedAgama.set(null)
  })

  $effect(() => {
    const u14 = userPlan.subscribe(v => userPlanVal = v)
    const u15 = planList.subscribe(v => planListVal = v)
    const u16 = userRole.subscribe(v => userRoleVal = v)
    return () => { u14(); u15(); u16() }
  })

  $effect(() => {
    if (userPlanVal?.plan_id) selectedPlanId.set(userPlanVal.plan_id)
    else selectedPlanId.set(null)
  })

  const planName = $derived(() => {
    if (!selectedPlanIdVal) return null
    const found = planListVal.find(p => p.id === selectedPlanIdVal)
    return found?.name || null
  })

  const filteredAktData = $derived.by(() => {
    const data = aktData
    if (!data || !data.length) return []

    let result = data

    if (selectedAnakIdVal) {
      result = result.map(a => {
        const contentKey = contentKeyMap[a.key]
        const items = (a[contentKey] || []).filter(item => {
          const ageOk = selectedAgeVal == null || (item.ages && item.ages.some(a => Number(a) === Number(selectedAgeVal)))
          const agamaOk = !selectedAgamaVal || !item.agama || !item.agama.length || item.agama.includes(selectedAgamaVal)
          const skillOk = !selectedSkillKeyVal || !item.skills || !item.skills.length || item.skills.includes(selectedSkillKeyVal)
          const planOk = !selectedPlanIdVal || !item.plans || !item.plans.length || item.plans.includes(selectedPlanIdVal)
          return ageOk && agamaOk && skillOk && planOk
        })
        return { ...a, [contentKey]: items }
      })

      const hasFilter = selectedAgeVal != null || selectedAgamaVal || selectedSkillKeyVal || selectedPlanIdVal
      if (hasFilter) {
        result = result.filter(a => {
          if (a.key === 'worksheet') return true
          const contentKey = contentKeyMap[a.key]
          return (a[contentKey] || []).length > 0
        })
      }
    }

    if (searchQuery) {
      const q = searchQuery.toLowerCase()
      result = result.filter(a => {
        if (a.title?.toLowerCase().includes(q) || a.desc?.toLowerCase().includes(q)) return true
        if (a.key === 'worksheet') return true
        const contentKey = contentKeyMap[a.key]
        return (a[contentKey] || []).some(item =>
          item.title?.toLowerCase().includes(q) || item.desc?.toLowerCase().includes(q)
        )
      }).map(a => {
        if (a.title?.toLowerCase().includes(q) || a.desc?.toLowerCase().includes(q)) return a
        if (a.key === 'worksheet') return a
        const contentKey = contentKeyMap[a.key]
        const items = (a[contentKey] || []).filter(item =>
          item.title?.toLowerCase().includes(q) || item.desc?.toLowerCase().includes(q)
        )
        return { ...a, [contentKey]: items }
      })
    }

    return result
  })

  function getItems(type) {
    return type[contentKeyMap[type.key]] || []
  }

  function getItemCount(type) {
    return getItems(type).length
  }

  const sortedItems = $derived.by(() => {
    if (!selectedType) return []
    const items = getItems(selectedType)
    let result = [...items].sort((a, b) => (a.title || '').localeCompare(b.title || ''))
    if (detailSearchQuery) {
      const q = detailSearchQuery.toLowerCase()
      result = result.filter(item =>
        item.title?.toLowerCase().includes(q) ||
        item.desc?.toLowerCase().includes(q)
      )
    }
    return result
  })

  async function doDownload() {
    await downloadActivities()
    const cache = get(activitiesCache)
    if (cache) {
      const aktivitas = buildAktivitasDataFromAPI(cache)
      setAktivitasData(aktivitas)
      aktData = get(aktivitasData)
      if (selectedType) {
        const refreshed = aktData.find(a => a.key === selectedType.key)
        if (refreshed) selectedType = refreshed
      }
    }
  }

  let typeSyncing = $state(false)

  async function syncByType() {
    if (!selectedType || typeSyncing) return
    typeSyncing = true
    try {
      const data = await syncActivitiesByType(selectedType.key)
      if (data && data[selectedType.key]) {
        const items = data[selectedType.key]
        await saveActivitiesByType(selectedType.key, items)

        const latestData = get(aktivitasData)
        const contentKey = contentKeyMap[selectedType.key]
        const updatedData = latestData.map(a => {
          if (a.key === selectedType.key) {
            return { ...a, [contentKey]: items }
          }
          return a
        })
        setAktivitasData(updatedData)
        aktData = get(aktivitasData)
        const updatedItem = updatedData.find(a => a.key === selectedType.key)
        if (updatedItem) selectedType = updatedItem
      }
    } catch (e) { /* ignore */ }
    typeSyncing = false
  }

  function handleCategoryClick(item) {
    selectedType = item
  }

  function handleItemClick(item) {
    activeItem = item
    puzzleQIndex = 0
    puzzleShowHint = false
    puzzleShowAnswer = false
    puzzleScore = { correct: 0, wrong: 0 }
    if (devPanel) devPanel.initStatus()
  }

  function puzzleAnswer(isCorrect) {
    if (isCorrect) puzzleScore.correct++
    else puzzleScore.wrong++
    const questions = activeItem?.questions || []
    if (puzzleQIndex < questions.length - 1) {
      puzzleQIndex++
      puzzleShowHint = false
      puzzleShowAnswer = false
    } else {
      puzzleShowAnswer = true
    }
  }

  function goBack() {
    if (activeItem) { activeItem = null; return }
    if (selectedType) { selectedType = null; detailSearchQuery = ''; return }
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  {#if !selectedType}
    <section class="mb-stack-lg">
      <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main leading-tight mb-2 flex items-center gap-2">
        <span class="w-10 h-10 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-xl">🎨</span> Semua Aktivitas
      </h2>
      <p class="font-body-md text-body-md text-on-surface-variant mb-3">
        Pilih jenis aktivitas untuk melihat seluruh konten.
        {#if isAuth}
          <span class="text-xs text-on-surface-variant/70">Download dari server untuk mendapatkan konten terbaru.</span>
        {/if}
      </p>
      <div class="flex items-center gap-2 mb-3">
        <div class="flex-1 min-w-0">
          <AnakDropdown anakList={anakListVal} value={selectedAnakIdVal} onselect={(id) => selectedAnakId.set(id)} />
        </div>
        {#if isAuth}
          <button onclick={doDownload} disabled={dl}
            class="flex items-center gap-2 py-3 rounded-2xl text-sm text-white shrink-0 transition-all active:scale-95 soft-shadow border-2 border-white bg-primary px-4 lg:px-5">
            <span class="material-symbols-outlined text-lg" class:animate-spin={dl}>cloud_download</span>
            <span class="hidden lg:inline">{dl ? '...' : 'Download Content'}</span>
          </button>
        {/if}
      </div>
      <div class="relative mt-3">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
        <input
          type="text"
          placeholder="Cari aktivitas..."
          bind:value={searchQuery}
          class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-sm"
        />
      </div>
      {#if selectedAgeVal != null || selectedAgamaVal || selectedSkillKeyVal || selectedPlanIdVal}
        <div class="mt-3">
          <div class="flex items-center justify-between mb-2">
            <p class="text-xs font-bold text-primary uppercase tracking-wider">Filter Aktif</p>
            {#if userRoleVal === 'developer'}
              <button onclick={() => { selectedAge.set(null); selectedAgama.set(null); selectedSkillKey.set(null); selectedPlanId.set(null) }}
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold text-error hover:bg-error/10 transition-colors">
                <span class="material-symbols-outlined text-sm">close</span>
                Hapus Semua
              </button>
            {/if}
          </div>
          <div class="bg-white rounded-2xl p-3 border-2 border-[#B7D9BC] flex flex-wrap gap-2">
            {#if selectedAgeVal != null}
              <button onclick={() => userRoleVal === 'developer' && selectedAge.set(null)}
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 {userRoleVal === 'developer' ? 'hover:bg-primary/10 cursor-pointer' : 'cursor-default'}">
                <span class="material-symbols-outlined text-sm">cake</span>
                Umur {selectedAgeVal} th
                {#if userRoleVal === 'developer'}
                  <span class="material-symbols-outlined text-sm text-primary/60">close</span>
                {/if}
              </button>
            {/if}
            {#if selectedAgamaVal}
              <button onclick={() => userRoleVal === 'developer' && selectedAgama.set(null)}
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 {userRoleVal === 'developer' ? 'hover:bg-primary/10 cursor-pointer' : 'cursor-default'}">
                <span class="material-symbols-outlined text-sm">diversity_3</span>
                {selectedAgamaVal}
                {#if userRoleVal === 'developer'}
                  <span class="material-symbols-outlined text-sm text-primary/60">close</span>
                {/if}
              </button>
            {/if}
            {#if selectedSkillKeyVal}
              <button onclick={() => selectedSkillKey.set(null)}
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 hover:bg-primary/10 cursor-pointer">
                <span class="material-symbols-outlined text-sm">psychology</span>
                {selectedSkillKeyVal.replace(/_/g, ' ')}
                <span class="material-symbols-outlined text-sm text-primary/60">close</span>
              </button>
            {/if}
            {#if selectedPlanIdVal}
              <button onclick={() => userRoleVal === 'developer' && selectedPlanId.set(null)}
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 {userRoleVal === 'developer' ? 'hover:bg-primary/10 cursor-pointer' : 'cursor-default'}">
                <span class="material-symbols-outlined text-sm">workspace_premium</span>
                {planName() || 'Plan'}
                {#if userRoleVal === 'developer'}
                  <span class="material-symbols-outlined text-sm text-primary/60">close</span>
                {/if}
              </button>
            {/if}
          </div>
        </div>
      {/if}
    </section>

    <div class="grid grid-cols-2 gap-3">
      {#each filteredAktData as item (item.key)}
        <button
          class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden cursor-pointer transition-all hover:shadow-lg flex flex-col border-4 border-[#B7D9BC] shadow-md text-left"
          onclick={() => handleCategoryClick(item)}>
          <div class="p-4 flex flex-col flex-1">
            <div class="flex items-start justify-between mb-3">
              <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm"
                style="background: {item.bg}">
                {item.emoji}
              </div>
              <span class="text-xs font-bold px-2 py-1 rounded-full"
                style="background: {item.bg}; color: {item.color}">
                {getItemCount(item)}
              </span>
            </div>
            <h3 class="font-label-lg text-label-lg text-text-main mb-1">{item.title}</h3>
            <p class="text-xs leading-snug text-on-surface-variant line-clamp-2 mt-auto">{item.desc}</p>
          </div>
        </button>
      {/each}
    </div>
  {:else}
    <button onclick={() => { selectedType = null; detailSearchQuery = '' }}
      class="flex items-center gap-2 text-primary font-label-lg mb-stack-md hover:opacity-80 transition-opacity bg-success-soft px-4 py-2 rounded-full border-2 border-[#B7D9BC]">
      <span class="material-symbols-outlined text-xl">arrow_back</span>
      Kembali
    </button>

    <section class="mb-stack-lg">
      <div class="flex items-center gap-3 mb-2">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl border-2 border-white shadow-sm" style="background: {selectedType.bg}">{selectedType.emoji}</div>
        <div>
          <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main leading-tight">{selectedType.title}</h2>
          <p class="font-body-md text-body-md text-on-surface-variant">{sortedItems.length} aktivitas</p>
        </div>
      </div>

      {#if isAuth}
        <div class="bg-white rounded-xl p-3 border-2 border-[#B7D9BC] flex items-center gap-2 mt-2">
          <span class="material-symbols-outlined text-primary text-sm">sync</span>
          <p class="text-xs text-on-surface-variant flex-1">
            {sortedItems.length} aktivitas tersedia
          </p>
          <button onclick={syncByType} disabled={typeSyncing}
            class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-white"
            style="background: {typeSyncing ? '#999' : '#176c33'}">
            {typeSyncing ? '...' : 'Sync'}
          </button>
        </div>
      {/if}
    </section>

    <div class="relative mb-4">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
      <input
        type="text"
        placeholder="Cari {selectedType.title?.toLowerCase() || 'aktivitas'}..."
        bind:value={detailSearchQuery}
        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-sm"
      />
    </div>

    {#if sortedItems.length > 0}
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        {#each sortedItems as item (item.title)}
          {@const Card = cardMap[selectedType?.key]}
          {#if Card}
            <Card {item} bg={selectedType.bg} type={selectedType.key} onclick={() => handleItemClick(item)} />
          {:else}
            <button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
              style="border-color: {userRoleVal === 'developer' && item.status && item.status !== 'approved' ? (statusColors[item.status]?.text || '#E65100') + '80' : '#B7D9BC'}"
              onclick={() => handleItemClick(item)}>
              <div class="p-5 flex flex-col flex-1">
                <div class="flex items-start justify-between mb-3">
                  <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {selectedType.bg}">
                    {item.emoji || selectedType.emoji}
                  </div>
                  {#if userRoleVal === 'developer' && item.status && item.status !== 'approved'}
                    {@const sc = statusColors[item.status] || statusColors.pending}
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="background: {sc.bg}; color: {sc.text}">{sc.label}</span>
                  {/if}
                </div>
                <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
                {#if item.desc}
                  <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
                {/if}
                <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
                  <span class="material-symbols-outlined text-xl">chevron_right</span>
                  Lihat Detail
                  <span class="material-symbols-outlined text-xl ml-auto group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
              </div>
            </button>
          {/if}
        {/each}
      </div>
    {:else}
      <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
        <div class="text-5xl mb-3">📭</div>
        <p class="font-label-lg text-text-main mb-1">Belum Ada Konten</p>
        <p class="text-sm text-on-surface-variant">Download aktivitas dari server terlebih dahulu.</p>
      </div>
    {/if}
  {/if}

  {#if dlMsg}
    <p class="text-xs text-primary mt-4 text-center font-medium">{dlMsg}</p>
  {/if}
</div>

<!-- Activity Detail Modal -->
{#if activeItem}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center lg:p-4" onclick={() => activeItem = null}>
    <div class="w-full max-w-md bg-canvas-cream rounded-t-[32px] lg:rounded-[32px] shadow-2xl border-4 border-[#B7D9BC] overflow-hidden max-h-[85vh] flex flex-col relative">
      <div class="relative p-5 flex items-center justify-between border-b-2 border-[#B7D9BC]/50 shrink-0 z-10">
        <div class="flex-1 min-w-0 mr-3">
          <h3 class="font-bold text-lg text-text-main truncate">{activeItem.title}</h3>
          {#if userRoleVal === 'developer' && activeItem.status}
            {@const sc = statusColors[activeItem.status] || statusColors.pending}
            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full mt-1" style="background: {sc.bg}; color: {sc.text}">{sc.label}</span>
          {/if}
        </div>
        {#if userRoleVal === 'developer'}
          <DevPanel bind:this={devPanel} item={activeItem} />
        {/if}
        <button onclick={() => activeItem = null}
          class="w-10 h-10 rounded-full bg-error text-white flex items-center justify-center text-lg shrink-0 shadow-md">
          ✕
        </button>
      </div>
      <div class="flex-1 overflow-y-auto p-5 space-y-4">

        {#if activeItem.questions?.length}
          {@const questions = activeItem.questions}
          {@const q = questions[puzzleQIndex]}
          {@const isLast = puzzleQIndex >= questions.length - 1 && puzzleShowAnswer}

          {#if !isLast}
            <div class="text-center">
              <span class="text-xs font-bold px-3 py-1 rounded-full bg-primary text-white">
                Soal {puzzleQIndex + 1} / {questions.length}
              </span>
              {#if puzzleScore.correct > 0 || puzzleScore.wrong > 0}
                <div class="flex justify-center gap-3 mt-2">
                  <span class="text-xs font-bold text-green-600">✅ {puzzleScore.correct}</span>
                  <span class="text-xs font-bold text-error">❌ {puzzleScore.wrong}</span>
                </div>
              {/if}
            </div>

            <div class="bg-white rounded-2xl p-5 border-2 border-[#B7D9BC] text-center">
              {#if q.emoji}
                <div class="text-5xl mb-3">{q.emoji}</div>
              {/if}
              <p class="text-base font-semibold text-text-main leading-relaxed">{q.q || q.clue}</p>
            </div>

            {#if puzzleShowHint && q.hint}
              <div class="bg-success-soft rounded-2xl p-4 border border-[#B7D9BC]/50">
                <p class="text-sm text-primary">
                  <span class="font-bold">💡 Petunjuk:</span> {q.hint}
                </p>
              </div>
            {/if}

            {#if puzzleShowAnswer}
              <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
                <p class="text-sm text-text-main"><span class="font-bold text-primary">Jawaban:</span> {q.a || q.answer}</p>
              </div>

              <p class="text-xs text-center text-on-surface-variant">Apakah jawabanmu benar?</p>
              <div class="flex gap-3">
                <button onclick={() => puzzleAnswer(true)}
                  class="flex-1 py-3 rounded-2xl bg-green-500 text-white font-bold text-sm shadow-md hover:bg-green-600 transition-colors">
                  ✅ Benar
                </button>
                <button onclick={() => puzzleAnswer(false)}
                  class="flex-1 py-3 rounded-2xl bg-error text-white font-bold text-sm shadow-md hover:bg-red-600 transition-colors">
                  ❌ Salah
                </button>
              </div>
            {:else}
              <div class="flex gap-3">
                {#if !puzzleShowHint && q.hint}
                  <button onclick={() => puzzleShowHint = true}
                    class="flex-1 py-3 rounded-2xl bg-white border-2 border-[#B7D9BC] text-primary font-bold text-sm shadow-sm hover:bg-success-soft transition-colors">
                    💡 Petunjuk
                  </button>
                {/if}
                <button onclick={() => puzzleShowAnswer = true}
                  class="flex-1 py-3 rounded-2xl bg-primary text-white font-bold text-sm shadow-md hover:bg-primary/90 transition-colors">
                  👁️ Jawaban
                </button>
              </div>
            {/if}

          {:else}
            <div class="text-center py-6 space-y-4">
              <div class="text-6xl">🎉</div>
              <h3 class="font-bold text-xl text-text-main">Selesai!</h3>
              <div class="flex justify-center gap-6">
                <div class="bg-green-50 rounded-2xl px-6 py-4 border-2 border-green-200">
                  <p class="text-3xl font-bold text-green-600">{puzzleScore.correct}</p>
                  <p class="text-xs text-green-600 font-bold">Benar</p>
                </div>
                <div class="bg-red-50 rounded-2xl px-6 py-4 border-2 border-red-200">
                  <p class="text-3xl font-bold text-error">{puzzleScore.wrong}</p>
                  <p class="text-xs text-error font-bold">Salah</p>
                </div>
              </div>
              <button onclick={() => { puzzleQIndex = 0; puzzleShowHint = false; puzzleShowAnswer = false; puzzleScore = { correct: 0, wrong: 0 } }}
                class="px-6 py-3 rounded-2xl bg-primary text-white font-bold text-sm shadow-md">
                🔄 Main Lagi
              </button>
            </div>
          {/if}

        {:else if activeItem.id && hasWorksheetTemplate(activeItem.id)}

          <div class="text-center py-4 space-y-4">
            <div class="text-6xl">{activeItem.emoji || '📝'}</div>
            <h3 class="font-bold text-xl text-text-main">{activeItem.title}</h3>
            {#if activeItem.desc}
              <p class="text-sm text-on-surface-variant">{activeItem.desc}</p>
            {/if}
            {#if activeItem.ageLabel}
              <span class="inline-block text-xs font-bold px-3 py-1.5 rounded-full bg-success-soft text-primary border border-[#B7D9BC]">
                Usia {activeItem.ageLabel}
              </span>
            {/if}
            <button onclick={() => openWorksheetByType(activeItem.id)}
              class="w-full py-4 rounded-2xl bg-primary text-white font-bold text-base shadow-lg hover:bg-primary/90 transition-colors flex items-center justify-center gap-2">
              <span class="material-symbols-outlined text-xl">download</span>
              Download PDF
            </button>
            <p class="text-xs text-on-surface-variant/60">Worksheet terbuka di tab baru. Gunakan Ctrl+P untuk print/save PDF.</p>
          </div>

        {:else}

          {#if activeItem.image}
            <div class="w-full aspect-video rounded-2xl overflow-hidden border-2 border-white shadow-md">
              <img src={resolveActivityCoverImage(selectedType?.key, activeItem.slug || activeItem.id, activeItem.image)} alt={activeItem.title} class="w-full h-full object-cover"
                onerror={(e) => { e.target.style.display = 'none' }} />
            </div>
          {:else if activeItem.emoji}
            <div class="w-full aspect-video rounded-2xl flex items-center justify-center text-6xl border-2 border-white shadow-md"
              style="background: {selectedType?.bg || '#E8F5E9'}">
              {activeItem.emoji}
            </div>
          {/if}

        {#if activeItem.desc}
          <p class="text-sm text-on-surface-variant leading-relaxed">{activeItem.desc}</p>
        {/if}

        {#if activeItem.how}
          <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <p class="text-xs font-bold text-primary mb-2">🎲 Cara Bermain</p>
            <p class="text-sm text-on-surface-variant leading-relaxed">{activeItem.how}</p>
          </div>
        {/if}

        {#if activeItem.rules?.length}
          <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <p class="text-xs font-bold text-primary mb-2">📋 Aturan</p>
            <ul class="space-y-2">
              {#each activeItem.rules as rule}
                <li class="flex items-start gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-primary text-base mt-0.5">check_circle</span>
                  {rule}
                </li>
              {/each}
            </ul>
          </div>
        {/if}

        {#if activeItem.pages?.length}
          {#each activeItem.pages as page, i}
            <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] shadow-sm">
              <p class="text-xs font-bold text-primary mb-2">Halaman {i + 1}</p>
              {#if page.text}
                <p class="text-sm text-text-main leading-relaxed">{page.text}</p>
              {/if}
            </div>
          {/each}
        {/if}

        {#if activeItem.steps?.length}
          <div class="space-y-2">
            <p class="text-xs font-bold text-primary">📋 Langkah-Langkah</p>
            {#each activeItem.steps as step, i}
              <div class="flex items-start gap-3 bg-white rounded-2xl p-3 border-2 border-[#B7D9BC]">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 bg-primary">{i + 1}</div>
                <p class="text-sm text-on-surface-variant pt-0.5">{step}</p>
              </div>
            {/each}
          </div>
        {/if}

        {#if activeItem.materials?.length}
          <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <p class="text-xs font-bold text-primary mb-2">📦 Bahan yang Dibutuhkan</p>
            <ul class="space-y-1.5">
              {#each activeItem.materials as mat}
                <li class="flex items-center gap-2 text-xs text-on-surface-variant">
                  <span class="material-symbols-outlined text-sm text-primary">check_box_outline_blank</span>
                  {mat}
                </li>
              {/each}
            </ul>
          </div>
        {/if}

        {#if activeItem.lyrics}
          <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <p class="text-xs font-bold text-primary mb-2">🎵 Lirik</p>
            <p class="text-sm text-on-surface-variant whitespace-pre-line leading-relaxed">{activeItem.lyrics}</p>
          </div>
        {/if}

        {#if activeItem.moves?.length}
          <div class="bg-success-soft rounded-2xl p-4 border border-[#B7D9BC]/50">
            <p class="text-xs font-bold text-primary mb-2">💃 Gerakan</p>
            <ul class="space-y-1.5">
              {#each activeItem.moves as move, i}
                <li class="flex items-start gap-2 text-sm text-on-surface-variant">
                  <span class="font-bold text-primary shrink-0">{i + 1}.</span>{move}
                </li>
              {/each}
            </ul>
          </div>
        {/if}

        {#if activeItem.script}
          <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <p class="text-xs font-bold text-primary mb-2">🎤 Naskah</p>
            <p class="text-sm text-on-surface-variant italic leading-relaxed">"{activeItem.script}"</p>
          </div>
        {/if}

        {#if activeItem.tips?.length}
          <div class="bg-success-soft rounded-2xl p-4 border border-[#B7D9BC]/50">
            <p class="text-xs font-bold text-primary mb-2">💡 Tips</p>
            <ul class="space-y-1.5">
              {#each activeItem.tips as tip}
                <li class="flex items-start gap-2 text-sm text-on-surface-variant">
                  <span class="material-symbols-outlined text-primary text-base mt-0.5">lightbulb</span>
                  {tip}
                </li>
              {/each}
            </ul>
          </div>
        {/if}

        {#if activeItem.observation}
          <div class="bg-success-soft rounded-2xl p-4 border border-[#B7D9BC]/50">
            <p class="text-xs font-bold text-primary mb-1">🔍 Pengamatan</p>
            <p class="text-sm text-on-surface-variant">{activeItem.observation}</p>
          </div>
        {/if}

        {#if activeItem.explanation}
          <div class="bg-success-soft rounded-2xl p-4 border border-[#B7D9BC]/50">
            <p class="text-xs font-bold text-primary mb-1">💡 Penjelasan</p>
            <p class="text-sm text-on-surface-variant leading-relaxed">{activeItem.explanation}</p>
          </div>
        {/if}

        {#if activeItem.exercises?.length}
          <div class="space-y-2">
            <p class="text-xs font-bold text-primary">🧠 Latihan Otak</p>
            {#each activeItem.exercises as ex, i}
              <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
                <div class="flex items-center gap-2 mb-2">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shrink-0 bg-primary">{i + 1}</div>
                  <p class="text-sm font-bold text-text-main">{ex.title}</p>
                </div>
                <p class="text-sm text-on-surface-variant leading-relaxed mb-2">{ex.instruction}</p>
                {#if ex.answer}
                  <div class="bg-success-soft rounded-xl p-2 border border-[#B7D9BC]/50">
                    <p class="text-xs text-primary"><span class="font-bold">Jawaban:</span> {ex.answer}</p>
                  </div>
                {/if}
              </div>
            {/each}
          </div>
        {/if}

        {#if activeItem.funFact}
          <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <p class="text-xs font-bold text-primary mb-1">⭐ Tahukah Kamu?</p>
            <p class="text-sm text-on-surface-variant leading-relaxed italic">{activeItem.funFact}</p>
          </div>
        {/if}

        {#if activeItem.moral}
          <div class="bg-success-soft rounded-2xl p-4 border border-[#B7D9BC]/50">
            <p class="text-xs font-bold text-primary mb-1">💬 Pelajaran</p>
            <p class="text-sm text-on-surface-variant">{activeItem.moral}</p>
          </div>
        {/if}

        {#if activeItem.duration || activeItem.difficulty}
          <div class="flex gap-2">
            {#if activeItem.duration}
              <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">⏱ {activeItem.duration}</span>
            {/if}
            {#if activeItem.difficulty}
              <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">{activeItem.difficulty}</span>
            {/if}
          </div>
        {/if}

        {/if}
      </div>
    </div>
  </div>
{/if}
