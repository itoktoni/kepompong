<script>
  import { generatePdf } from './pdf/index.js'

  let { item, bg, onclick, type } = $props()

  let downloading = $state(false)

  async function handleDownload() {
    if (downloading) return
    downloading = true
    try { await generatePdf(item, type) }
    catch (e) { console.error('PDF download failed:', e) }
    finally { downloading = false }
  }

  function getYoutubeId(url) {
    if (!url) return null
    const m = url.match(/(?:embed\/|youtu\.be\/|watch\?v=)([a-zA-Z0-9_-]{11})/)
    return m ? m[1] : null
  }

  const youtubeId = $derived(getYoutubeId(item?.audio_url))
  const isYoutube = $derived(!!youtubeId)
  const coverImage = $derived(
    item?.cover || item?.image || (isYoutube ? `https://img.youtube.com/vi/${youtubeId}/hqdefault.jpg` : null)
  )
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  {#if coverImage}
    <div class="w-full aspect-video overflow-hidden relative">
      <img src={coverImage} alt={item.title} class="w-full h-full object-cover" loading="lazy"
        onerror={(e) => { e.target.style.display = 'none'; e.target.nextElementSibling.nextElementSibling.style.display = 'flex' }} />
      <div class="absolute inset-0 bg-surface-container items-center justify-center flex-col gap-1 hidden">
        <span class="text-3xl">🖼️</span>
        <span class="text-[10px] text-on-surface-variant">Gambar tidak tersedia</span>
      </div>
      <div class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
        <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" class="w-12 h-12 drop-shadow-lg">
          <path d="M8 5.14v14.72a1 1 0 0 0 1.5.86l11.5-7.36a1 1 0 0 0 0-1.72L9.5 4.28A1 1 0 0 0 8 5.14Z"/>
        </svg>
      </div>
    </div>
  {:else}
    <div class="w-full aspect-video flex items-center justify-center text-5xl" style="background: {bg}">
      {item.emoji || '🎵'}
    </div>
  {/if}
  <div class="p-4 flex flex-col flex-1">
    <h3 class="font-headline-md text-headline-md mb-1">{item.title}</h3>
    {#if item.desc}
      <p class="text-xs text-on-surface-variant line-clamp-2">{item.desc}</p>
    {/if}
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span onclick={(e) => { e.stopPropagation(); handleDownload() }}
        class="w-7 h-7 rounded-full bg-white border border-[#B7D9BC] flex items-center justify-center text-xs hover:bg-success-soft transition-colors cursor-pointer shrink-0 {downloading ? 'opacity-50 pointer-events-none' : ''}"
        title="Download PDF" role="button" tabindex="0">
        {downloading ? '⏳' : '📥'}
      </span>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V4.5A2.25 2.25 0 0 0 17.118 2.3L7.5 5.25" /></svg>
      Putar
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 ml-auto group-hover:translate-x-1 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
    </div>
  </div>
</button>
