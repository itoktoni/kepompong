<script>
  import { canInstall, installApp } from '../composables/useInstall.js'

  let show = $state(false)
  let dismissed = $state(false)

  $effect(() => {
    const unsub = canInstall.subscribe(v => {
      if (v && !dismissed) {
        setTimeout(() => { show = true }, 2000)
      } else if (!v) {
        show = false
      }
    })
    return unsub
  })

  function handleInstall() {
    installApp()
    show = false
  }

  function handleDismiss() {
    show = false
    dismissed = true
  }
</script>

{#if show}
  <div class="fixed bottom-20 lg:bottom-6 left-4 right-4 lg:left-auto lg:right-6 lg:w-[380px] z-[90] animate-slide-up">
    <div class="bg-white rounded-[20px] border-4 border-[#B7D9BC] shadow-xl p-4 flex items-center gap-3">
      <div class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center shrink-0 shadow-md">
        <span class="material-symbols-outlined text-white text-2xl">download</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-text-main">Install Jejak Tumbuh</p>
        <p class="text-xs text-on-surface-variant mt-0.5">Akses lebih cepat dari layar utama</p>
      </div>
      <div class="flex flex-col gap-1.5 shrink-0">
        <button onclick={handleInstall}
          class="px-4 py-2 rounded-xl text-white text-xs font-bold"
          style="background: #176C33; box-shadow: 0 3px 0 #0d4a22;">
          Install
        </button>
        <button onclick={handleDismiss}
          class="px-4 py-1.5 rounded-xl text-xs font-bold text-on-surface-variant bg-canvas-cream border border-[#B7D9BC]">
          Nanti
        </button>
      </div>
    </div>
  </div>
{/if}

<style>
  @keyframes slide-up {
    from { transform: translateY(100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .animate-slide-up {
    animation: slide-up 0.4s ease-out;
  }
</style>
