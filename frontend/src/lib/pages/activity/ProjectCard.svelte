<script>

  let { item, bg, onclick } = $props()
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  {#if item.image}
    <div class="h-32 overflow-hidden relative shrink-0">
      <img src={item.image} alt={item.title} class="w-full h-full object-cover" onerror={(e) => { e.target.style.display = 'none' }} />
      <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
      {#if item.duration}
        <div class="absolute top-2 right-2 bg-white/90 rounded-full px-2 py-0.5 text-[10px] font-bold text-primary border border-[#B7D9BC]">
          {item.duration}
        </div>
      {/if}
    </div>
  {/if}
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '🎨'}
      </div>
      <div class="flex gap-1.5">
        {#if item.duration}
          <span class="text-xs font-bold px-2 py-1 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">
            ⏱ {item.duration}
          </span>
        {/if}
        {#if item.difficulty}
          <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: {bg}; color: #2196F3">
            {item.difficulty}
          </span>
        {/if}
      </div>
    </div>
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
    {/if}
    {#if item.materials?.length}
      <div class="flex flex-wrap gap-1.5 mb-3">
        {#each item.materials.slice(0, 3) as mat}
          <span class="text-xs px-2 py-1 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">
            {mat}
          </span>
        {/each}
        {#if item.materials.length > 3}
          <span class="text-xs px-2 py-1 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">
            +{item.materials.length - 3}
          </span>
        {/if}
      </div>
    {/if}
    {#if item.views}
      <span class="flex items-center gap-1 text-[11px] text-on-surface-variant">
        <span class="material-symbols-outlined text-sm">visibility</span>
        {item.views}
      </span>
    {/if}
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span class="material-symbols-outlined text-xl">brush</span>
      Mulai Proyek{#if item.steps?.length} ({item.steps.length} langkah){/if}
      <span class="material-symbols-outlined text-xl ml-auto group-hover:translate-x-1 transition-transform">arrow_forward</span>
    </div>
  </div>
</button>
