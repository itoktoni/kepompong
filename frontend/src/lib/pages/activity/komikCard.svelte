<script>
  import { onMount, onDestroy } from 'svelte'
  import { resolveActivityCoverImage, resolveActivityImage } from '../../utils/images.js'
  import { trackActivityView, deleteActivityById } from '../../services/api.js'
  import { isOffline } from '../../utils/network.js'
  import { queue } from '../../services/syncService.js'
  import { userRole } from '../../stores/authStore.js'
  import DevPanel from '../../components/DevPanel.svelte'

  let { item, bg, onclick, type, ondelete } = $props()

  let deletingActivity = $state(false)

  let showReader = $state(false)
  let currentPanel = $state(0)
  let isFinished = $state(false)
  let isSpeaking = $state(false)
  let isSpeakingMoral = $state(false)
  let autoPlay = $state(false)
  let utterance = null
  let naratorVoice = null
  let userRoleVal = $state('')
  let devPanel = $state(null)
  let slideDirection = $state('none')
  let isAnimating = $state(false)

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

  let itemData = $state(null)

  const panels = $derived(itemData?.pages || item.pages || item.data?.pages || [])
  const totalPanels = $derived(panels.length)
  const currentPanelData = $derived(panels[currentPanel] || {})

  onMount(() => {
    loadVoices()
    if ('speechSynthesis' in window) {
      speechSynthesis.onvoiceschanged = loadVoices
    }
  })

  onDestroy(() => stopSpeech())

  function loadVoices() {
    if (!('speechSynthesis' in window)) return
    const voices = speechSynthesis.getVoices()
    naratorVoice = voices.find(v => v.lang === 'id-ID') || voices.find(v => v.lang.startsWith('id')) || null
  }

  function createUtterance(text) {
    const u = new SpeechSynthesisUtterance(text)
    u.lang = 'id-ID'
    u.rate = 0.9
    u.pitch = 1.1
    if (naratorVoice) u.voice = naratorVoice
    return u
  }

  async function openReader() {
    currentPanel = 0
    isFinished = false
    showReader = true
    window.__readerOpen = true
    if (devPanel) devPanel.initStatus()
    itemData = item
    if (typeof window !== 'undefined') {
      history.pushState({ reader: true }, '')
    }
  }

  function closeReader() {
    stopSpeech()
    showReader = false
    window.__readerOpen = false
  }

  function prevPanel() {
    if (isAnimating) return
    if (isFinished) {
      isFinished = false
      stopSpeech()
      return
    }
    if (currentPanel > 0) {
      autoPlay = false
      stopSpeech()
      isAnimating = true
      slideDirection = 'prev'
      setTimeout(() => { currentPanel-- }, 300)
      setTimeout(() => { slideDirection = 'none'; isAnimating = false }, 600)
    }
  }

  function nextPanel() {
    if (isAnimating) return
    if (isFinished) {
      closeReader()
      return
    }
    autoPlay = false
    stopSpeech()
    if (currentPanel < totalPanels - 1) {
      isAnimating = true
      slideDirection = 'next'
      setTimeout(() => { currentPanel++ }, 300)
      setTimeout(() => { slideDirection = 'none'; isAnimating = false }, 600)
    } else {
      isFinished = true
      if (item.id) {
        if (isOffline()) { queue('trackView', { id: item.id }) }
        else { trackActivityView(item.id).then(d => { if (d) item.views = (d.views || 0) + 1 }).catch(() => {}) }
      }
    }
  }

  function toggleSpeech() {
    if (isSpeaking) {
      autoPlay = false
      stopSpeech()
    } else {
      autoPlay = true
      speak()
    }
  }

  function speak() {
    if (!('speechSynthesis' in window)) return
    stopSpeech()
    utterance = createUtterance(currentPanelData.text)
    utterance.onend = () => {
      isSpeaking = false
      if (autoPlay && currentPanel < totalPanels - 1) {
        currentPanel++
        setTimeout(() => speak(), 400)
      } else {
        autoPlay = false
      }
    }
    utterance.onerror = () => { isSpeaking = false; autoPlay = false }
    speechSynthesis.speak(utterance)
    isSpeaking = true
  }

  function speakMoral() {
    if (!('speechSynthesis' in window)) return
    if (isSpeakingMoral) {
      speechSynthesis.cancel()
      isSpeakingMoral = false
      return
    }
    speechSynthesis.cancel()
    const u = createUtterance(item.moral)
    u.onend = () => { isSpeakingMoral = false }
    u.onerror = () => { isSpeakingMoral = false }
    speechSynthesis.speak(u)
    isSpeakingMoral = true
  }

  function stopSpeech() {
    if ('speechSynthesis' in window) speechSynthesis.cancel()
    isSpeaking = false
    isSpeakingMoral = false
  }

  $effect(() => {
    function onClose() {
      if (showReader) {
        showReader = false
        stopSpeech()
        window.__readerOpen = false
      }
    }
    window.addEventListener('close-reader', onClose)
    return () => window.removeEventListener('close-reader', onClose)
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

<button class="group cursor-pointer w-full text-left"
  onclick={openReader}>
  <div class="relative transition-all duration-300 group-hover:-translate-y-1 group-hover:rotate-[1deg]">
    <div class="bg-white rounded-[24px] overflow-hidden shadow-lg border-4 relative"
      style="border-color: {userRoleVal === 'developer' && item.status && item.status !== 'approved' ? (statusColors[item.status]?.text || '#E65100') + '80' : '#B7D9BC'}">
      <div class="aspect-square p-2 overflow-hidden relative rounded-t-[20px]">
        {#if item.image}
          <img src={resolveActivityCoverImage(type, item.slug || item.id, item.image)} alt={item.title} class="w-full h-full object-cover group-hover:scale-110 rounded-2xl transition-transform duration-700" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
          <div class="w-full h-full flex-col items-center justify-center absolute inset-0 rounded-lg" style="background: {bg}; display: none">
            <span class="text-5xl mb-1">💬</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {:else}
          <div class="w-full h-full flex flex-col items-center justify-center rounded-lg" style="background: {bg}">
            <span class="text-5xl mb-1">💬</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {/if}
        <div class="absolute bottom-2 left-2">
          {#if userRoleVal === 'developer' && item.status && item.status !== 'approved'}
            {@const sc = statusColors[item.status] || statusColors.pending}
            <div class="rounded-full ml-1 mb-1 px-2.5 py-1 text-[10px] font-bold shadow-sm" style="background: {sc.bg}; color: {sc.text}">
              {sc.label}
            </div>
          {/if}
        </div>
        <div class="absolute bottom-2 right-2">
          {#if totalPanels > 0}
            <div class="bg-white/90 backdrop-blur-sm rounded-full mr-1 mb-1 px-2.5 py-1 text-[10px] font-bold shadow-sm" style="color: #E65100">
               {totalPanels} Panel
            </div>
          {/if}
        </div>
      </div>
      <div class="px-3 py-2.5 space-y-1">
        <h3 class="text-sm font-semibold text-on-surface line-clamp-2 leading-tight">{item.title}</h3>
        {#if item.moral}
          <p class="text-[10px] text-on-surface-variant line-clamp-1">💬 {item.moral}</p>
        {/if}
      </div>
      <div class="px-3 py-2.5 flex items-center justify-between" style="background: {bg}">
        <div class="flex items-center gap-1.5 text-xs text-text-secondary">
           <span class="text-sm" style="color: #E65100">👁</span>
          <span class="font-medium">{item.views || 0}</span>
        </div>

        {#if totalPanels > 0}
          <div class="flex items-center gap-1.5 text-xs text-text-secondary">
            <span class="text-sm" style="color: #E65100">⏱</span>
            <span class="font-medium">+{Math.ceil(totalPanels * 0.5)} menit</span>
          </div>
        {/if}
      </div>
    </div>
  </div>
</button>

{#if showReader}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center lg:p-4" onclick={closeReader}>
    <div class="w-full max-w-md lg:rounded-[40px] lg:shadow-2xl lg:border-8 border-[#B7D9BC] overflow-hidden flex flex-col max-h-[100dvh] lg:max-h-[90vh] relative" style="background: #FFF8F0" onclick={(e) => e.stopPropagation()}>

      <div class="relative px-4 pt-4 pb-2 flex items-center gap-2 z-20 shrink-0">
        <div class="text-white w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-xs font-bold shrink-0" style="background: #E65100">
          {isFinished ? '✓' : `${currentPanel + 1}/${totalPanels}`}
        </div>
        <div class="flex-1 min-w-0 text-on-primary px-3 py-2 rounded-2xl border-4 border-white shadow-md overflow-hidden" style="background: #E65100">
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
        <div class="flex-1 flex flex-col justify-center px-4 py-5 gap-3 overflow-hidden page-flip"
          class:slide-next={slideDirection === 'next'}
          class:slide-prev={slideDirection === 'prev'}>

          <div class="w-full rounded-[20px] border-4 border-white shadow-lg overflow-hidden relative" style="background: {bg || '#FFF3E0'}">
            {#if currentPanelData.num}
              {@const imgSrc = resolveActivityImage(type, item.slug || item.id, currentPanelData.num + '.png')}
              {#if item.image}
                <img src={imgSrc} alt={currentPanelData.text || item.title}
                  class="w-full object-contain max-h-[60vh]" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
                <div class="w-full h-48 flex-col items-center justify-center absolute inset-0" style="background: {bg || '#FFF3E0'}; display: none">
                  <span class="text-5xl mb-1">💬</span>
                  <p class="text-xs font-bold text-on-surface-variant">Panel belum tersedia</p>
                </div>
              {:else}
                <div class="w-full min-h-[120px] flex flex-col items-center justify-center py-6" style="background: {bg || '#FFF3E0'}">
                  <span class="text-4xl mb-2">💬</span>
                  <p class="text-xs font-bold text-on-surface-variant">Belum ada gambar</p>
                </div>
              {/if}
            {:else}
              <div class="w-full min-h-[120px] flex flex-col items-center justify-center py-6" style="background: {bg || '#FFF3E0'}">
                <span class="text-4xl mb-2">💬</span>
                <p class="text-xs font-bold text-on-surface-variant">Belum ada gambar</p>
              </div>
            {/if}
          </div>

          {#if currentPanelData.text}
            <div class="bg-white rounded-[20px] border-4 border-[#B7D9BC] p-4 shadow-md">
              <p class="text-text-main text-sm lg:text-base text-center leading-relaxed font-medium">
                {currentPanelData.text}
              </p>
            </div>
          {/if}
        </div>
      {:else}
        <div class="flex-1 flex flex-col justify-center px-5 gap-5 overflow-y-auto py-6">
          <div class="flex flex-col items-center">
            <p class="text-xs mt-2 font-bold" style="color: #E65100">Komik Selesai!</p>
          </div>

          {#if item.moral}
            <div class="bg-white rounded-[32px] border-4 border-[#B7D9BC] p-5 shadow-md relative">

              <div class="flex items-center gap-2 mb-3 justify-center">
                <span class="w-8 h-8 rounded-full border-2 border-[#B7D9BC] flex items-center justify-center text-base" style="background: {bg}">💬</span>
                <p class="text-base font-bold" style="color: #E65100">Pelajaran</p>
              </div>
              <p class="text-text-main text-base text-center leading-relaxed font-medium" style="font-style: italic;">
                {item.moral}
              </p>
            </div>

            <div class="flex justify-center">
              <button onclick={speakMoral}
                class="border-4 border-white px-5 py-2.5 rounded-full flex items-center gap-2 text-base font-semibold shadow-lg hover:scale-105 active:scale-95 transition-all"
                class:bg-error={isSpeakingMoral}
                class:text-on-error={isSpeakingMoral}
                style={!isSpeakingMoral ? 'background: #E65100; color: white' : ''}>
                <span class="text-xl">
                  {isSpeakingMoral ? '⏹' : '🔊'}
                </span>
                {isSpeakingMoral ? 'Berhenti' : 'Pelajaran'}
              </button>
            </div>
          {/if}

          {#if item.creator}
            <div class="bg-white rounded-[24px] border-2 border-[#B7D9BC] p-4 shadow-sm">
              <div class="flex items-center gap-2 mb-2">
                <span class="w-7 h-7 rounded-full flex items-center justify-center" style="background: {bg}">
                  <span class="text-sm" style="color: #E65100">👤</span>
                </span>
                <p class="text-xs font-bold" style="color: #E65100">Dibuat oleh</p>
              </div>
              <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{item.creator}</p>
            </div>
          {/if}
        </div>
      {/if}

      <div class="p-4 rounded-t-[40px] border-t-4 border-[#B7D9BC] flex flex-col gap-3 items-center shrink-0" style="background: {bg || '#FFF3E0'}">
        <div class="w-full flex gap-3">
          <button onclick={prevPanel} disabled={!isFinished && currentPanel === 0}
            class="flex-1 py-3 px-4 rounded-2xl border border-stone-400 font-semibold text-base flex items-center justify-center gap-2 transition-all
              {!isFinished && currentPanel === 0 ? 'text-on-surface-variant btn-pop-gray opacity-60 cursor-not-allowed' : 'text-text-main btn-pop-gray'}">
            <span class="text-xl">←</span>
            {isFinished ? 'Baca Lagi' : 'Back'}
          </button>

          {#if !isFinished}
            <button onclick={toggleSpeech}
              class="py-3 px-4 rounded-2xl border-4 border-white flex items-center justify-center gap-1.5 text-sm font-semibold shadow-lg hover:scale-105 active:scale-95 transition-all shrink-0 text-white"
              class:bg-error={isSpeaking}
              class:text-on-error={isSpeaking}
              style={!isSpeaking ? 'background: #E65100' : ''}>
              <span class="text-lg" class:animate-pulse={!isSpeaking}>
                {isSpeaking ? '⏹' : '🔊'}
              </span>
              {isSpeaking ? 'Stop' : 'Play'}
            </button>
          {/if}

          <button onclick={nextPanel}
            class="flex-1 py-3 px-4 rounded-2xl border text-white font-semibold text-base flex items-center justify-center gap-2 transition-all btn-pop-orange">
            {isFinished ? 'Tutup' : currentPanel === totalPanels - 1 ? 'Selesai' : 'Next'}
            <span class="text-xl">
              {isFinished ? '✕' : currentPanel === totalPanels - 1 ? '✓' : '→'}
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
  @keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
    100% { transform: translateY(0px); }
  }
  .floating-illustration {
    animation: float 4s ease-in-out infinite;
  }

  .page-flip {
    transform-origin: center center;
    backface-visibility: hidden;
    will-change: transform, opacity;
  }

  .page-flip.slide-next {
    animation: slideOutLeft 0.35s ease-in forwards;
  }

  .page-flip.slide-prev {
    animation: slideOutRight 0.35s ease-in forwards;
  }

  .page-flip:not(.slide-next):not(.slide-prev) {
    animation: slideIn 0.35s ease-out forwards;
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
