<script>
  import { trackActivityView, deleteActivityById } from '../../services/api.js'
  import { isOffline } from '../../utils/network.js'
  import { queue } from '../../services/syncService.js'
  import { userRole } from '../../stores/authStore.js'
  import DevPanel from '../../components/DevPanel.svelte'
  import { generatePdf } from './pdf/index.js'

  let { item, bg, onclick, type, ondelete } = $props()

  let showReader = $state(false)
  let currentQ = $state(0)
  let isFinished = $state(false)
  let showAnswer = $state(false)
  let slideDirection = $state('none')
  let isAnimating = $state(false)
  let score = $state(0)
  let answered = $state(false)
  let userRoleVal = $state('')
  let deletingActivity = $state(false)
  let devPanel = $state(null)

  let downloading = $state(false)

  async function handleDownload() {
    if (downloading) return
    downloading = true
    try { await generatePdf(item, type) }
    catch (e) { console.error('PDF download failed:', e) }
    finally { downloading = false }
  }

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })

  const statusColors = {
    approved: { bg: '#E1F2E5', text: '#176c33', label: 'Approved' },
    pending: { bg: '#FFF3E0', text: '#E65100', label: 'Pending' },
    review: { bg: '#E3F2FD', text: '#0D47A1', label: 'Review' },
    rejected: { bg: '#FFEBEE', text: '#C62828', label: 'Rejected' },
  }

  const normalizedStatus = $derived(item.status?.toLowerCase() || '')

  const questions = $derived(item.questions || [])
  const totalQ = $derived(questions.length)
  const currentData = $derived(questions[currentQ] || {})

  function openReader() {
    currentQ = 0
    isFinished = false
    showAnswer = false
    answered = false
    score = 0
    showReader = true
    window.__readerOpen = true
    if (devPanel) devPanel.initStatus()
    if (typeof window !== 'undefined') {
      history.pushState({ reader: true }, '')
    }
  }

  function closeReader() {
    showReader = false
    window.__readerOpen = false
  }

  function markAnswer(correct) {
    if (answered) return
    answered = true
    if (correct) score++
  }

  function prevQ() {
    if (isAnimating) return
    if (isFinished) {
      isFinished = false
      showAnswer = false
      answered = false
      score = 0
      currentQ = 0
      return
    }
    if (currentQ > 0) {
      showAnswer = false
      answered = false
      isAnimating = true
      slideDirection = 'prev'
      setTimeout(() => { currentQ-- }, 150)
      setTimeout(() => { slideDirection = 'none'; isAnimating = false }, 300)
    }
  }

  function nextQ() {
    if (isAnimating) return
    if (isFinished) {
      closeReader()
      return
    }
    if (!showAnswer) {
      showAnswer = true
      return
    }
    if (!answered) return
    showAnswer = false
    answered = false
    if (currentQ < totalQ - 1) {
      isAnimating = true
      slideDirection = 'next'
      setTimeout(() => { currentQ++ }, 150)
      setTimeout(() => { slideDirection = 'none'; isAnimating = false }, 300)
    } else {
      isFinished = true
      if (item.id) {
        if (isOffline()) { queue('trackView', { id: item.id }) }
        else { trackActivityView(item.id).then(d => { if (d) item.views = (d.views || 0) + 1 }).catch(() => {}) }
      }
    }
  }

  $effect(() => {
    function onClose() {
      if (showReader) {
        showReader = false
        window.__readerOpen = false
      }
    }
    function onKeydown(e) {
      if (!showReader) return
      if (e.key === 'ArrowLeft') { e.preventDefault(); prevQ() }
      else if (e.key === 'ArrowRight') { e.preventDefault(); nextQ() }
    }
    window.addEventListener('close-reader', onClose)
    window.addEventListener('keydown', onKeydown)
    return () => { window.removeEventListener('close-reader', onClose); window.removeEventListener('keydown', onKeydown) }
  })

  async function handleDelete() {
    if (!item?.id) return
    if (!confirm(`Hapus "${item.title}"?`)) return
    deletingActivity = true
    try {
      await deleteActivityById(item.id)
      closeReader()
      ondelete?.(item.id)
    } catch (e) {
      console.error('Delete failed:', e)
      alert('Gagal menghapus. Silakan coba lagi.')
    } finally {
      deletingActivity = false
    }
  }
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  onclick={openReader}>
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '🤔'}
      </div>
      {#if totalQ > 0}
        <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: {bg}; color: #FF6F00">
          {totalQ} Quiz
        </span>
      {/if}
    </div>
    <h3 class="font-bold text-lg mb-2">{item.title}</h3>
    <div class="mt-auto px-3 py-2.5 flex items-center justify-between bg-success-soft">
      <div class="flex items-center gap-1.5 text-xs text-text-secondary">
        <span class="text-sm text-primary">👁</span>
        <span class="font-medium">{item.views || 0}</span>
      </div>
      <div class="flex items-center gap-2">
        <div class="flex items-center gap-1.5 text-xs text-text-secondary">
          <span class="font-medium">Mulai</span>
        </div>

      </div>
    </div>
  </div>
</button>

{#if showReader}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center lg:p-4" onclick={closeReader}>
    <div class="w-full max-w-md lg:rounded-[40px] lg:shadow-2xl lg:border-8 border-[#B7D9BC] overflow-hidden flex flex-col max-h-[100dvh] lg:max-h-[90vh] relative" style="background: #FFF8F0" onclick={(e) => e.stopPropagation()}>

      <div class="relative px-4 pt-4 pb-2 flex items-center gap-2 z-20 shrink-0">
        <div class="text-white w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-xs font-bold shrink-0" style="background: #FF6F00">
          {isFinished ? '✓' : `${currentQ + 1}/${totalQ}`}
        </div>
        <div class="flex-1 min-w-0 px-3 py-2 rounded-2xl border-4 border-white shadow-md overflow-hidden" style="background: #FF6F00">
          <p class="text-sm font-semibold leading-tight line-clamp-2 text-white">{item.title}</p>
        </div>
        {#if userRoleVal === 'developer'}
          <button onclick={handleDelete} disabled={deletingActivity}
            class="w-11 h-11 rounded-full bg-error/80 text-white flex items-center justify-center text-base shrink-0 shadow-md hover:bg-error transition-colors disabled:opacity-50 border-4 border-white">
            🗑
          </button>
          <DevPanel bind:this={devPanel} {item} />
        {/if}
        <button onclick={closeReader}
          class="w-11 h-11 bg-error border-4 border-white text-white rounded-full flex items-center justify-center text-xl shadow-md hover:scale-105 active:scale-95 transition-all shrink-0">
          ✕
        </button>
      </div>

      {#if !isFinished}
        <div class="flex-1 flex flex-col justify-center px-4 py-5 gap-4 overflow-hidden page-flip"
          class:slide-next={slideDirection === 'next'}
          class:slide-prev={slideDirection === 'prev'}>

          <div class="text-center">
            <p class="text-base lg:text-lg text-on-surface leading-relaxed font-semibold">
              {currentData.question || ''}
            </p>
          </div>

          {#if currentData.hint}
            <div class="bg-white/70 rounded-[20px] border-2 border-[#B7D9BC]/50 px-4 py-3 text-center">
              <p class="text-sm text-on-surface-variant">💡 <span class="font-medium">{currentData.hint}</span></p>
            </div>
          {/if}

          {#if showAnswer}
            <div class="bg-success-soft rounded-[24px] border-4 border-[#B7D9BC] p-6 shadow-md text-center">
              <p class="text-xs font-bold mb-2 text-primary">✅ Jawaban</p>
              <p class="text-xl text-primary font-bold">
                {currentData.answer || ''}
              </p>
            </div>

            {#if !answered}
              <p class="text-xs text-center text-on-surface-variant font-medium">Kamu benar atau salah?</p>
              <div class="flex gap-3">
                <button onclick={() => markAnswer(true)}
                  class="flex-1 py-3 rounded-2xl border-4 border-white text-white font-bold text-base flex items-center justify-center gap-2 shadow-lg hover:scale-105 active:scale-95 transition-all"
                  style="background: #4CAF50">
                  ✅ Benar
                </button>
                <button onclick={() => markAnswer(false)}
                  class="flex-1 py-3 rounded-2xl border-4 border-white text-white font-bold text-base flex items-center justify-center gap-2 shadow-lg hover:scale-105 active:scale-95 transition-all"
                  style="background: #F44336">
                  ❌ Salah
                </button>
              </div>
            {:else}
              <div class="rounded-2xl border-4 border-white px-4 py-3 text-center shadow-md"
                style="background: {score > 0 ? '#E8F5E9' : '#FFEBEE'}">
                <p class="text-sm font-bold" style="color: {score > 0 ? '#2E7D32' : '#C62828'}">
                  {score > 0 ? '✅ Benar!' : '❌ Salah!'}
                </p>
              </div>
            {/if}
          {:else}
            <button onclick={() => showAnswer = true}
              class="rounded-[20px] border-4 border-dashed border-[#B7D9BC] px-4 py-4 text-center cursor-pointer hover:bg-white/50 transition-colors">
              <p class="text-sm font-semibold" style="color: #FF6F00">👀 Lihat Jawaban</p>
            </button>
          {/if}
        </div>
      {:else}
        <div class="flex-1 flex flex-col justify-center px-5 gap-5 overflow-y-auto py-6">
          <div class="flex flex-col items-center">
            <div class="w-20 h-20 rounded-full border-4 border-white flex items-center justify-center text-5xl shadow-lg mb-2"
              style="background: {score === totalQ ? '#4CAF50' : score > 0 ? '#FF9800' : '#F44336'}">
              {score === totalQ ? '🎉' : score > 0 ? '💪' : '😅'}
            </div>
            <p class="text-2xl font-bold" style="color: #FF6F00">Jawaban {score}/{totalQ} | Score {totalQ > 0 ? Math.round(score / totalQ * 100) : 0}/100</p>
            <p class="text-xs font-bold mt-1 text-on-surface-variant">
              {score === totalQ ? 'Sempurna! Semua benar!' : score > totalQ / 2 ? 'Bagus! Terus belajar ya!' : 'Ayo coba lagi!'}
            </p>
          </div>

          {#if item.moral}
            <div class="bg-white rounded-[32px] border-4 border-[#B7D9BC] p-5 shadow-md relative">
              <div class="flex items-center gap-2 mb-3 justify-center">
                <span class="w-8 h-8 rounded-full border-2 border-[#B7D9BC] flex items-center justify-center text-base" style="background: {bg}">💬</span>
                <p class="text-base font-bold" style="color: #FF6F00">Pelajaran</p>
              </div>
              <p class="text-text-main text-base text-center leading-relaxed font-medium" style="font-style: italic;">
                {item.moral}
              </p>
            </div>
          {/if}

          {#if item.creator}
            <div class="bg-white rounded-[24px] border-2 border-[#B7D9BC] p-4 shadow-sm">
              <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-full flex items-center justify-center" style="background: {bg}">
                  <span class="text-sm" style="color: #FF6F00">👤</span>
                </span>
                <p class="text-xs font-bold" style="color: #FF6F00">Dibuat oleh</p>
              </div>
              <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{item.creator}</p>
            </div>
          {/if}
        </div>
      {/if}

      <div class="p-4 rounded-t-[40px] border-t-4 border-[#B7D9BC] flex flex-col gap-3 items-center shrink-0" style="background: {bg || '#FFF8E1'}">
        <div class="w-full flex gap-3">
          <button onclick={prevQ} disabled={!isFinished && currentQ === 0}
            class="flex-1 py-3 px-4 rounded-2xl border border-stone-400 font-semibold text-base flex items-center justify-center gap-2 transition-all
              {!isFinished && currentQ === 0 ? 'text-on-surface-variant btn-pop-gray opacity-60 cursor-not-allowed' : 'text-text-main btn-pop-gray'}">
            <span class="text-xl">←</span>
            {isFinished ? 'Main Lagi' : 'Back'}
          </button>

          <button onclick={nextQ}
            disabled={!isFinished && showAnswer && !answered}
            class="flex-1 py-3 px-4 rounded-2xl border text-white font-semibold text-base flex items-center justify-center gap-2 transition-all
              {!isFinished && showAnswer && !answered ? 'opacity-50 cursor-not-allowed' : ''} btn-pop-orange">
            {isFinished ? 'Tutup' : !showAnswer ? 'Jawab' : currentQ === totalQ - 1 ? 'Selesai' : 'Next'}
            <span class="text-xl">
              {isFinished ? '✕' : !showAnswer ? '👀' : currentQ === totalQ - 1 ? '✓' : '→'}
            </span>
          </button>
        </div>
      </div>

    </div>
  </div>
{/if}

<style>
  .btn-pop-orange {
    background-color: #FF8A50;
    box-shadow: 0 6px 0 #E65100;
    transition: all 0.1s ease;
  }
  .btn-pop-orange:active {
    transform: translateY(6px);
    box-shadow: 0 0px 0 #E65100;
  }
  .btn-pop-gray {
    background-color: #E5E7EB;
    box-shadow: 0 6px 0 #9CA3AF;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(6px);
    box-shadow: 0 0px 0 #9CA3AF;
  }

  .page-flip {
    transform-origin: center center;
    backface-visibility: hidden;
    will-change: transform, opacity;
  }

  .page-flip.slide-next {
    animation: slideOutLeft 0.2s ease-in forwards;
  }

  .page-flip.slide-prev {
    animation: slideOutRight 0.2s ease-in forwards;
  }

  .page-flip:not(.slide-next):not(.slide-prev) {
    animation: slideIn 0.2s ease-out forwards;
  }

  @keyframes slideOutLeft {
    0% { transform: translateX(0) scale(1); opacity: 1; }
    100% { transform: translateX(-60%) scale(0.92); opacity: 0; }
  }

  @keyframes slideOutRight {
    0% { transform: translateX(0) scale(1); opacity: 1; }
    100% { transform: translateX(60%) scale(0.92); opacity: 0; }
  }

  @keyframes slideIn {
    0% { transform: translateX(0) scale(0.92); opacity: 0; }
    100% { transform: translateX(0) scale(1); opacity: 1; }
  }
</style>
