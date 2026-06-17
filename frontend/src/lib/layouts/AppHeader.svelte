<script>
  import NotificationDropdown from '../components/NotificationDropdown.svelte'
  import { unreadCount } from '../composables/useNotifications.js'
  import { syncStatus } from '../stores/syncStatusStore.js'

  const notificationEnabled = import.meta.env.VITE_NOTIFICATION_ENABLE === 'true'

  let { title = '', activeTab = 'pilar', userName = '', userGender = '', userEmail = '', canInstallProp = false, onswitch, onsync, oninstall, onprofile, onsettings, onbilling, onreferral, onlogout, onopenMobileMenu } = $props()

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

<header class="w-full top-0 sticky z-40 bg-canvas-cream flex justify-between items-center px-4 py-3 lg:hidden rounded-b-[32px] border-b-4 border-[#B7D9BC] shadow-md">
  <div class="flex items-center gap-3">
    <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-primary text-white shadow-md hover:bg-primary/90 transition-colors duration-200"
      onclick={() => onopenMobileMenu?.()}>
      <span class="material-symbols-outlined">menu</span>
    </button>
    <h1 class="font-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-text-main">{title}</h1>
  </div>
  <div class="flex items-center gap-2">
    {#if canInstallProp}
      <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-white border-2 border-[#B7D9BC] shadow-sm text-primary hover:opacity-80 transition-opacity duration-200"
        onclick={() => oninstall?.()}>
        <span class="material-symbols-outlined">install_mobile</span>
      </button>
    {/if}
    <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full border-2 shadow-sm hover:opacity-80 transition-all duration-200
      {sync.syncing ? 'bg-primary text-white border-primary' : offline ? 'bg-amber-500 text-white border-amber-600' : 'bg-white text-primary border-[#B7D9BC]'}"
      onclick={() => onsync?.()}
      title="{sync.syncing ? 'Menyinkronkan...' : offline ? 'Mode Offline' : sync.pending > 0 ? sync.pending + ' perubahan tertunda' : 'Tersinkronkan'}">
      <span class="material-symbols-outlined {sync.syncing ? 'animate-sync-spin' : ''}">{sync.syncing ? 'sync' : offline ? 'cloud_off' : 'cloud_sync'}</span>
    </button>
    {#if notificationEnabled}
    <div class="relative">
      <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-white border-2 border-[#B7D9BC] shadow-sm text-primary hover:opacity-80 transition-opacity duration-200 relative"
        onclick={() => { notifOpen = !notifOpen; profileOpen = false }}>
        <span class="material-symbols-outlined">notifications</span>
        {#if $unreadCount > 0}
          <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center px-1 bg-error text-white text-[10px] font-bold rounded-full border-2 border-canvas-cream">{$unreadCount > 99 ? '99+' : $unreadCount}</span>
        {/if}
      </button>
      <NotificationDropdown show={notifOpen} onclose={() => notifOpen = false} />
    </div>
    {/if}
    <div class="relative" bind:this={profileRef}>
      <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-success-soft border-2 border-[#B7D9BC] shadow-sm text-lg hover:opacity-80 transition-opacity duration-200"
        onclick={() => { profileOpen = !profileOpen; notifOpen = false }}>
        {getAvatarEmoji(userGender)}
      </button>
      {#if profileOpen}
        <div class="absolute right-0 top-full mt-2 bg-white rounded-xl shadow-xl border border-outline-variant py-1 w-52 z-50">
          <div class="px-4 py-2 border-b border-outline-variant">
            <p class="text-sm font-semibold truncate">{userName}</p>
            <p class="text-xs text-on-surface-variant truncate">{userEmail}</p>
          </div>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onprofile?.() }}>
            <span class="material-symbols-outlined text-base">person</span> Profile
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onsettings?.() }}>
            <span class="material-symbols-outlined text-base">settings</span> Pengaturan
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onbilling?.() }}>
            <span class="material-symbols-outlined text-base">payments</span> Billing
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onreferral?.() }}>
            <span class="material-symbols-outlined text-base">share</span> Affiliate
          </button>
          <hr class="border-outline-variant" />
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2 text-error" onclick={() => { profileOpen = false; onlogout?.() }}>
            <span class="material-symbols-outlined text-base">logout</span> Logout
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
