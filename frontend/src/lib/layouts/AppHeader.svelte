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
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
      </svg>
    </button>
    <h1 class="font-headline-lg-mobile md:font-headline-lg md:text-headline-lg text-text-main">{title}</h1>
  </div>
  <div class="flex items-center gap-2">
    {#if canInstallProp}
      <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-white border-2 border-[#B7D9BC] shadow-sm text-primary hover:opacity-80 transition-opacity duration-200"
        onclick={() => oninstall?.()}>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
      </button>
    {/if}
    <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full border-2 shadow-sm hover:opacity-80 transition-all duration-200
      {sync.syncing ? 'bg-primary text-white border-primary' : offline ? 'bg-amber-500 text-white border-amber-600' : 'bg-white text-primary border-[#B7D9BC]'}"
      onclick={() => onsync?.()}
      title="{sync.syncing ? 'Menyinkronkan...' : offline ? 'Mode Offline' : sync.pending > 0 ? sync.pending + ' perubahan tertunda' : 'Tersinkronkan'}">
      {#if sync.syncing}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 animate-sync-spin">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182" />
        </svg>
      {:else if offline}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
        </svg>
      {:else}
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z" />
        </svg>
      {/if}
    </button>
    {#if notificationEnabled}
    <div class="relative">
      <button class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full bg-white border-2 border-[#B7D9BC] shadow-sm text-primary hover:opacity-80 transition-opacity duration-200 relative"
        onclick={() => { notifOpen = !notifOpen; profileOpen = false }}>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
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
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-primary">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
      </button>
      {#if profileOpen}
        <div class="absolute right-0 top-full mt-2 bg-white rounded-xl shadow-xl border border-outline-variant py-1 w-52 z-50">
          <div class="px-4 py-2 border-b border-outline-variant">
            <p class="text-sm font-semibold truncate">{userName}</p>
            <p class="text-xs text-on-surface-variant truncate">{userEmail}</p>
          </div>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onprofile?.() }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            Profile
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onsettings?.() }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            Pengaturan
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onbilling?.() }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
            </svg>
            Billing
          </button>
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2" onclick={() => { profileOpen = false; onreferral?.() }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
            </svg>
            Affiliate
          </button>
          <hr class="border-outline-variant" />
          <button class="w-full text-left px-4 py-2.5 text-sm hover:bg-surface-container-low flex items-center gap-2 text-error" onclick={() => { profileOpen = false; onlogout?.() }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
            </svg>
            Logout
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
