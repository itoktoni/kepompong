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

<div class="bento-card group bg-canvas-cream rounded-[20px] overflow-hidden border-2 border-[#B7D9BC] shadow-sm hover:shadow-md transition-all flex flex-col text-left w-full">
  <div class="p-4 flex flex-col flex-1 gap-2.5">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
        style="background: {bg}">
        {item.icon || '📝'}
      </div>
      <div class="flex-1 min-w-0">
        <h3 class="font-bold text-sm text-text-main leading-tight truncate">{item.title}</h3>
        {#if item.ageLabel}
          <span class="text-[10px] font-semibold text-on-surface-variant">{item.ageLabel}</span>
        {/if}
      </div>
    </div>

    {#if item.desc}
      <p class="text-xs text-on-surface-variant leading-relaxed line-clamp-2">{item.desc}</p>
    {/if}

    <button onclick={handleDownload} disabled={downloading}
      class="mt-auto flex items-center justify-center gap-2 w-full py-2 rounded-xl text-xs font-bold text-white transition-all active:scale-[0.97] disabled:opacity-50"
      style="background: {downloading ? '#999' : '#176c33'}">
      {#if downloading}
        <span class="animate-spin">⏳</span> Mengunduh...
      {:else}
        <span>⬇️</span> Download PDF
      {/if}
    </button>
  </div>
</div>
