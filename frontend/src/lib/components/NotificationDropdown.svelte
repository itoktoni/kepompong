<script>
  import { notifications, unreadCount, markRead, markAllRead } from '../composables/useNotifications.js'

  let { show = false, onclose } = $props()

  let notifRef = $state()

  function handleClickItem(notif) {
    if (!notif.read) markRead(notif)
    if (notif.url) {
      window.location.href = notif.url
    }
  }

  $effect(() => {
    if (!show) return
    function handleClick(e) {
      if (notifRef && !notifRef.contains(e.target)) {
        onclose?.()
      }
    }
    document.addEventListener('mousedown', handleClick)
    return () => document.removeEventListener('mousedown', handleClick)
  })
</script>

{#if show}
  <div bind:this={notifRef}
    class="absolute right-0 top-full mt-2 bg-white rounded-2xl shadow-xl border-2 border-[#B7D9BC] w-80 sm:w-96 z-50 overflow-hidden">
    <div class="flex items-center justify-between px-4 py-3 border-b border-outline-variant bg-success-soft/30">
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>
        <h3 class="text-sm font-bold text-text-main">Notifikasi</h3>
        {#if $unreadCount > 0}
          <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-error text-white">{$unreadCount}</span>
        {/if}
      </div>
      {#if $unreadCount > 0}
        <button class="text-xs font-semibold text-primary hover:underline" onclick={markAllRead}>
          Tandai dibaca
        </button>
      {/if}
    </div>

    <div class="max-h-80 overflow-y-auto">
      {#if $notifications.length === 0}
        <div class="px-4 py-8 text-center">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-on-surface-variant/40">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
          </svg>
          <p class="text-xs text-on-surface-variant/60">Tidak ada notifikasi</p>
        </div>
      {:else}
        {#each $notifications as notif (notif.id)}
          <button class="w-full text-left flex items-start gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors border-b border-outline-variant/50 last:border-b-0
            {!notif.read ? 'bg-success-soft/20' : ''}"
            onclick={() => handleClickItem(notif)}>
            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 mt-0.5"
              style="background: {notif.iconColor || '#176c33'}15; color: {notif.iconColor || '#176c33'}">
              <span class="text-lg">{notif.icon || 'ℹ️'}</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-text-main truncate">{notif.title}</p>
                {#if !notif.read}
                  <span class="w-2 h-2 rounded-full bg-primary shrink-0"></span>
                {/if}
              </div>
              {#if notif.body}
                <p class="text-xs text-on-surface-variant leading-snug mt-0.5 line-clamp-2">{notif.body}</p>
              {/if}
              <p class="text-[10px] text-on-surface-variant/60 mt-1">{notif.time || ''}</p>
            </div>
          </button>
        {/each}
      {/if}
    </div>
  </div>
{/if}
