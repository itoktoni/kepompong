<script>
  import AppModal from '../../components/AppModal.svelte'

  let { item, bg, onclick, type } = $props()

  let showModal = $state(false)
  let currentSlide = $state(0)
  let slides = $derived(item.slides || [])

  function open() {
    currentSlide = 0
    showModal = true
  }

  function next() {
    if (currentSlide < slides.length - 1) currentSlide++
  }

  function prev() {
    if (currentSlide > 0) currentSlide--
  }

  let slide = $derived(slides[currentSlide])
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  onclick={open}>
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '🪣'}
      </div>
      {#if slides.length}
        <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: {bg}; color: #5D4037">
          {slides.length} benda
        </span>
      {/if}
    </div>
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
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
      <span class="text-xl">🪣</span>
      Mulai Mengenal Benda
      <span class="text-xl ml-auto group-hover:translate-x-1 transition-transform">→</span>
    </div>
  </div>
</button>

<AppModal show={showModal} title="" onclose={() => showModal = false}>
  {#if slide}
    <div class="flex items-center gap-2 mb-4">
      <span class="w-10 h-10 rounded-full flex items-center justify-center text-xl border-2 border-white shadow-sm" style="background: {bg}">
        🪣
      </span>
      <div>
        <p class="text-xs text-on-surface-variant">{item.title}</p>
        <p class="font-headline-md text-text-main">{slide.nama}</p>
      </div>
      <span class="ml-auto text-xs font-bold px-2 py-1 rounded-full" style="background: {bg}; color: #5D4037">
        {currentSlide + 1}/{slides.length}
      </span>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden border-2 border-[#B7D9BC] mb-4">
      <div class="aspect-video bg-surface-container flex items-center justify-center overflow-hidden">
        {#if slide.image}
          <img src={slide.image} alt={slide.nama} class="w-full h-full object-cover" loading="lazy" onerror={(e) => e.target.style.display='none'} />
        {:else}
          <span class="text-5xl">🪣</span>
        {/if}
      </div>
    </div>

    <div class="space-y-3">
      <div class="bg-white rounded-xl p-3 border-2 border-[#B7D9BC]">
        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Digunakan Untuk</p>
        <p class="text-sm text-text-main">{slide.digunakan_untuk}</p>
      </div>

      <div class="bg-white rounded-xl p-3 border-2 border-[#B7D9BC]">
        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Fungsi</p>
        <p class="text-sm text-text-main">{slide.fungsi}</p>
      </div>

      <div class="bg-white rounded-xl p-3 border-2 border-[#B7D9BC]">
        <p class="text-xs font-bold text-on-surface-variant uppercase tracking-wider mb-1">Spesifikasi</p>
        <p class="text-sm text-text-main">{slide.spesifikasi}</p>
      </div>

      {#if slide.fakta}
        <div class="bg-warning-soft rounded-xl p-3 border-2 border-warm-bonding/30">
          <p class="text-xs font-bold text-warm-bonding uppercase tracking-wider mb-1">💡 Taukah Kamu?</p>
          <p class="text-sm text-text-main">{slide.fakta}</p>
        </div>
      {/if}
    </div>

    <div class="flex gap-3 mt-5">
      <button
        class="flex-1 py-3 rounded-2xl text-sm font-bold btn-pop-gray {currentSlide === 0 ? 'opacity-40 cursor-not-allowed' : ''}"
        onclick={prev}
        disabled={currentSlide === 0}>
        ← Sebelumnya
      </button>
      <button
        class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green {currentSlide === slides.length - 1 ? 'opacity-40 cursor-not-allowed' : ''}"
        onclick={next}
        disabled={currentSlide === slides.length - 1}>
        Selanjutnya →
      </button>
    </div>
  {/if}
</AppModal>

<style>
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 3px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #176c33;
  }
  .btn-pop-gray {
    background-color: #e5e5e5;
    box-shadow: 0 3px 0 #999;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #999;
  }
</style>
