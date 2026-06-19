<script>
  import { sidebarNav } from '../data/sidebarNav.js'
  import { userRole } from '../stores/authStore.js'

  let { activeTab = 'pilar', userName = 'Bunda', userGender = '', showMobileMenu = false, onswitch, oncloseMobile } = $props()

  let userRoleVal = $state('')

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })

  const visibleNav = $derived(sidebarNav.filter(t => !t.dev || userRoleVal === 'developer'))

  function iconStyle(tabId) {
    return activeTab === tabId ? 'scale-110' : ''
  }

  function handleSwitch(tabId) {
    onswitch?.(tabId)
    oncloseMobile?.()
  }
</script>

<aside class="desktop-sidebar hidden lg:flex fixed left-0 top-0 h-full w-[280px] z-50 flex-col border-r-4 border-[#B7D9BC]"
  style="background: #FFF9F3;">
  <div class="p-3 pb-4">
    <div class="flex items-center gap-3 bg-canvas-cream rounded-[20px] p-4 border-4 border-[#B7D9BC] shadow-md">
      <div>
        <h2 class="font-headline-md text-headline-md text-text-main">Halo {userName}!</h2>
        <p class="text-sm text-on-surface-variant">Selamat datang</p>
      </div>
    </div>
  </div>

  <div class="px-4 mb-2">
    <p class="text-xs font-bold text-primary uppercase tracking-wider px-3">Menu</p>
  </div>

  <nav class="flex-1 px-3 space-y-2">
    {#each visibleNav as tab}
      <button
        class="w-full flex text-sm items-center gap-3 px-4 py-3 rounded-2xl cursor-pointer
          {activeTab === tab.id
            ? 'bg-primary text-on-primary shadow-md'
            : 'bg-white text-on-surface-variant border-2 border-[#B7D9BC] hover:border-primary/30'}"
        onclick={() => handleSwitch(tab.id)}>
        <span class="text-xl {iconStyle(tab.id)}">
          {tab.icon}
        </span>
        <span class="font-label-lg">{tab.label}</span>
      </button>
    {/each}
  </nav>
</aside>

{#if showMobileMenu}
  <div class="fixed inset-0 bg-black/50 z-[60] lg:hidden" onclick={oncloseMobile}></div>
  <div class="fixed top-0 left-0 h-full w-[75%] max-w-[320px] bg-canvas-cream shadow-2xl flex flex-col border-r-4 border-[#B7D9BC] z-[60] lg:hidden">
    <div class="flex items-center justify-between p-5 border-b-4 border-[#B7D9BC]">
      <h2 class="font-headline-md text-text-main">Menu</h2>
      <button class="w-8 h-8 rounded-full bg-white border-2 border-[#B7D9BC] flex items-center justify-center text-primary cursor-pointer" onclick={oncloseMobile}>
        <span class="text-xl">✕</span>
      </button>
    </div>
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
      {#each visibleNav as tab}
        <button
          class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl cursor-pointer
            {activeTab === tab.id
              ? 'bg-primary text-on-primary shadow-md'
              : 'bg-white text-on-surface-variant border-2 border-[#B7D9BC] hover:shadow-md hover:border-primary/30'}"
          onclick={() => handleSwitch(tab.id)}>
          <span class="text-xl {iconStyle(tab.id)}">
            {tab.icon}
          </span>
          <span class="font-label-lg">{tab.label}</span>
        </button>
      {/each}
    </nav>
  </div>
{/if}
