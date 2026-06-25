<script>
  import { onMount, onDestroy } from 'svelte'
  import { resolveActivityCoverImage, resolveActivityImage } from '../../utils/images.js'
  import { trackActivityView, deleteActivityById } from '../../services/api.js'
  import { isOffline } from '../../utils/network.js'
  import { queue } from '../../services/syncService.js'
  import { userRole } from '../../stores/authStore.js'
  import DevPanel from '../../components/DevPanel.svelte'
  import { generatePdf } from './pdf/index.js'

  let { item, bg, onclick, type, ondelete } = $props()

  let slides = $derived(item.slides || item.data?.slides || [])
  let tags = $derived(item.tags || item.data?.tags || [])

  let showReader = $state(false)
  let currentSlide = $state(0)
  let isFinished = $state(false)
  let isSpeaking = $state(false)
  let autoPlay = $state(false)
  let utterance = null
  let naratorVoice = null
  let englishVoice = null
  let slideDirection = $state('none')
  let isAnimating = $state(false)
  let flipKey = $state(0)
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

  const IMAGES_URL = import.meta.env.VITE_IMAGES_URL || ''

  function resolveSlideImage(slide) {
    const img = slide.image
    if (img) {
      if (img.startsWith('http://') || img.startsWith('https://')) return img
      if (/^\d+\.png$/i.test(img)) return resolveActivityImage(type, item.slug || item.id, img)
      return `${IMAGES_URL}${type}/${img}`
    }
    const num = slide.num
    if (num) return resolveActivityImage(type, item.slug || item.id, num + '.png')
    return null
  }

  const totalSlides = $derived(slides.length)
  const currentSlideData = $derived(slides[currentSlide] || {})

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
    englishVoice = voices.find(v => v.lang === 'en-US') || voices.find(v => v.lang.startsWith('en')) || null
  }

  function speakText(text, lang = 'id-ID', voice = null, onend = null) {
    if (!('speechSynthesis' in window) || !text) return null
    const u = new SpeechSynthesisUtterance(text)
    u.lang = lang
    u.rate = 0.9
    u.pitch = 1.1
    if (voice) u.voice = voice
    if (onend) u.onend = onend
    speechSynthesis.speak(u)
    return u
  }

  function openReader() {
    currentSlide = 0
    isFinished = false
    showReader = true
    window.__readerOpen = true
    if (devPanel) devPanel.initStatus()
    if (typeof window !== 'undefined') {
      history.pushState({ reader: true }, '')
    }
  }

  function closeReader() {
    stopSpeech()
    showReader = false
    window.__readerOpen = false
  }

  function prevSlide() {
    if (isAnimating) return
    if (isFinished) {
      isFinished = false
      stopSpeech()
      return
    }
    if (currentSlide > 0) {
      autoPlay = false
      stopSpeech()
      isAnimating = true
      slideDirection = 'prev'
      setTimeout(() => { currentSlide-- }, 300)
      setTimeout(() => { slideDirection = 'none'; isAnimating = false }, 600)
    }
  }

  function nextSlide() {
    if (isAnimating) return
    if (isFinished) {
      closeReader()
      return
    }
    autoPlay = false
    stopSpeech()
    if (currentSlide < totalSlides - 1) {
      isAnimating = true
      slideDirection = 'next'
      setTimeout(() => { currentSlide++ }, 300)
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
      speakCurrent()
    }
  }

  function speakCurrent() {
    if (!('speechSynthesis' in window)) return
    stopSpeech()
    const d = currentSlideData
    const idText = d.nama + '. ' + (d.digunakan_untuk || '')
    utterance = speakText(idText, 'id-ID', naratorVoice, () => {
      isSpeaking = false
      if (d.english) {
        const enUtterance = speakText(d.english, 'en-US', englishVoice, () => {
          if (autoPlay && currentSlide < totalSlides - 1) {
            currentSlide++
            setTimeout(() => speakCurrent(), 500)
          } else {
            autoPlay = false
          }
        })
        if (enUtterance) { enUtterance.onerror = () => { autoPlay = false } }
      } else {
        if (autoPlay && currentSlide < totalSlides - 1) {
          currentSlide++
          setTimeout(() => speakCurrent(), 500)
        } else {
          autoPlay = false
        }
      }
    })
    if (utterance) {
      utterance.onerror = () => { isSpeaking = false; autoPlay = false }
      isSpeaking = true
    }
  }

  function stopSpeech() {
    if ('speechSynthesis' in window) speechSynthesis.cancel()
    isSpeaking = false
  }

  async function handleDelete() {
    if (!item?.id) return
    if (!confirm(`Hapus "${item.title}"?`)) return
    deletingActivity = true
    try { await deleteActivityById(item.id); closeReader(); ondelete?.(item.id) }
    catch (e) { alert('Gagal menghapus.') }
    finally { deletingActivity = false }
  }

  $effect(() => {
    function onClose() {
      if (showReader) {
        showReader = false
        stopSpeech()
        window.__readerOpen = false
      }
    }
    function onKeydown(e) {
      if (!showReader) return
      if (e.key === 'ArrowLeft') { e.preventDefault(); prevSlide() }
      else if (e.key === 'ArrowRight') { e.preventDefault(); nextSlide() }
    }
    window.addEventListener('close-reader', onClose)
    window.addEventListener('keydown', onKeydown)
    return () => { window.removeEventListener('close-reader', onClose); window.removeEventListener('keydown', onKeydown) }
  })
