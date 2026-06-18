<script>
  import { syncStatus } from '../stores/syncStatusStore.js'

  let status = $state({ syncing: false, pending: 0, lastSyncAt: '', currentAction: '' })
  let offline = $state(false)
  let showDone = $state(false)
  let lastProcessed = $state(0)

  $effect(() => {
    const unsub = syncStatus.subscribe(v => {
      if (v.processed > 0 && v.processed !== lastProcessed && !v.syncing) {
        lastProcessed = v.processed
        showDone = true
        setTimeout(() => { showDone = false }, 3000)
      }
      status = v
    })
    return unsub
  })

  $effect(() => {
    offline = !navigator.onLine
    const goOffline = () => { offline = true }
    const goOnline = () => { offline = false }
    window.addEventListener('online', goOnline)
    window.addEventListener('offline', goOffline)
    return () => {
      window.removeEventListener('online', goOnline)
      window.removeEventListener('offline', goOffline)
    }
  })

  const btnClass = $derived.by(() => {
    if (status.syncing) return 'bg-primary text-white border-primary'
    if (offline) return 'bg-amber-500 text-white border-amber-600'
    return 'bg-white text-primary border-[#B7D9BC]'
  })

  const icon = $derived.by(() => {
    if (status.syncing) return '🗘'
    if (offline) return '☁'
    if (status.pending > 0) return '🔄'
    return '☁✓'
  })

  const title = $derived.by(() => {
    if (status.syncing) return 'Menyinkronkan...'
    if (offline) return status.pending > 0 ? `Offline — ${status.pending} perubahan tertunda` : 'Mode Offline'
    if (status.pending > 0) return `${status.pending} perubahan tertunda`
    return 'Tersinkronkan'
  })
</script>

<div class="fixed bottom-20 right-4 lg:bottom-4 z-[80] animate-slide-up">
  <button
    class="w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full border-2 shadow-sm hover:opacity-80 transition-all duration-200 {btnClass}"
    {title}>
    <span class="text-xl {status.syncing ? 'animate-spin-icon' : ''}">{icon}</span>
  </button>

  {#if showDone && lastProcessed > 0}
    <div class="absolute bottom-14 right-0 bg-green-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-lg shadow whitespace-nowrap animate-fade-in">
      {lastProcessed} data tersinkronkan
    </div>
  {/if}
</div>

<style>
  @keyframes slide-up {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .animate-slide-up {
    animation: slide-up 0.3s ease-out;
  }
  @keyframes spin-icon {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  .animate-spin-icon {
    animation: spin-icon 1s linear infinite;
  }
  @keyframes fade-in {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fade-in {
    animation: fade-in 0.2s ease-out;
  }
</style>
