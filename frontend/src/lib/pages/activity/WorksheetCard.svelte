<script>
  import { getWorksheetDownloadUrl } from '../../services/api.js'

  let { item, bg, onclick } = $props()
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

<div class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md hover:shadow-lg transition-all flex flex-col text-left w-full">
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm"
        style="background: {bg}">
        {item.icon || '📝'}
      </div>
      {#if item.ageLabel}
        <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-white border border-[#B7D9BC] text-on-surface-variant">
          {item.ageLabel}
        </span>
      {/if}
    </div>
    <h3 class="font-headline-md text-headline-md mb-2 line-clamp-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
    {/if}
    <div class="mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <button onclick={handleDownload} disabled={downloading}
        class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl text-xs font-bold text-white transition-all active:scale-[0.97] disabled:opacity-50"
        style="background: {downloading ? '#999' : '#176c33'}">
        {#if downloading}
          <span class="animate-spin">⏳</span> Mengunduh...
        {:else}
          <span>⬇️</span> Download
        {/if}
      </button>
    </div>
  </div>
</div>
