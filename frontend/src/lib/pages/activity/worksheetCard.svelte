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

<div class="bento-card group bg-white rounded-[20px] overflow-hidden border-2 border-[#B7D9BC] shadow-sm hover:shadow-md transition-all flex flex-col text-center w-full">
  <div class="p-4 flex flex-col flex-1 items-center">
    <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl mb-2 shadow-sm"
      style="background: {bg}">
      {item.icon || '📝'}
    </div>
    <h3 class="font-bold text-sm text-text-main leading-tight mb-1 line-clamp-2">{item.title}</h3>
    {#if item.ageLabel}
      <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-success-soft text-primary mb-2">
        {item.ageLabel}
      </span>
    {/if}
    {#if item.desc}
      <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2 mb-3">{item.desc}</p>
    {/if}
    <div class="w-full mt-auto pt-2">
      <button onclick={handleDownload} disabled={downloading}
        class="flex items-center justify-center gap-1.5 w-full py-2 rounded-xl text-[11px] font-bold text-white transition-all active:scale-[0.97] disabled:opacity-50"
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
