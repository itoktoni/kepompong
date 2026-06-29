<script>
  import { onMount, onDestroy } from 'svelte'
  import { resolveActivityCoverImage, resolveActivityImage } from '../../utils/images.js'
  import { trackActivityView, deleteActivityById } from '../../services/api.js'
  import { isOffline } from '../../utils/network.js'
  import { queue } from '../../services/syncService.js'
  import { userRole, user } from '../../stores/authStore.js'
  import ActivityEditor from '../../components/ActivityEditor.svelte'
  import { generatePdf } from './pdf/index.js'

  let { item, bg, onclick, type } = $props()

  let showReader = $state(false)
  let currentPageIndex = $state(0)
  let isFinished = $state(false)
  let isDragging = $state(false)
  let dragStartX = $state(0)
  let userRoleVal = $state('')
  let currentUserId = $state(null)
  let showEditor = $state(false)

  let downloading = $state(false)

  async function handleDownload() {
    if (downloading) return
    downloading = true
    try { await generatePdf(item, type) }
    catch (e) { console.error('PDF download failed:', e) }
    finally { downloading = false }
  }

  $effect(() => {
    const unsub1 = userRole.subscribe(v => userRoleVal = v)
    const unsub2 = user.subscribe(v => currentUserId = v?.id || null)
    return () => { unsub1(); unsub2() }
  })

  const isOwner = $derived(userRoleVal === 'developer' || (currentUserId && item.created_by === currentUserId))

  const statusColors = {
    approved: { bg: '#E1F2E5', text: '#176c33', label: 'Approved' },
    pending: { bg: '#FFF3E0', text: '#E65100', label: 'Pending' },
    review: { bg: '#E3F2FD', text: '#0D47A1', label: 'Review' },
    rejected: { bg: '#FFEBEE', text: '#C62828', label: 'Rejected' },
  }

  let dragOffset = $state(0)
  let isSpeakingNarrator = $state(false)
  let autoNarrate = $state(false)
  let itemData = $state(null)

  const SWIPE_THRESHOLD = 50

  const pages = $derived(itemData?.pages || item.pages || item.data?.pages || [])
  const roles = $derived(itemData?.roles || item.roles || item.data?.roles || [])
  const totalPages = $derived(pages.length)
  const currentPage = $derived(pages[currentPageIndex] || {})
  const currentPageImage = $derived(
    currentPage.image
      ? resolveActivityImage(type, item.slug || item.id, currentPage.image)
      : item.image
        ? resolveActivityImage(type, item.slug || item.id, `${currentPageIndex + 1}.png`)
        : null
  )

  const rightRoles = ['Pembeli', 'Pasien', 'Anak', 'Pelanggan', 'Murid', 'Penumpang', 'Teman', 'Bayi', 'Penerima', 'Pemilik Mobil', 'Penonton', 'Pengunjung', 'Warga', 'Ko-pilot']
  const emojiMap = { Pedagang: '👨‍🍳', Dokter: '👨‍⚕️', Pembeli: '👩', Pasien: '🧒', Ibu: '👩', Anak: '🧒', Nenek: '👵', Koki: '👨‍🍳', Pelayan: '🤵', Pelanggan: '🛒', Kasir: '🤑', Pemadam: '🧑‍🚒', Polisi: '👮', Warga: '😟', Guru: '👩‍🏫', Murid: '😊', Pilot: '✈️', 'Ko-pilot': '😊', Penumpang: '😊', Petani: '🧑‍🌾', Teman: '😄', Perawat: '👩‍⚕️', Bayi: '👶', 'Tukang Pos': '🏣', Penerima: '😊', Montir: '🔧', 'Pemilik Mobil': '🚗', Astronot: '🚀', Komandan: '👨‍🚀', Penyanyi: '🎤', Penonton: '👏', Pelukis: '🎨', Satpam: '🛡️', Pengunjung: '😊', 'Tukang Kebun': '🌻', Kakak: '👦', Adik: '😢', 'Anak Kecil': '😢', 'Teman Baru': '😄' }

  const roleColorPalette = [
    { bg: '#E8F5E9', border: '#B7D9BC', text: '#176c33' },
    { bg: '#FFF3E0', border: '#F4A261', text: '#8e4e14' },
    { bg: '#E3F2FD', border: '#7CC6FE', text: '#004568' },
    { bg: '#F3E5F5', border: '#CE93D8', text: '#6A1B9A' },
    { bg: '#FFEBEE', border: '#EF9A9A', text: '#ba1a1a' },
    { bg: '#E0F2F1', border: '#80CBC4', text: '#00695C' },
    { bg: '#FFF8E1', border: '#FFE082', text: '#F57F17' },
  ]

  let roleColors = {}
  let colorIndex = 0

  function getRoleColor(role) {
    if (!roleColors[role]) {
      roleColors[role] = roleColorPalette[colorIndex % roleColorPalette.length]
      colorIndex++
    }
    return roleColors[role]
  }

  function isRightRole(role) { return rightRoles.includes(role) }
  function getRoleEmoji(role) { return emojiMap[role] || '👤' }

  function getClientX(e) { return e.touches ? e.touches[0].clientX : e.clientX }

  function onDragStart(e) {
    isDragging = true
    dragStartX = getClientX(e)
    dragOffset = 0
  }

  function onDragMove(e) {
    if (!isDragging) return
    dragOffset = getClientX(e) - dragStartX
  }

  function onDragEnd() {
    if (!isDragging) return
    isDragging = false
    if (dragOffset < -SWIPE_THRESHOLD) nextPage()
    else if (dragOffset > SWIPE_THRESHOLD) prevPage()
    dragOffset = 0
  }

  function goToPage(index) { stopSpeech(); currentPageIndex = index }

  function prevPage() {
    if (currentPageIndex > 0) { stopSpeech(); currentPageIndex-- }
  }

  function nextPage() {
    if (isFinished) { showReader = false; window.__readerOpen = false; return }
    stopSpeech()
    if (currentPageIndex < totalPages - 1) currentPageIndex++
    else {
      isFinished = true
      if (item.id) {
        if (isOffline()) { queue('trackView', { id: item.id }) }
        else { trackActivityView(item.id).then(d => { if (d) item.views = (d.views || 0) + 1 }).catch(() => {}) }
      }
    }
  }

  function backToLastPage() { isFinished = false }

  async function handleDelete() {
    if (!confirm('Hapus aktivitas ini?')) return
    try {
      await deleteActivityById(item.id)
      showReader = false
      window.__readerOpen = false
      item._deleted = true
    } catch (e) {
      alert('Gagal menghapus: ' + (e.message || e))
    }
  }

  async function openReader() {
    currentPageIndex = 0
    isFinished = false
    roleColors = {}
    colorIndex = 0
    autoNarrate = false
    showReader = true
    window.__readerOpen = true
    itemData = item
    if (typeof window !== 'undefined') {
      history.pushState({ reader: true }, '')
    }
  }

  function speakNarrator(manual = false) {
    if (!('speechSynthesis' in window)) return
    if (!currentPage.narrator) return
    if (manual) autoNarrate = true
    stopSpeech()
    const u = new SpeechSynthesisUtterance(currentPage.narrator)
    u.lang = 'id-ID'; u.rate = 0.9; u.pitch = 1.1
    u.onend = () => {
      isSpeakingNarrator = false
      speakNextDialog(0)
    }
    u.onerror = () => { isSpeakingNarrator = false }
    speechSynthesis.speak(u)
    isSpeakingNarrator = true
  }

  function speakNextDialog(index) {
    if (!('speechSynthesis' in window)) return
    const dialog = currentPage.dialog || []
    if (index >= dialog.length) return
    stopSpeech()
    const d = dialog[index]
    const u = new SpeechSynthesisUtterance(`${d.role} berkata: ${d.text}`)
    u.lang = 'id-ID'; u.rate = 0.9; u.pitch = 1.1
    u.onend = () => { isSpeakingNarrator = false; speakNextDialog(index + 1) }
    u.onerror = () => { isSpeakingNarrator = false }
    speechSynthesis.speak(u)
    isSpeakingNarrator = true
  }

  $effect(() => {
    const _page = currentPageIndex
    if (showReader && !isFinished && autoNarrate) {
      setTimeout(() => speakNarrator(), 300)
    }
  })

  function stopSpeech() {
    if ('speechSynthesis' in window) speechSynthesis.cancel()
    isSpeakingNarrator = false
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
      if (e.key === 'ArrowLeft') { e.preventDefault(); prevPage() }
      else if (e.key === 'ArrowRight') { e.preventDefault(); nextPage() }
    }
    window.addEventListener('close-reader', onClose)
    window.addEventListener('keydown', onKeydown)
    return () => { window.removeEventListener('close-reader', onClose); window.removeEventListener('keydown', onKeydown) }
  })

  onDestroy(() => stopSpeech())
