<script>
  import { trackActivityView, deleteActivityById } from '../../services/api.js'
  import { isOffline } from '../../utils/network.js'
  import { queue } from '../../services/syncService.js'
  import { userRole } from '../../stores/authStore.js'
  import { resolveActivityCoverImage, resolveActivityImage } from '../../utils/images.js'
  import DevPanel from '../../components/DevPanel.svelte'

  let { item, bg, onclick, type, ondelete } = $props()

  let showReader = $state(false)
  let currentPageIndex = $state(0)
  let isFinished = $state(false)
  let isDragging = $state(false)
  let dragStartX = $state(0)
  let dragOffset = $state(0)
  let userRoleVal = $state('')
  let deletingActivity = $state(false)
  let devPanel = $state(null)

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })

  const pages = $derived(item.pages || item.data?.pages || item.steps || [])
  const materials = $derived(item.materials || item.data?.materials || [])
  const explanation = $derived(item.explanation || item.data?.explanation || '')
  const totalPages = $derived(pages.length)
  const currentPage = $derived(pages[currentPageIndex] || {})
  const currentPageImage = $derived(
    currentPage.image
      ? resolveActivityImage(type, item.slug || item.id, currentPage.image)
      : item.image
        ? resolveActivityImage(type, item.slug || item.id, `${currentPageIndex + 1}.png`)
        : null
  )

  const statusColors = {
    approved: { bg: '#E1F2E5', text: '#176c33', label: 'Approved' },
    pending: { bg: '#FFF3E0', text: '#E65100', label: 'Pending' },
    review: { bg: '#E3F2FD', text: '#0D47A1', label: 'Review' },
    rejected: { bg: '#FFEBEE', text: '#C62828', label: 'Rejected' },
  }

  const normalizedStatus = $derived(item.status?.toLowerCase() || '')

  const SWIPE_THRESHOLD = 50

  function getClientX(e) { return e.touches ? e.touches[0].clientX : e.clientX }
  function onDragStart(e) { isDragging = true; dragStartX = getClientX(e); dragOffset = 0 }
  function onDragMove(e) { if (!isDragging) return; dragOffset = getClientX(e) - dragStartX }
  function onDragEnd() {
    if (!isDragging) return
    isDragging = false
    if (dragOffset < -SWIPE_THRESHOLD) nextPage()
    else if (dragOffset > SWIPE_THRESHOLD) prevPage()
    dragOffset = 0
  }

  function openReader() {
    currentPageIndex = 0; isFinished = false; showReader = true
    window.__readerOpen = true
    if (devPanel) devPanel.initStatus()
    if (typeof window !== 'undefined') history.pushState({ reader: true }, '')
  }

  function closeReader() { showReader = false; window.__readerOpen = false }
  function prevPage() { if (isFinished) { isFinished = false; return } if (currentPageIndex > 0) currentPageIndex-- }
  function nextPage() {
    if (isFinished) { closeReader(); return }
    if (currentPageIndex < totalPages - 1) currentPageIndex++
    else {
      isFinished = true
      if (item.id) {
        if (isOffline()) queue('trackView', { id: item.id })
        else trackActivityView(item.id).then(d => { if (d) item.views = (d.views || 0) + 1 }).catch(() => {})
      }
    }
  }

  $effect(() => {
    function onClose() { if (showReader) { showReader = false; window.__readerOpen = false } }
    window.addEventListener('close-reader', onClose)
    return () => window.removeEventListener('close-reader', onClose)
  })

  async function handleDelete() {
    if (!item?.id) return
    if (!confirm(`Hapus "${item.title}"?`)) return
    deletingActivity = true
    try { await deleteActivityById(item.id); closeReader(); ondelete?.(item.id) }
    catch (e) { alert('Gagal menghapus.') }
    finally { deletingActivity = false }
  }

  function getPageText(page) {
    if (typeof page === 'string') return page
    return page.text || page.desc || page.step || ''
  }
</script>

