<script>
  import { pilars } from '../data/pilars.js'
  import { getEvaluasi } from '../data/skills.js'
  import { ageLabel } from '../utils/age.js'
  import { shareProgress } from '../utils/share.js'
  import { anakList, resetSkill, deleteSkill } from '../stores/anakStore.js'
  import { user } from '../stores/authStore.js'
  import { selectedAnakId } from '../stores/appStore.js'
  import { toolsAnakId } from '../stores/toolsStore.js'
  import { getSetting, saveSetting } from '../db.js'
  import { queue } from '../services/syncService.js'
  import AppModal from '../components/AppModal.svelte'
  import AnakDropdown from '../components/AnakDropdown.svelte'

  let anakListVal = $state([])
  let selectedAnakIdVal = $state(null)
  let toolsAnakIdVal = $state(null)
  let userVal = $state(null)
  let openId = $state(null)
  let showCompleted = $state(null)
  let evaluationsData = $state({})
  let activeEvals = $state({})
  let completedCount = $state({})
  let totalPoints = $state({})
  let totalMax = $state({})

  let showEvaluasi = $state(false)
  let evalAnak = $state(null)
  let evalSkill = $state(null)
  let evalQuestions = $state([])
  let evalPoints = $state(0)
  let evalMax = 10
  let evalSaving = $state(false)
  let autoSaveTimer = null
  let loadingAnakId = $state(null)
  let fetchedIds = new Set()

  $effect(() => {
    const u1 = anakList.subscribe(v => anakListVal = v)
    const u2 = selectedAnakId.subscribe(v => selectedAnakIdVal = v)
    const u3 = user.subscribe(v => userVal = v)
    const u4 = toolsAnakId.subscribe(v => toolsAnakIdVal = v)
    return () => { u1(); u2(); u3(); u4() }
  })

  $effect(() => {
    if (selectedAnakIdVal && !fetchedIds.has(selectedAnakIdVal)) {
      openId = selectedAnakIdVal
      fetchedIds.add(selectedAnakIdVal)
      fetchEvaluations(selectedAnakIdVal)
    }
  })

  $effect(() => {
    const ids = anakListVal.map(a => a.id)
    for (const id of ids) {
      if (id !== selectedAnakIdVal && !fetchedIds.has(id)) {
        fetchedIds.add(id)
        fetchEvaluations(id)
      }
    }
  })

  const filteredAnakList = $derived(toolsAnakIdVal ? anakListVal.filter(a => a.id === toolsAnakIdVal) : anakListVal)

  function toggle(id) {
    if (openId === id) { openId = null }
    else { openId = id; fetchEvaluations(id) }
  }

  async function fetchEvaluations(anakId) {
    const cacheKey = `eval_cache_${anakId}`
    const cached = await getSetting(cacheKey)
    if (cached) {
      evaluationsData[anakId] = cached.evaluations || []
      activeEvals[anakId] = cached.active || []
      completedCount[anakId] = cached.completed_count || 0
      totalPoints[anakId] = cached.total_points || 0
      totalMax[anakId] = cached.total_max || 0
      evaluationsData = { ...evaluationsData }
      activeEvals = { ...activeEvals }
      completedCount = { ...completedCount }
      totalPoints = { ...totalPoints }
      totalMax = { ...totalMax }
    }
  }

  function getAnakTotal(anakId) {
    return {
      points: totalPoints[anakId] || 0,
      max: totalMax[anakId] || 0,
      completed: completedCount[anakId] || 0,
    }
  }

  function getSkillProgress(anakId, skillKey) {
    const evals = activeEvals[anakId] || []
    const ev = evals.find(e => e.evaluation_skill_key === skillKey)
    if (!ev?.evaluation_max_points) return { points: 0, max: 10, percent: 0 }
    return { points: ev.evaluation_points, max: ev.evaluation_max_points, percent: Math.round((ev.evaluation_points / ev.evaluation_max_points) * 100) }
  }

  function getCompletedEvals(anakId) {
    return (evaluationsData[anakId] || []).filter(e => e.evaluation_points >= e.evaluation_max_points)
  }

  function getPilarName(key) {
    const p = pilars.find(p => p.key === key)
    return p ? p.title : key
  }

  function formatDate(dateStr) {
    if (!dateStr) return ''
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  }

  async function toggleActivityComplete(anakId, act) {
    if (!act.id) return
    act.completed = !act.completed
    queue('toggleActivity', { anakId, activityId: act.id, completed: act.completed })
  }

  const evalTitle = $derived(evalSkill ? `Evaluasi: ${evalSkill.title}` : 'Evaluasi')
  const evalDesc = $derived(evalSkill?.desc || '')
  const evalPercent = $derived(Math.min(100, Math.round((evalPoints / evalMax) * 100)))

  function openEvaluasi(anak, sp) {
    evalAnak = anak
    evalSkill = sp
    const data = getEvaluasi(sp.key)
    evalQuestions = data ? data.evaluasi : []
    const existing = (activeEvals[anak.id] || []).find(e => e.evaluation_skill_key === sp.key)
    evalPoints = existing ? existing.evaluation_points : 0
    showEvaluasi = true
  }

  function onSliderChange() {
    if (autoSaveTimer) clearTimeout(autoSaveTimer)
    autoSaveTimer = setTimeout(() => autoSaveEvaluation(), 800)
  }

  async function autoSaveEvaluation() {
    if (!evalAnak || !evalSkill) return
    evalSaving = true

    const evalData = {
      skill_key: evalSkill.key,
      skill_title: evalSkill.title,
      pilar: evalSkill.pilar,
      points: evalPoints,
      max_points: evalMax,
      notes: `${evalPoints} dari ${evalMax} poin`,
    }

    const cacheKey = `eval_cache_${evalAnak.id}`
    const cached = await getSetting(cacheKey) || { evaluations: [], active: [], completed_count: 0, total_points: 0, total_max: 0 }
    const existingIdx = cached.active.findIndex(e => e.evaluation_skill_key === evalSkill.key)
    const evalEntry = {
      evaluation_id_anak: evalAnak.id,
      evaluation_skill_key: evalSkill.key,
      evaluation_skill_title: evalSkill.title,
      evaluation_pilar: evalSkill.pilar,
      evaluation_points: evalPoints,
      evaluation_max_points: evalMax,
      evaluation_notes: evalData.notes,
      evaluation_created_at: new Date().toISOString(),
    }
    if (existingIdx >= 0) {
      cached.active[existingIdx] = evalEntry
    } else {
      cached.active.push(evalEntry)
    }
    cached.evaluations = [...cached.evaluations.filter(e => e.evaluation_skill_key !== evalSkill.key), evalEntry]
    cached.total_points = cached.evaluations.filter(e => e.evaluation_points >= e.evaluation_max_points).reduce((s, e) => s + e.evaluation_points, 0)
    await saveSetting(cacheKey, cached)

    evaluationsData[evalAnak.id] = cached.evaluations
    activeEvals[evalAnak.id] = cached.active
    evaluationsData = { ...evaluationsData }
    activeEvals = { ...activeEvals }

    queue('addEvaluation', { anakId: evalAnak.id, data: evalData })
    evalSaving = false
  }

  async function shareEval() {
    if (!evalAnak || !evalSkill) return
    await autoSaveEvaluation()
    const progress = getSkillProgress(evalAnak.id, evalSkill.key)
    shareProgress({
      title: evalSkill.title,
      category: getPilarName(evalSkill.pilar),
      emoji: evalSkill.emoji,
      color: evalSkill.color,
      points: progress.points,
      maxPoints: progress.max,
      notes: progress.points >= progress.max ? `Selesai! ${progress.points}/${progress.max} poin` : `Progress ${progress.percent}%`,
      childName: evalAnak.nama,
      isComplete: progress.points >= progress.max,
    })
  }

  function shareEvalDirect(anak, sp) {
    const progress = getSkillProgress(anak.id, sp.key)
    shareProgress({
      title: sp.title,
      category: getPilarName(sp.pilar),
      emoji: sp.emoji,
      color: sp.color,
      points: progress.points,
      maxPoints: progress.max,
      notes: progress.points >= progress.max ? `Selesai! ${progress.points}/${progress.max} poin` : `Progress ${progress.percent}%`,
      childName: anak.nama,
      isComplete: progress.points >= progress.max,
    })
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  <h2 class="font-headline-md text-headline-md mb-6 flex items-center gap-2">
    <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">📊</span> Laporan Perkembangan
  </h2>

  <div class="space-y-4">
    <AnakDropdown anakList={anakListVal} value={toolsAnakIdVal} onselect={(id) => toolsAnakId.set(id)} />

    {#if !anakListVal.length}
      <div class="bg-canvas-cream rounded-[32px] border-4 border-dashed border-[#B7D9BC] p-8 text-center">
        <div class="text-5xl mb-3">👶</div>
        <p class="font-label-lg text-text-main mb-1">Belum ada data anak</p>
        <p class="text-sm text-on-surface-variant">Tambahkan anak terlebih dahulu di menu Profil untuk mulai melihat perkembangan.</p>
      </div>
    {/if}

    {#each filteredAnakList as anak (anak.id)}
      <div class="bg-canvas-cream rounded-[28px] border-4 border-[#B7D9BC] shadow-md overflow-hidden">
        <button class="w-full flex items-center gap-4 p-5 text-left hover:bg-white/50 transition-colors"
          onclick={() => toggle(anak.id)}>
          <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl shrink-0 border-2 border-white shadow-sm bg-success-soft">
            {anak.emoji}
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-label-lg text-text-main">{anak.nama}</p>
            <p class="text-sm text-on-surface-variant">{ageLabel(anak.tahun, anak.bulan, anak.tanggal)}</p>
          </div>
          <div class="flex items-center gap-2">
            {#if anak.skills?.length}
              <span class="text-xs font-bold text-primary bg-success-soft px-2 py-1 rounded-full">
                {anak.skills.length} skills
              </span>
            {/if}
            {#if getAnakTotal(anak.id).completed > 0}
              <span class="text-xs font-bold text-white bg-primary px-2 py-1 rounded-full">
                {getAnakTotal(anak.id).completed} selesai
              </span>
            {/if}
            <span class="material-symbols-outlined text-primary transition-transform duration-200"
              class:rotate-180={openId === anak.id}>expand_more</span>
          </div>
        </button>

        {#if openId === anak.id}
          <div class="px-5 pb-5 space-y-5 border-t-2 border-[#B7D9BC]/50">
            {#if loadingAnakId === anak.id}
              <div class="pt-4 space-y-3">
                <div class="h-4 w-32 bg-[#B7D9BC]/50 rounded animate-pulse"></div>
                {#each [1, 2, 3] as _}
                  <div class="rounded-2xl p-4 border-2 border-[#B7D9BC]/50 bg-white/50">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="flex-1 space-y-2">
                        <div class="h-4 w-3/4 bg-[#B7D9BC]/30 rounded animate-pulse"></div>
                        <div class="h-3 w-1/2 bg-[#B7D9BC]/20 rounded animate-pulse"></div>
                      </div>
                      <div class="h-5 w-16 bg-[#B7D9BC]/30 rounded animate-pulse"></div>
                    </div>
                    <div class="w-full h-2 bg-[#B7D9BC]/20 rounded-full overflow-hidden">
                      <div class="h-full w-1/3 bg-[#B7D9BC]/40 rounded-full animate-pulse"></div>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-[#B7D9BC]/30">
                      <div class="h-3 w-24 bg-[#B7D9BC]/20 rounded animate-pulse"></div>
                      <div class="flex gap-2">
                        <div class="h-8 w-8 bg-[#B7D9BC]/20 rounded-lg animate-pulse"></div>
                        <div class="h-8 w-20 bg-[#B7D9BC]/30 rounded-lg animate-pulse"></div>
                      </div>
                    </div>
                  </div>
                {/each}
              </div>
            {:else if anak.skills?.length}
              <div class="pt-4 space-y-3">
                <h4 class="text-xs font-bold text-primary uppercase tracking-wider">Skills Aktif</h4>
                {#each anak.skills as sp (sp.key)}
                  <div class="rounded-2xl p-4 border-2 border-[#B7D9BC] shadow-sm">
                    <div class="flex items-center gap-3 mb-3">
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-text-main">{sp.title}</p>
                        <p class="text-xs text-on-surface-variant">{getPilarName(sp.pilar)}</p>
                      </div>
                      <span class="text-xs font-bold" style="color: {sp.color}">
                        Progress {getSkillProgress(anak.id, sp.key).percent}%
                      </span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mb-3">
                      <div class="h-full rounded-full transition-all duration-700"
                        style="width: {getSkillProgress(anak.id, sp.key).percent}%; background: {sp.color}"></div>
                    </div>

                    {#if sp.activities?.length}
                      <div class="mb-3 space-y-1.5">
                        {#each sp.activities as act (act.id || act.title)}
                          <div class="flex items-center gap-2 px-3 py-2 bg-canvas-cream rounded-xl text-xs border border-[#B7D9BC]/50
                            {act.completed ? 'opacity-50 line-through' : ''}">
                            <button onclick={() => toggleActivityComplete(anak.id, act)}
                              class="relative w-10 h-6 rounded-full shrink-0 transition-colors duration-200 focus:outline-none
                                {act.completed ? 'bg-primary' : 'bg-gray-300'}">
                              <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-200
                                {act.completed ? 'translate-x-4' : ''}"></span>
                            </button>
                            <span class="text-base">{act.emoji}</span>
                            <span class="flex-1 font-medium text-text-main">{act.title}</span>
                            <span class="text-on-surface-variant">{act.date}</span>
                          </div>
                        {/each}
                      </div>
                    {/if}

                    <div class="flex items-center justify-between">
                      <div class="flex items-center gap-1 text-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-xs">check_circle</span>
                        <span>{(sp.activities || []).filter(a => a.completed).length}/{(sp.activities || []).length} selesai</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <button onclick={() => deleteSkill({ anak, skill: sp })}
                          class="h-8 w-8 rounded-lg flex items-center justify-center border-2 transition-all active:scale-95 border-error/30 text-error">
                          <span class="material-symbols-outlined text-base">delete</span>
                        </button>
                        <button onclick={() => openEvaluasi(anak, sp)}
                          class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs text-white transition-all active:scale-95"
                          style="background: {sp.color}">
                          <span class="material-symbols-outlined text-sm">rate_review</span>
                          Evaluasi
                        </button>
                        <button onclick={() => shareEvalDirect(anak, sp)}
                          class="h-8 w-8 rounded-lg flex items-center justify-center border-2 transition-all active:scale-95"
                          style="border-color: {sp.color}60; color: {sp.color}">
                          <span class="material-symbols-outlined text-base">share</span>
                        </button>
                      </div>
                    </div>
                  </div>
                {/each}
              </div>
            {:else}
              <div class="pt-4 text-center text-sm text-on-surface-variant py-4">
                <p class="text-2xl mb-1">📝</p>
                Belum ada skills aktif
              </div>
            {/if}

            {#if anak.completedSkills?.length}
              <div>
                <h4 class="text-xs font-bold text-primary uppercase tracking-wider mb-3">Skills Selesai</h4>
                <div class="space-y-2">
                  {#each anak.completedSkills as sp (sp.key)}
                    <div class="flex items-center gap-3 bg-white rounded-2xl p-3 border-2 border-[#B7D9BC] shadow-sm">
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-text-main">{sp.title}</p>
                        <p class="text-xs text-on-surface-variant">{getPilarName(sp.pilar)}</p>
                      </div>
                      <button onclick={() => resetSkill({ anak, skill: sp })}
                        class="h-8 w-8 rounded-lg flex items-center justify-center border-2 transition-all active:scale-95 border-error/30 text-error">
                        <span class="material-symbols-outlined text-base">delete</span>
                      </button>
                      <button onclick={() => shareEvalDirect(anak, sp)}
                        class="h-8 w-8 rounded-lg flex items-center justify-center border-2 transition-all active:scale-95"
                        style="border-color: {(sp.color || '#176c33')}60; color: {sp.color || '#176c33'}">
                        <span class="material-symbols-outlined text-base">share</span>
                      </button>
                    </div>
                  {/each}
                </div>
              </div>
            {/if}

            {#if getCompletedEvals(anak.id).length}
              <div>
                <button onclick={() => showCompleted = showCompleted === anak.id ? null : anak.id}
                  class="w-full flex items-center justify-between py-2">
                  <h4 class="text-xs font-bold text-primary uppercase tracking-wider">
                    Evaluasi Selesai ({getCompletedEvals(anak.id).length})
                  </h4>
                  <span class="material-symbols-outlined text-primary text-lg transition-transform
                    {showCompleted === anak.id ? 'rotate-180' : ''}">expand_more</span>
                </button>
                {#if showCompleted === anak.id}
                  <div class="space-y-2">
                    {#each getCompletedEvals(anak.id) as ev (ev.id)}
                      <div class="flex items-center gap-3 bg-white rounded-xl p-3 border-2 border-[#B7D9BC] shadow-sm">
                        <div class="w-8 h-8 rounded-full bg-success-soft flex items-center justify-center shrink-0">
                          <span class="material-symbols-outlined text-primary text-sm">check_circle</span>
                        </div>
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-medium text-text-main">{ev.evaluation_skill_title}</p>
                          <p class="text-[10px] text-on-surface-variant">{formatDate(ev.evaluation_created_at)}</p>
                        </div>
                        <span class="text-xs font-bold text-white bg-primary px-2 py-1 rounded-full">
                          {ev.evaluation_points}/{ev.evaluation_max_points}
                        </span>
                      </div>
                    {/each}
                  </div>
                {/if}
              </div>
            {/if}
          </div>
        {/if}
      </div>
    {/each}
  </div>
</div>

<!-- Evaluasi Modal -->
{#if showEvaluasi}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 bg-black/50 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4" onclick={async () => { await autoSaveEvaluation(); showEvaluasi = false }}>
    <div class="bg-canvas-cream rounded-t-[32px] sm:rounded-[32px] border-4 border-primary shadow-xl w-full sm:max-w-lg max-h-[90vh] flex flex-col overflow-hidden"
      onclick={(e) => e.stopPropagation()}>
      <div class="p-5 sm:p-6 overflow-y-auto overscroll-contain flex-1 min-h-0">
      <div class="w-10 h-1 bg-outline-variant rounded-full mx-auto mb-4 sm:hidden"></div>

      <h3 class="font-bold text-lg text-text-main mb-2">{evalTitle}</h3>

      {#if evalDesc}
        <p class="text-xs text-on-surface-variant mb-4">{evalDesc}</p>
      {/if}

      {#if evalQuestions.length}
        <div class="space-y-2 mb-5">
          <p class="text-[11px] font-bold text-primary uppercase tracking-wider">Panduan Penilaian</p>
          {#each evalQuestions as q, i}
            <div class="bg-white rounded-xl p-3 text-sm text-text-main border-2 border-[#B7D9BC]/50">
              {i + 1}. {q}
            </div>
          {/each}
        </div>
      {/if}

      <div class="bg-success-soft rounded-xl p-3 border-2 border-[#B7D9BC]/50 mb-4 text-center">
        <p class="text-xs text-on-surface-variant">
          {#if userVal?.gender === 'female'}
            Menurut <span class="font-bold text-text-main">Bunda</span>, berapa nilai yang sudah <span class="font-bold text-text-main">ananda</span> peroleh berdasarkan pertanyaan diatas terhadap softskill yang sudah dikuasai?
          {:else if userVal?.gender === 'male'}
            Menurut <span class="font-bold text-text-main">Ayah</span>, berapa nilai yang sudah <span class="font-bold text-text-main">ananda</span> peroleh berdasarkan pertanyaan diatas terhadap softskill yang sudah dikuasai?
          {:else}
            Menurut Ayah / Bunda, berapa nilai yang sudah <span class="font-bold text-text-main">ananda</span> peroleh berdasarkan pertanyaan diatas terhadap softskill yang sudah dikuasai?
          {/if}
        </p>
      </div>

      <div class="bg-white rounded-2xl p-4 shadow-sm border-2 border-[#B7D9BC]/50">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold text-primary uppercase tracking-wider">Penilaian</span>
          <span class="text-xs font-bold text-primary">{evalPoints}/{evalMax}</span>
        </div>
        <div class="w-full h-5 rounded-full overflow-hidden relative bg-primary/10 mb-3">
          <div class="h-full rounded-full transition-all duration-300 bg-primary"
            style="width: {evalPercent}%"></div>
        </div>
        <input type="range" bind:value={evalPoints} min={0} max={evalMax} step={1}
          oninput={onSliderChange}
          class="w-full accent-primary h-3 rounded-full appearance-none cursor-pointer" />
        <div class="flex justify-between text-[10px] text-on-surface-variant mt-1.5 px-0.5">
          {#each Array(evalMax + 1) as _, i}
            <span>{i}</span>
          {/each}
        </div>
      </div>

      <div class="flex gap-3 mt-5">
        <button onclick={async () => { await autoSaveEvaluation(); showEvaluasi = false }}
          class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray">Tutup</button>
        <button onclick={shareEval}
          class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green flex items-center justify-center gap-1">
          <span class="material-symbols-outlined text-lg">share</span> Share
        </button>
      </div>
      </div>
    </div>
  </div>
{/if}

<style>
  input[type="range"] {
    -webkit-appearance: none;
    background: transparent;
  }
  input[type="range"]::-webkit-slider-runnable-track {
    height: 10px;
    border-radius: 5px;
    background: #B7D9BC;
  }
  input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #176C33;
    margin-top: -9px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    cursor: pointer;
  }
  input[type="range"]::-moz-range-track {
    height: 10px;
    border-radius: 5px;
    background: #B7D9BC;
  }
  input[type="range"]::-moz-range-thumb {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #176C33;
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    cursor: pointer;
  }
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 3px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #176c33;
  }
  .btn-pop-gray {
    background-color: #e5e5e5;
    box-shadow: 0 3px 0 #999;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #999;
  }
  .overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #B7D9BC transparent;
  }
  .overflow-y-auto::-webkit-scrollbar {
    width: 4px;
  }
  .overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
  }
  .overflow-y-auto::-webkit-scrollbar-thumb {
    background-color: #B7D9BC;
    border-radius: 9999px;
  }
</style>