</script>

{#if !item._deleted}
<button class="group cursor-pointer w-full text-left"
  onclick={openReader}>
  <div class="relative transition-all duration-300 group-hover:-translate-y-1 group-hover:rotate-[-1deg]">
    <div class="bg-white rounded-[24px] overflow-hidden shadow-lg border-4 relative"
      style="border-color: {userRoleVal === 'developer' && item.status && item.status !== 'approved' ? (statusColors[item.status]?.text || '#E65100') + '80' : '#B7D9BC'}">
      <div class="aspect-square p-2 overflow-hidden relative rounded-t-[20px]">
        {#if item.image}
          <img src={resolveActivityCoverImage(type, item.slug || item.id, item.image)} alt={item.title} class="w-full h-full object-cover group-hover:scale-110 rounded-2xl transition-transform duration-700" loading="lazy" decoding="async" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
          <div class="w-full h-full flex-col items-center justify-center absolute inset-0 rounded-lg" style="background: {bg}; display: none">
            <span class="text-5xl mb-1">🖼️</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {:else}
          <div class="w-full h-full flex flex-col items-center justify-center rounded-lg" style="background: {bg}">
            <span class="text-5xl mb-1">🖼️</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {/if}
        <div class="absolute top-2 left-2">
          {#if (item.views || 0) < 10}
            <div class="bg-white/90 backdrop-blur-sm rounded-full ml-1 mt-1.5 px-2.5 py-1 text-[10px] font-bold text-primary shadow-sm">
              🆕 NEW
            </div>
          {/if}
        </div>
        <div class="absolute bottom-2 left-2">
          {#if isOwner && item.status && item.status !== 'approved'}
            {@const sc = statusColors[item.status] || statusColors.pending}
            <div class="rounded-full ml-1 mb-1 px-2.5 py-1 text-[10px] font-bold shadow-sm" style="background: {sc.bg}; color: {sc.text}">
              {sc.label}
            </div>
          {/if}
        </div>
        <div class="absolute bottom-2 right-2">
          {#if roles.length}
            <div class="bg-white/90 backdrop-blur-sm rounded-full mr-1 mb-1 px-2.5 py-1 text-[10px] font-bold text-primary shadow-sm">
               {roles.length} Peran
            </div>
          {/if}
        </div>
      </div>
      <div class="px-3 py-2.5 space-y-1">
        <h3 class="text-sm font-semibold text-on-surface line-clamp-2 leading-tight">{item.title}</h3>
        {#if item.desc}
          <p class="text-[10px] text-on-surface-variant line-clamp-1">{item.desc}</p>
        {/if}
      </div>
      <div class="px-3 py-2.5 flex items-center justify-between bg-success-soft">
        <div class="flex items-center gap-1.5 text-xs text-text-secondary">
          <span class="text-sm text-primary">👁</span>
          <span class="font-medium">{item.views || 0}</span>
        </div>
        <div class="flex items-center gap-2">
          {#if roles.length}
            <div class="flex items-center gap-1.5 text-xs text-text-secondary">
              <span class="text-sm text-primary">👥</span>
              <span class="font-medium">{roles.length} peran</span>
            </div>
          {/if}
        <span onclick={(e) => { e.stopPropagation(); handleDownload() }}
          class="font-medium flex items-center justify-center text-xs  transition-colors cursor-pointer shrink-0 {downloading ? 'opacity-50 pointer-events-none' : ''}"
          title="Download PDF" role="button" tabindex="0">
          {downloading ? 'Waiting...' : 'Download'}
        </span>
        </div>
      </div>
    </div>
  </div>
</button>
{/if}

{#if showReader}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center p-2 lg:p-4" onclick={() => { stopSpeech(); showReader = false; window.__readerOpen = false }}>
    <div class="w-full max-w-md bg-canvas-cream rounded-[40px] shadow-2xl border-8 border-[#B7D9BC] overflow-hidden flex flex-col h-[100dvh] lg:h-[852px] relative" onclick={(e) => e.stopPropagation()}>

      <div class="relative px-4 pt-4 pb-2 flex items-center gap-3 z-20 shrink-0">
        <div class="bg-primary text-on-primary w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-xs font-bold shrink-0">
          {isFinished ? '✓' : `${currentPageIndex + 1}/${totalPages}`}
        </div>
        <div class="flex-1 min-w-0 bg-primary text-on-primary px-4 py-2 rounded-2xl border-4 border-white shadow-md">
          <p class="text-base font-semibold truncate">{item.title}</p>
        </div>
        {#if userRoleVal === 'developer'}
          <button onclick={handleDelete}
            class="w-11 h-11 bg-error/80 border-4 border-white text-white rounded-full flex items-center justify-center text-xl shadow-md hover:scale-105 active:scale-95 transition-all shrink-0">
            🗑
          </button>
        {/if}
        {#if isOwner}
          <button onclick={() => { showEditor = true }}
            class="w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-base shrink-0 hover:scale-105 active:scale-95 transition-all"
            style="background: #E1F2E5; color: #176c33">
            ✏️
          </button>
        {/if}
        <button onclick={() => { stopSpeech(); showReader = false; window.__readerOpen = false }}
          class="w-11 h-11 bg-error border-4 border-white text-white rounded-full flex items-center justify-center text-xl shadow-md hover:scale-105 active:scale-95 transition-all shrink-0">
          ✕
        </button>
      </div>

      {#if !isFinished}
        <div class="flex-1 min-h-0 relative select-none"
          onmousedown={onDragStart} onmousemove={onDragMove} onmouseup={onDragEnd} onmouseleave={onDragEnd}
          ontouchstart={onDragStart} ontouchmove={onDragMove} ontouchend={onDragEnd}>
          <div class="h-full overflow-y-auto"
            style="transform: translateX({dragOffset}px); transition: {isDragging ? 'none' : 'transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94)'}">
            <div class="flex flex-col">
              {#if currentPageImage}
                <div class="w-full shrink-0">
                  <img src={currentPageImage} alt={currentPage.narrator || ''} class="w-full h-auto object-contain" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
                  <div class="w-full h-[220px] flex-col items-center justify-center bg-success-soft rounded-b-2xl hidden" style="display: none">
                    <span class="text-4xl">🖼️</span>
                    <p class="text-xs font-bold text-on-surface-variant mt-1">No Image</p>
                  </div>
                </div>
              {/if}
              <div class="px-4 py-4 space-y-2">
                {#if currentPage.narrator}
                  <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                      <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-xs">📖</span>
                        <span class="font-label-lg text-label-lg text-primary">Narator</span>
                      </div>
                      <button onclick={() => speakNarrator(true)} class="w-8 h-8 rounded-full flex items-center justify-center transition-all border-2"
                        class:bg-error={isSpeakingNarrator} class:text-on-error={isSpeakingNarrator} class:border-error={isSpeakingNarrator}
                        class:bg-success-soft={!isSpeakingNarrator} class:text-primary={!isSpeakingNarrator} class:border-[#B7D9BC]={!isSpeakingNarrator}>
                        <span class="text-lg">{isSpeakingNarrator ? '⏹' : '🔊'}</span>
                      </button>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface leading-relaxed">{currentPage.narrator}</p>
                  </div>
                {/if}

                {#each (currentPage.dialog || []) as d}
                  <div class="flex gap-3" class:flex-row-reverse={isRightRole(d.role)}>
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shrink-0 border-2 border-white shadow-sm"
                      style="background: {getRoleColor(d.role).bg}">
                      {getRoleEmoji(d.role)}
                    </div>
                    <div class="max-w-[75%]">
                      <span class="text-xs font-bold mb-1 block" class:text-right={isRightRole(d.role)}
                        style="color: {getRoleColor(d.role).text}">
                        {d.role}
                      </span>
                      <div class="rounded-2xl px-4 py-3 border-2"
                        class:rounded-tr-sm={isRightRole(d.role)} class:rounded-tl-sm={!isRightRole(d.role)}
                        style="background: {getRoleColor(d.role).bg}; border-color: {getRoleColor(d.role).border}">
                        <p class="text-sm leading-relaxed" style="color: {getRoleColor(d.role).text}">{d.text}</p>
                      </div>
                    </div>
                  </div>
                {/each}
              </div>
            </div>
          </div>
        </div>

      {:else}
        <div class="flex-1 min-h-0 overflow-y-auto">
          <div class="flex flex-col items-center px-5 py-8 max-w-lg mx-auto">
            <div class="w-20 h-20 rounded-full bg-success-soft border-4 border-[#B7D9BC] flex items-center justify-center text-5xl mb-6 shadow-md floating-illustration">🎭</div>
            <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main text-center mb-2">Selesai!</h2>
            <p class="font-body-md text-body-md text-on-surface-variant text-center mb-8">{item.title}</p>
            <div class="w-full grid grid-cols-2 gap-3 mb-6">
              {#each roles as role}
                <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] shadow-sm text-center">
                  <div class="text-3xl mb-2">{role.emoji || '👤'}</div>
                  <p class="font-label-lg text-label-lg text-primary">{role.name || role}</p>
                  {#if role.desc}
                    <p class="text-xs text-on-surface-variant mt-1">{role.desc}</p>
                  {/if}
                </div>
              {/each}
            </div>
            {#if item.moral}
              <div class="w-full bg-white rounded-[28px] p-5 border-4 border-[#B7D9BC] shadow-md relative">
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[14px] border-l-transparent border-r-[14px] border-r-transparent border-b-[14px] border-b-white"></div>
                <div class="flex items-center gap-2 mb-3 justify-center">
                  <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">💬</span>
                  <p class="text-primary text-base font-bold">Pelajaran</p>
                </div>
                <p class="font-body-lg text-body-lg text-on-surface leading-relaxed text-center">{item.moral}</p>
              </div>
            {/if}
            {#if item.creator}
              <div class="bg-white rounded-[24px] border-2 border-[#B7D9BC] p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                  <span class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-sm text-primary">👤</span>
                  </span>
                  <p class="text-xs font-bold text-primary">Dibuat oleh</p>
                </div>
                <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{item.creator}</p>
              </div>
            {/if}
          </div>
        </div>
      {/if}
      <div class="p-4 rounded-t-[40px] border-t-4 border-[#B7D9BC] flex flex-col gap-3 items-center shrink-0" style="background: {bg || '#E8F5E9'}">
        <div class="w-full flex gap-3">
          <button onclick={isFinished ? backToLastPage : prevPage}
            disabled={!isFinished && currentPageIndex === 0}
            class="flex-1 py-3 px-4 rounded-2xl border border-stone-400 font-semibold text-base flex items-center justify-center gap-2 transition-all
              {!isFinished && currentPageIndex === 0 ? 'text-on-surface-variant btn-pop-gray opacity-60 cursor-not-allowed' : 'text-text-main btn-pop-gray'}">
            <span class="text-xl">←</span>
            {isFinished ? 'Baca Lagi' : 'Back'}
          </button>

          {#if !isFinished}
            <button onclick={() => speakNarrator(true)}
              class="py-3 px-4 rounded-2xl border-4 border-white flex items-center justify-center gap-1.5 text-sm font-semibold shadow-lg hover:scale-105 active:scale-95 transition-all shrink-0 text-white"
              class:bg-error={isSpeakingNarrator}
              class:text-on-error={isSpeakingNarrator}
              style={!isSpeakingNarrator ? 'background: #176c33' : ''}>
              <span class="text-lg" class:animate-pulse={!isSpeakingNarrator}>
                {isSpeakingNarrator ? '⏹' : '🔊'}
              </span>
              {isSpeakingNarrator ? 'Stop' : 'Play'}
            </button>
          {/if}

          <button onclick={nextPage}
            class="flex-1 py-3 px-4 rounded-2xl border text-white font-semibold text-base flex items-center justify-center gap-2 transition-all btn-pop-green">
            {isFinished ? 'Tutup' : currentPageIndex === totalPages - 1 ? 'Selesai' : 'Next'}
            <span class="text-xl">
              {isFinished ? '✕' : currentPageIndex === totalPages - 1 ? '✓' : '→'}
            </span>
          </button>
        </div>
      </div>
      </div>
    </div>
  {/if}

{#if showEditor}
  <ActivityEditor {item} type={type}
    onsave={() => { showEditor = false }}
    ondelete={handleDelete}
    onclose={() => { showEditor = false }} />
{/if}

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
    background-color: #E5E7EB;
    box-shadow: 0 4px 0 #9CA3AF;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(4px);
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
</style>