<button class="group cursor-pointer w-full text-left" onclick={openReader}>
  <div class="relative transition-all duration-300 group-hover:-translate-y-1 group-hover:rotate-[1deg]">
    <div class="bg-white rounded-[24px] overflow-hidden shadow-lg border-4 relative"
      style="border-color: {userRoleVal === 'developer' && normalizedStatus && normalizedStatus !== 'approved' ? (statusColors[normalizedStatus]?.text || '#E65100') + '80' : '#B7D9BC'}">
      <div class="aspect-square p-2 overflow-hidden relative rounded-t-[20px]">
        {#if item.image}
          <img src={resolveActivityCoverImage(type, item.slug || item.id, item.image)} alt={item.title} class="w-full h-full object-cover group-hover:scale-110 rounded-2xl transition-transform duration-700" onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.style.display = 'flex' }} />
          <div class="w-full h-full flex-col items-center justify-center absolute inset-0 rounded-lg" style="background: {bg}; display: none">
            <span class="text-5xl mb-1">🎨</span>
            <p class="text-xs font-bold text-on-surface-variant">No Image</p>
          </div>
        {:else}
          <div class="w-full h-full flex flex-col items-center justify-center rounded-lg" style="background: {bg}">
            <span class="text-5xl mb-1">🎨</span>
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
          {#if totalPages > 0}
            <div class="bg-white/90 backdrop-blur-sm rounded-full mr-1 mb-1 px-2.5 py-1 text-[10px] font-bold shadow-sm" style="color: #2196F3">
              {totalPages} langkah
            </div>
          {/if}
        </div>
      </div>
      <div class="px-3 py-2.5 space-y-1">
        <h3 class="text-sm font-semibold text-on-surface line-clamp-2 leading-tight">{item.title}</h3>
        {#if item.desc}
          <p class="text-xs text-[#1565C0] font-medium line-clamp-2">{item.desc}</p>
        {/if}
      </div>
      <div class="px-3 py-2.5 flex items-center justify-between bg-success-soft">
        <div class="flex items-center gap-1.5 text-xs text-text-secondary">
          <span class="text-sm text-primary">👁</span>
          <span class="font-medium">{item.views || 0}</span>
        </div>
        <span class="text-xs font-bold text-primary">🎨 Proyek</span>
      </div>
    </div>
  </div>
</button>

{#if showReader}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center p-2 lg:p-4" onclick={closeReader}>
    <div class="w-full max-w-md bg-canvas-cream rounded-[40px] shadow-2xl border-8 border-[#B7D9BC] overflow-hidden flex flex-col h-[100dvh] lg:h-[852px] relative" onclick={(e) => e.stopPropagation()}>
      <div class="relative px-4 pt-4 pb-2 flex items-center gap-3 z-20 shrink-0">
        <div class="bg-primary text-on-primary w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-xs font-bold shrink-0">
          {isFinished ? '✓' : `${currentPageIndex + 1}/${totalPages}`}
        </div>
        <div class="flex-1 min-w-0 bg-primary text-on-primary px-4 py-2 rounded-2xl border-4 border-white shadow-md">
          <p class="text-base font-semibold truncate">{item.title}</p>
        </div>
        {#if userRoleVal === 'developer'}
          <button onclick={handleDelete} disabled={deletingActivity} class="w-11 h-11 rounded-full bg-error/80 text-white flex items-center justify-center text-base shrink-0 shadow-md hover:bg-error transition-colors disabled:opacity-50 border-4 border-white">🗑</button>
          <DevPanel bind:this={devPanel} {item} />
        {/if}
        <button onclick={closeReader} class="w-11 h-11 bg-error border-4 border-white text-white rounded-full flex items-center justify-center text-xl shadow-md hover:scale-105 active:scale-95 transition-all shrink-0">✕</button>
      </div>

      {#if !isFinished}
        <div class="flex-1 min-h-0 relative select-none" onmousedown={onDragStart} onmousemove={onDragMove} onmouseup={onDragEnd} onmouseleave={onDragEnd} ontouchstart={onDragStart} ontouchmove={onDragMove} ontouchend={onDragEnd}>
          <div class="h-full overflow-y-auto" style="transform: translateX({dragOffset}px); transition: {isDragging ? 'none' : 'transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94)'}">
            <div class="flex flex-col">
              {#if currentPageImage}
                <div class="w-full shrink-0">
                  <img src={currentPageImage} alt="" class="w-full h-auto object-contain" onerror={(e) => { e.target.style.display = 'none' }} />
                </div>
              {/if}
              <div class="px-4 py-4 space-y-3">
                {#if currentPageIndex === 0 && materials.length > 0}
                  <div class="bg-[#E3F2FD] rounded-2xl p-4 border-2 border-[#90CAF9] shadow-sm">
                    <p class="text-xs font-bold text-[#1565C0] mb-2">📋 Bahan yang dibutuhkan</p>
                    <ul class="space-y-1">
                      {#each materials as mat}
                        <li class="text-sm text-on-surface flex items-start gap-2">
                          <span class="text-[#1565C0] shrink-0">•</span>
                          <span>{mat}</span>
                        </li>
                      {/each}
                    </ul>
                  </div>
                {/if}
                <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] shadow-sm">
                  <p class="font-body-md text-body-md text-on-surface leading-relaxed">{getPageText(currentPage)}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      {:else}
        <div class="flex-1 min-h-0 overflow-y-auto">
          <div class="flex flex-col items-center px-5 py-8 max-w-lg mx-auto">
            <div class="w-20 h-20 rounded-full bg-success-soft border-4 border-[#B7D9BC] flex items-center justify-center text-5xl mb-6 shadow-md floating-illustration">🎨</div>
            <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main text-center mb-2">Selesai!</h2>
            <p class="font-body-md text-body-md text-on-surface-variant text-center mb-8">{item.title}</p>
            {#if explanation}
              <div class="w-full bg-[#E3F2FD] rounded-[28px] p-5 border-4 border-[#90CAF9] shadow-md mb-4">
                <div class="flex items-center gap-2 mb-3 justify-center">
                  <span class="w-8 h-8 rounded-full bg-[#BBDEFB] border-2 border-[#90CAF9] flex items-center justify-center text-base">💡</span>
                  <p class="text-[#1565C0] text-base font-bold">Penjelasan</p>
                </div>
                <p class="font-body-lg text-body-lg text-on-surface leading-relaxed text-center">{explanation}</p>
              </div>
            {/if}
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
          </div>
        </div>
      {/if}

      <div class="p-4 bg-success-soft rounded-t-[40px] border-t-4 border-[#B7D9BC] flex flex-col gap-4 items-center shrink-0">
        <div class="w-full flex gap-3">
          <button onclick={prevPage} disabled={!isFinished && currentPageIndex === 0}
            class="flex-1 py-3 px-4 rounded-2xl font-semibold text-base flex items-center justify-center gap-2 transition-all btn-pop-gray"
            class:opacity-60={!isFinished && currentPageIndex === 0} class:cursor-not-allowed={!isFinished && currentPageIndex === 0}>
            <span class="text-xl">←</span> {isFinished ? 'Baca Lagi' : 'Kembali'}
          </button>
          <button onclick={nextPage} class="flex-1 py-3 px-4 rounded-2xl text-white font-semibold text-base btn-pop-green flex items-center justify-center gap-2">
            {isFinished ? 'Tutup' : currentPageIndex === totalPages - 1 ? 'Selesai' : 'Lanjut'}
            <span class="text-xl">{isFinished ? '✕' : currentPageIndex === totalPages - 1 ? '✓' : '→'}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
{/if}

<style>
  .btn-pop-green { background-color: #6DBE7B; box-shadow: 0 4px 0 #176c33; transition: all 0.1s ease; }
  .btn-pop-green:active { transform: translateY(4px); box-shadow: 0 0px 0 #176c33; }
  .btn-pop-gray { background-color: #E5E7EB; box-shadow: 0 4px 0 #9CA3AF; transition: all 0.1s ease; }
  .btn-pop-gray:active { transform: translateY(4px); box-shadow: 0 0px 0 #9CA3AF; }
  @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-8px); } 100% { transform: translateY(0px); } }
  .floating-illustration { animation: float 4s ease-in-out infinite; }
</style>
