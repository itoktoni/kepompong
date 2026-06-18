<script>
  import NotificationDropdown from '../components/NotificationDropdown.svelte'
  import { unreadCount } from '../composables/useNotifications.js'
  import { syncStatus } from '../stores/syncStatusStore.js'

  const notificationEnabled = import.meta.env.VITE_NOTIFICATION_ENABLE === 'true'

  let { title = '', canInstallProp = false, userName = '', userEmail = '', userGender = '', onsync, oninstall, onprofile, onsettings, onbilling, onreferral, onlogout } = $props()

  let profileOpen = $state(false)
  let profileRef = $state()
  let notifOpen = $state(false)
  let sync = $state({ syncing: false, pending: 0 })
  let offline = $state(false)

  $effect(() => {
    const unsub = syncStatus.subscribe(v => { sync = v })
    return unsub
  })

  $effect(() => {
    offline = !navigator.onLine
    const on = () => { offline = false }
    const off = () => { offline = true }
    window.addEventListener('online', on)
    window.addEventListener('offline', off)
    return () => { window.removeEventListener('online', on); window.removeEventListener('offline', off) }
  })

  function getAvatarEmoji(gender) {
    return gender === 'female' ? '👩' : '👨'
  }

  $effect(() => {
    if (!profileOpen) return
    function handleClick(e) {
      if (profileRef && !profileRef.contains(e.target)) {
        profileOpen = false
      }
    }
    document.addEventListener('mousedown', handleClick)
    return () => document.removeEventListener('mousedown', handleClick)
  })
</script>

<header class="hidden lg:flex fixed top-0 right-0 z-40 h-[72px] bg-canvas-cream items-center justify-between px-6 border-b-4 border-[#B7D9BC] shadow-md"
  style="left: 280px;">
  <h1 class="text-headline-md text-text-main">{title}</h1>
  <div class="flex items-center gap-2">
    {#if canInstallProp}
      <button class="w-9 h-9 flex items-center justify-center rounded-full bg-white border-2 border-[#B7D9BC] shadow-sm text-primary hover:opacity-80 transition-opacity duration-200"
        onclick={() => oninstall?.()}>
        <span class="text-xl">🡻</span>
      </button>
    {/if}
    <button class="w-9 h-9 flex items-center justify-center rounded-full border-2 shadow-sm hover:opacity-80 transition-all duration-200
      {sync.syncing ? 'bg-primary text-white border-primary' : offline ? 'bg-amber-500 text-white border-amber-600' : 'bg-white text-primary border-[#B7D9BC]'}"
      onclick={() => onsync?.()}
      title="{sync.syncing ? 'Menyinkronkan...' : offline ? 'Mode Offline' : sync.pending > 0 ? sync.pending + ' perubahan tertunda' : 'Tersinkronkan'}">
      <span class="text-xl {sync.syncing ? 'animate-sync-spin' : ''}">{sync.syncing ? '🗘' : offline ? '⚠' : '🗘'}</span>
    </button>
    {#if notificationEnabled}
    <div class="relative">
      <button class="w-9 h-9 flex items-center justify-center rounded-full bg-white border-2 border-[#B7D9BC] shadow-sm text-primary hover:opacity-80 transition-opacity duration-200 relative"
        onclick={() => { notifOpen = !notifOpen; profileOpen = false }}>
        <span class="text-xl">🔔</span>
        {#if $unreadCount > 0}
          <span style="letter-spacing: 2px;" class="absolute -top-2 -right-2 min-w-[20px] h-[20px] flex items-center justify-center px-1.5 bg-error text-white text-[9px] rounded-full border-2 border-canvas-cream">{$unreadCount > 99 ? '99+' : $unreadCount}</span>
        {/if}
      </button>
      <NotificationDropdown show={notifOpen} onclose={() => notifOpen = false} />
    </div>
    {/if}
    <div class="relative" bind:this={profileRef}>
      <button class="w-9 h-9 flex items-center justify-center rounded-full bg-success-soft border-2 border-[#B7D9BC] shadow-sm text-base hover:opacity-80 transition-opacity duration-200"
        onclick={() => { profileOpen = !profileOpen; notifOpen = false }}>
        {getAvatarEmoji(userGender)}
      </button>
      {#if profileOpen}
        <div class="absolute right-0 top-full mt-2 bg-white rounded-xl shadow-xl border border-outline-variant py-1 w-56 z-50">
          <div class="px-4 py-2 border-b border-outline-variant">
            <p class="text-sm font-semibold truncate">{userName}</p>
            <p class="text-xs text-on-surface-variant truncate">{userEmail}</p>
          </div>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onprofile?.() }}>
            <span class="text-base">👤</span> Profile
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onsettings?.() }}>
            <span class="text-base">⚙️</span> Pengaturan
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onbilling?.() }}>
            <span class="text-base">💳</span> Billing
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onreferral?.() }}>
            <span class="text-base">➤</span> Affiliate
          </button>
          <hr class="border-outline-variant" />
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2 text-error" onclick={() => { profileOpen = false; onlogout?.() }}>
            <span class="text-base">🚪</span> Logout
          </button>
        </div>
      {/if}
    </div>
  </div>
</header>

<style>
  @keyframes sync-rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .animate-sync-spin {
    animation: sync-rotate 1s linear infinite;
    transform-origin: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
</style>
