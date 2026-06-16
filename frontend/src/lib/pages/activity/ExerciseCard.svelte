<script>

  let { item, bg, onclick } = $props()
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '🧘'}
      </div>
      {#if item.steps?.length}
        <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: {bg}; color: #795548">
          {item.steps.length} langkah
        </span>
      {/if}
    </div>
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
    {/if}
    {#if item.steps?.length}
      <div class="bg-white rounded-xl p-3 mb-3 border border-[#B7D9BC]/50">
        <p class="text-xs text-on-surface-variant">
          <span class="font-bold text-primary">1.</span> {item.steps[0]}
        </p>
      </div>
    {/if}
    {#if item.benefit}
      <div class="bg-success-soft rounded-xl p-3 mb-3 border border-[#B7D9BC]/50">
        <p class="text-xs text-primary font-bold">
          <span class="w-5 h-5 rounded-full bg-white border border-[#B7D9BC] inline-flex items-center justify-center text-[10px] align-middle mr-1">✨</span>
          {item.benefit}
        </p>
      </div>
    {/if}
    {#if item.views}
      <span class="flex items-center gap-1 text-[11px] text-on-surface-variant">
        <span class="material-symbols-outlined text-sm">visibility</span>
        {item.views}
      </span>
    {/if}
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span class="material-symbols-outlined text-xl">self_improvement</span>
      Mulai Latihan
      <span class="material-symbols-outlined text-xl ml-auto group-hover:translate-x-1 transition-transform">arrow_forward</span>
    </div>
  </div>
</button>
