<script>
  let { show = false, title = '', onclose, children } = $props()
</script>

{#if show}
  <div class="fixed inset-0 z-[100] flex items-end justify-center lg:items-center"
    onmousedown={(e) => { if (e.target === e.currentTarget) { e.preventDefault(); onclose?.() } }} role="presentation">
    <div class="absolute inset-0 bg-black/40" onclick={() => onclose?.()}></div>
    <div class="relative bg-canvas-cream rounded-t-[32px] lg:rounded-[32px] w-full max-w-md p-6 pb-8 lg:mb-0 border-4 border-[#B7D9BC] border-b-0 lg:border-b-4 shadow-xl max-h-[90vh] overflow-y-auto" onclick={(e) => e.stopPropagation()} role="dialog" aria-modal="true" tabindex="-1">
      <div class="w-10 h-1 bg-[#B7D9BC] rounded-full mx-auto mb-5 lg:hidden"></div>
      {#if title}
        <div class="flex items-center justify-between mb-4 pb-4 border-b-2 border-[#B7D9BC]">
          <h3 class="font-headline-md text-text-main">{title}</h3>
          <button class="w-8 h-8 rounded-full flex items-center justify-center hover:bg-surface-container transition-colors" onclick={() => onclose?.()}>
            <span class="text-xl">✕</span>
          </button>
        </div>
      {:else}
        <button class="absolute top-4 right-4 w-8 h-8 rounded-full flex items-center justify-center hover:bg-surface-container transition-colors z-10" onclick={() => onclose?.()}>
          <span class="text-xl">✕</span>
        </button>
      {/if}
      <div>
        {@render children?.()}
      </div>
    </div>
  </div>
{/if}