</script>

<button class="group cursor-pointer w-full text-left"
  onclick={openReader}>
  <div class="relative transition-all duration-300 group-hover:-translate-y-1 group-hover:rotate-[1deg]">
    <div class="bg-white rounded-[24px] overflow-hidden shadow-lg border-4 relative"
      style="border-color: {userRoleVal === 'developer' && normalizedStatus && normalizedStatus !== 'approved' ? (statusColors[normalizedStatus]?.text || '#E65100') + '80' : '#B7D9BC'}">
      <div class="aspect-square p-2 overflow-hidden relative rounded-t-[20px]">
        {#if item.image}
          <img src={resolveActivityCoverImage(type, item.slug || item.id, item.image)} alt={item.title} class="w-full h-full object-cover group-hover:scale-110 rounded-2xl transition-transform duration-700" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
          <div class="w-full h-full flex-col items-center justify-center absolute inset-0 rounded-lg" style="background: {bg}; display: none">
            <span class="text-5xl mb-1">🪣</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {:else}
          <div class="w-full h-full flex flex-col items-center justify-center rounded-lg" style="background: {bg}">
            <span class="text-5xl mb-1">🪣</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {/if}
        <div class="absolute top-2 left-2">
          {#if userRoleVal === 'developer' && item.creator}
            <div class="bg-white/90 backdrop-blur-sm rounded-full ml-1 mt-1 px-2.5 py-1 text-[10px] font-bold text-primary shadow-sm">
              👤 {item.creator}
            </div>
          {/if}
        </div>
        <div class="absolute bottom-2 left-2">
          {#if userRoleVal === 'developer' && normalizedStatus && normalizedStatus !== 'approved'}
            {@const sc = statusColors[normalizedStatus] || statusColors.pending}
            <div class="rounded-full ml-1 mb-1 px-2.5 py-1 text-[10px] font-bold shadow-sm" style="background: {sc.bg}; color: {sc.text}">
              {sc.label}
            </div>
          {/if}
        </div>
        <div class="absolute bottom-2 right-2">
          {#if totalSlides > 0}
            <div class="bg-white/90 backdrop-blur-sm rounded-full mr-1 mb-1 px-2.5 py-1 text-[10px] font-bold shadow-sm" style="color: #5D4037">
              {totalSlides} Kata
            </div>
          {/if}
        </div>
      </div>
      <div class="px-3 py-2.5 space-y-1">
        <h3 class="text-sm font-semibold text-on-surface line-clamp-2 leading-tight">{item.title}</h3>
        {#if tags.length}
          <div class="flex flex-wrap gap-1">
            {#each tags.slice(0, 3) as tag}
              <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-[#EFEBE9] text-on-surface-variant">{tag}</span>
            {/each}
          </div>
        {/if}
        {#if slides.length}
          <p class="text-[10px] text-on-surface-variant line-clamp-1">
            <span class="font-bold">{slides[0].nama}</span>
            {#if slides[0].english}
              <span class="text-gray-400">({slides[0].english})</span>
            {/if}
          </p>
        {/if}
      </div>
      <div class="px-3 py-2.5 flex items-center justify-between" style="background: {bg}">
        <div class="flex items-center gap-1.5 text-xs text-text-secondary">
          <span class="text-sm" style="color: #5D4037">👁</span>
          <span class="font-medium">{item.views || 0}</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="flex items-center gap-2 text-primary font-label-lg">
            <span class="text-sm">🪣</span>
            Kenalan
            <span class="text-sm ml-auto group-hover:translate-x-1 transition-transform">→</span>
          </div>
          <span onclick={(e) => { e.stopPropagation(); handleDownload() }}
            class="w-7 h-7 rounded-full bg-white border border-[#B7D9BC] flex items-center justify-center text-xs hover:bg-success-soft transition-colors cursor-pointer shrink-0 {downloading ? 'opacity-50 pointer-events-none' : ''}"
            title="Download PDF" role="button" tabindex="0">
            {downloading ? '⏳' : '📥'}
          </span>
        </div>
      </div>
    </div>
  </div>
</button>

{#if showReader}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center lg:p-4" onclick={closeReader}>
    <div class="w-full max-w-md lg:rounded-[40px] lg:shadow-2xl lg:border-8 border-[#B7D9BC] overflow-hidden flex flex-col h-[100dvh] lg:h-auto lg:max-h-[90vh] relative" style="background: #FFF8F0" onclick={(e) => e.stopPropagation()}>

      <div class="relative px-4 pt-4 pb-2 flex items-center gap-2 z-20 shrink-0">
        <div class="text-white w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-xs font-bold shrink-0" style="background: #5D4037">
          {isFinished ? '✓' : `${currentSlide + 1}/${totalSlides}`}
        </div>
        <div class="flex-1 min-w-0 px-3 py-2 rounded-2xl border-4 border-white shadow-md overflow-hidden" style="background: #5D4037">
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
        <div class="flex-1 min-h-0 overflow-y-auto px-4 pb-2">
          <div class="space-y-3 page-flip"
            class:slide-next={slideDirection === 'next'}
            class:slide-prev={slideDirection === 'prev'}>

            <div class="w-full rounded-[20px] border-4 border-white shadow-lg overflow-hidden relative"
              style="background: {bg || '#EFEBE9'}">
              {#if resolveSlideImage(currentSlideData)}
                {@const slideImg = resolveSlideImage(currentSlideData)}
                <img src={slideImg} alt={currentSlideData.nama}
                  class="w-full object-contain max-h-[45vh]" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
                <div class="w-full h-48 flex-col items-center justify-center absolute inset-0" style="background: {bg || '#EFEBE9'}; display: none">
                  <span class="text-5xl mb-1">🪣</span>
                  <p class="text-xs font-bold text-on-surface-variant">Gambar tidak tersedia</p>
                </div>
              {:else}
                <div class="w-full h-48 flex flex-col items-center justify-center" style="background: {bg || '#EFEBE9'}">
                  <span class="text-5xl mb-1">🪣</span>
                  <p class="text-xs font-bold text-on-surface-variant">Gambar tidak tersedia</p>
                </div>
              {/if}
            </div>

            <div class="bg-white rounded-[20px] border-4 border-[#B7D9BC] p-5 shadow-md text-center">
              <h2 class="text-3xl font-bold" style="color: #5D4037">{currentSlideData.nama}</h2>
              {#if currentSlideData.english}
                <p class="text-lg text-gray-500 italic mt-1">{currentSlideData.english}</p>
              {/if}
              {#if currentSlideData.digunakan_untuk}
                <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{currentSlideData.digunakan_untuk}</p>
              {/if}
            </div>
          </div>
        </div>

      {:else}
        <div class="flex-1 min-h-0 flex flex-col justify-center px-5 gap-5 overflow-y-auto py-6">
          <div class="flex flex-col items-center">
            <div class="w-20 h-20 rounded-full border-4 border-white flex items-center justify-center text-5xl shadow-lg floating-illustration mb-1" style="background: #5D4037">
              🎉
            </div>
            <p class="text-xs mt-2 font-bold" style="color: #5D4037">Selesai!</p>
          </div>

          <div class="bg-white rounded-[32px] border-4 border-[#B7D9BC] p-5 shadow-md">
            <p class="text-sm text-center text-on-surface-variant">
              Kamu sudah mengenal <span class="font-bold" style="color: #5D4037">{totalSlides}</span> kata baru!
            </p>
          </div>
        </div>
      {/if}

      <div class="p-4 rounded-t-[40px] border-t-4 border-[#B7D9BC] flex flex-col gap-3 items-center shrink-0" style="background: {bg || '#EFEBE9'}">
        <div class="w-full flex gap-3">
          <button onclick={prevSlide} disabled={!isFinished && currentSlide === 0}
            class="flex-1 py-3 px-4 rounded-2xl border border-stone-400 font-semibold text-base flex items-center justify-center gap-2 transition-all
              {!isFinished && currentSlide === 0 ? 'text-on-surface-variant btn-pop-gray opacity-60 cursor-not-allowed' : 'text-text-main btn-pop-gray'}">
            <span class="text-xl">←</span>
            {isFinished ? 'Baca Lagi' : 'Back'}
          </button>

          {#if !isFinished}
            <button onclick={toggleSpeech}
              class="py-3 px-4 rounded-2xl border-4 border-white flex items-center justify-center gap-1.5 text-sm font-semibold shadow-lg hover:scale-105 active:scale-95 transition-all shrink-0 text-white"
              class:bg-error={isSpeaking}
              style={!isSpeaking ? 'background: #FF8A50' : ''}>
              <span class="text-lg" class:animate-pulse={!isSpeaking}>
                {isSpeaking ? '⏹' : '🔊'}
              </span>
              {isSpeaking ? 'Stop' : 'Mainkan'}
            </button>
          {/if}

          <button onclick={nextSlide}
            class="flex-1 py-3 px-4 rounded-2xl border text-white font-semibold text-base flex items-center justify-center gap-2 transition-all btn-pop-brown">
            {isFinished ? 'Tutup' : currentSlide === totalSlides - 1 ? 'Selesai' : 'Next'}
            <span class="text-xl">
              {isFinished ? '✕' : currentSlide === totalSlides - 1 ? '✓' : '→'}
            </span>
          </button>
        </div>
      </div>

    </div>
  </div>
{/if}

<style>
  .btn-pop-brown {
    background-color: #8D6E63;
    box-shadow: 0 6px 0 #5D4037;
    transition: all 0.1s ease;
  }
  .btn-pop-brown:active {
    transform: translateY(6px);
    box-shadow: 0 0px 0 #5D4037;
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
    0% {
      transform: translateX(0) scale(1);
      opacity: 1;
    }
    100% {
      transform: translateX(-60%) scale(0.92);
      opacity: 0;
    }
  }

  @keyframes slideOutRight {
    0% {
      transform: translateX(0) scale(1);
      opacity: 1;
    }
    100% {
      transform: translateX(60%) scale(0.92);
      opacity: 0;
    }
  }

  @keyframes slideIn {
    0% {
      transform: translateX(0) scale(0.92);
      opacity: 0;
    }
    100% {
      transform: translateX(0) scale(1);
      opacity: 1;
    }
  }
</style>
