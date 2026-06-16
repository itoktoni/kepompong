<script>
  import { onMount, onDestroy } from 'svelte'
  import { trackActivityView } from '../../services/api.js'
  import { userRole } from '../../stores/authStore.js'
  import DevPanel from '../../components/DevPanel.svelte'

  let { item, bg, onclick } = $props()

  let showReader = $state(false)
  let currentPageIndex = $state(0)
  let isFinished = $state(false)
  let isDragging = $state(false)
  let dragStartX = $state(0)
  let userRoleVal = $state('')
  let devPanel = $state(null)

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })
  let dragOffset = $state(0)
  let isSpeakingNarrator = $state(false)
  let autoNarrate = $state(false)
  let itemData = $state(null)

  const SWIPE_THRESHOLD = 50

  const pages = $derived(itemData?.pages || item.pages || item.data?.pages || [])
  const roles = $derived(itemData?.roles || item.roles || item.data?.roles || [])
  const totalPages = $derived(pages.length)
  const currentPage = $derived(pages[currentPageIndex] || {})

  const rightRoles = ['Pembeli', 'Pasien', 'Anak']
  const emojiMap = { Pedagang: '👨‍🍳', Dokter: '👨‍⚕️', Pembeli: '👩', Pasien: '🧒', Ibu: '👩', Anak: '🧒', Nenek: '👵' }

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
    if (isFinished) { showReader = false; return }
    stopSpeech()
    if (currentPageIndex < totalPages - 1) currentPageIndex++
    else isFinished = true
  }

  function backToLastPage() { isFinished = false }

  async function openReader() {
    currentPageIndex = 0
    isFinished = false
    roleColors = {}
    colorIndex = 0
    autoNarrate = false
    showReader = true
    if (devPanel) devPanel.initStatus()
    if (item.id) {
      try {
        const detail = await trackActivityView(item.id)
        if (detail) {
          const { data, ...rest } = detail
          itemData = { ...item, ...rest, ...(data || {}) }
        }
      } catch (e) { /* ignore */ }
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

  onDestroy(() => stopSpeech())
</script>

<button class="group cursor-pointer w-full text-left"
  onclick={openReader}>
  <div class="relative transition-all duration-300 group-hover:-translate-y-1 group-hover:rotate-[-1deg]">
    <div class="bg-white rounded-[24px] overflow-hidden shadow-lg border-4 border-[#B7D9BC] relative">
      <div class="aspect-square p-2 overflow-hidden relative rounded-t-[20px]">
        {#if item.image}
          <img src={item.image} alt={item.title} class="w-full h-full object-cover group-hover:scale-110 rounded-2xl transition-transform duration-700" />
        {:else}
          <div class="w-full h-full flex items-center justify-center rounded-lg" style="background: {bg}">
            <span class="text-6xl">{item.emoji || '🎭'}</span>
          </div>
        {/if}
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
          <span class="material-symbols-outlined text-sm text-primary">visibility</span>
          <span class="font-medium">{item.views || 0}</span>
        </div>
        {#if roles.length}
          <div class="flex items-center gap-1.5 text-xs text-text-secondary">
            <span class="material-symbols-outlined text-sm text-primary">groups</span>
            <span class="font-medium">{roles.length} peran</span>
          </div>
        {/if}
      </div>
    </div>
  </div>
</button>

{#if showReader}
  <div class="fixed inset-0 z-[100] bg-black/40 flex items-end lg:items-center justify-center p-2 lg:p-4">
    <div class="w-full max-w-md bg-canvas-cream rounded-[40px] shadow-2xl border-8 border-[#B7D9BC] overflow-hidden flex flex-col h-[100dvh] lg:h-[852px] relative">

      <div class="relative px-4 pt-4 pb-2 flex items-center gap-3 z-20 shrink-0">
        <div class="bg-primary text-on-primary w-11 h-11 rounded-full border-4 border-white shadow-md flex items-center justify-center text-xs font-bold shrink-0">
          {isFinished ? '✓' : `${currentPageIndex + 1}/${totalPages}`}
        </div>
        <div class="flex-1 min-w-0 bg-primary text-on-primary px-4 py-2 rounded-2xl border-4 border-white shadow-md">
          <p class="text-base font-semibold truncate">{item.title}</p>
        </div>
        {#if userRoleVal === 'developer'}
          <DevPanel bind:this={devPanel} {item} />
        {/if}
        <button onclick={() => { stopSpeech(); showReader = false }}
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
              {#if currentPage.image}
                <div class="w-full shrink-0">
                  <img src={currentPage.image} alt={currentPage.narrator || ''} class="w-full h-[220px] object-cover" />
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
                        <span class="material-symbols-outlined text-lg">{isSpeakingNarrator ? 'stop' : 'volume_up'}</span>
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
          </div>
        </div>
      {/if}

      <div class="p-4 bg-success-soft rounded-t-[40px] border-t-4 border-[#B7D9BC] flex flex-col gap-4 items-center shrink-0">
        <div class="w-full flex gap-3">
          <button onclick={isFinished ? backToLastPage : prevPage}
            disabled={!isFinished && currentPageIndex === 0}
            class="flex-1 py-3 px-4 rounded-2xl font-semibold text-base flex items-center justify-center gap-2 transition-all btn-pop-gray"
            class:opacity-60={!isFinished && currentPageIndex === 0}
            class:cursor-not-allowed={!isFinished && currentPageIndex === 0}>
            <span class="material-symbols-outlined text-xl">arrow_back</span>
            {isFinished ? 'Baca Lagi' : 'Kembali'}
          </button>
          <button onclick={nextPage}
            class="flex-1 py-3 px-4 rounded-2xl text-white font-semibold text-base btn-pop-green flex items-center justify-center gap-2">
            {isFinished ? 'Tutup' : currentPageIndex === totalPages - 1 ? 'Selesai ✨' : 'Lanjut'}
            <span class="material-symbols-outlined text-xl">{isFinished ? 'close' : currentPageIndex === totalPages - 1 ? 'check' : 'arrow_forward'}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
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
