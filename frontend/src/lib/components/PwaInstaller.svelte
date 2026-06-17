<script>
  import { canInstall, installApp } from '../composables/useInstall.js'
  import { appConfig } from '../config/appConfig.js'

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
  <div class="fixed bottom-20 left-4 right-4 lg:bottom-3 lg:left-3 lg:right-auto lg:w-[250px] z-[90] animate-slide-up">
    <div class="bg-white rounded-[20px] border-4 border-[#B7D9BC] shadow-xl p-4 flex flex-col gap-3">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center shrink-0 shadow-md">
          <span class="material-symbols-outlined text-white text-xl">download</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-text-main">{appConfig.name}</p>
          <p class="text-[11px] text-on-surface-variant">Install ke layar utama</p>
        </div>
      </div>
      <div class="flex gap-2">
        <button onclick={handleInstall}
          class="flex-1 py-2 rounded-xl text-white text-xs font-bold"
          style="background: #176C33; box-shadow: 0 3px 0 #0d4a22;">
          Install
        </button>
        <button onclick={handleDismiss}
          class="px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant bg-canvas-cream border border-[#B7D9BC]">
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
