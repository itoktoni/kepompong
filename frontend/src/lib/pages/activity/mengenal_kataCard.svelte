<script>

  let { item, bg, onclick } = $props()
  let slides = $derived(item.slides || [])
  let tags = $derived(item.tags || [])
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  <div class="p-5 flex flex-col flex-1">
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if tags.length}
      <div class="flex flex-wrap gap-1.5 mb-3">
        {#each tags as tag}
          <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">{tag}</span>
        {/each}
      </div>
    {/if}
    {#if slides.length}
      <div class="bg-white rounded-xl p-3 mb-3 border border-[#B7D9BC]/50">
        <p class="text-xs text-on-surface-variant">
          <span class="font-bold text-primary">Contoh:</span> {slides[0].nama} — {slides[0].digunakan_untuk}
        </p>
      </div>
    {/if}
    {#if item.views}
      <span class="flex items-center gap-1 text-[11px] text-on-surface-variant">
        <span class="text-sm">👁</span>
        {item.views}
      </span>
    {/if}
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span class="text-xl">👋</span>
      Kenalan
      <span class="text-xl ml-auto group-hover:translate-x-1 transition-transform">→</span>
    </div>
  </div>
</button>
