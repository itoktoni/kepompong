<script>
  import { getWorksheetDownloadUrl } from '../../services/api.js'

  let { item, bg, onclick, type } = $props()
  let downloading = $state(false)

  async function handleDownload(e) {
    e.stopPropagation()
    if (downloading || !item?.id) return
    downloading = true
    try {
      const data = await getWorksheetDownloadUrl(item.id)
      if (data?.url) {
        const a = document.createElement('a')
        a.href = data.url
        a.download = ''
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)
      }
    } catch (err) {
      console.error('Download failed:', err)
    } finally {
      downloading = false
    }
  }
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '📝'}
      </div>
      {#if item.ageLabel}
        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">
          {item.ageLabel}
        </span>
      {/if}
    </div>
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
    {/if}
    {#if item.views}
      <span class="flex items-center gap-1 text-[11px] text-on-surface-variant">
        <span class="text-sm">👁</span>
        {item.views}
      </span>
    {/if}
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span onclick={handleDownload} role="button" tabindex="0"
        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-white transition-all active:scale-95 cursor-pointer"
        class:opacity-50={downloading}
        style="background: {downloading ? '#999' : '#176c33'}">
        <span class="text-sm">{downloading ? '⏳' : '⬇️'}</span>
        {downloading ? '...' : 'Download'}
      </span>
      <span class="text-sm ml-auto text-on-surface-variant">📝 Buka Worksheet</span>
      <span class="text-xl group-hover:translate-x-1 transition-transform">→</span>
    </div>
  </div>
</button>
