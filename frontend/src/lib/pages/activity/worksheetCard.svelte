<script>
  import { getWorksheetDownloadUrl } from '../../services/api.js'
  import { userRole, user } from '../../stores/authStore.js'
  import DevPanel from '../../components/DevPanel.svelte'

  let { item, bg, onclick } = $props()
  let downloading = $state(false)
  let userRoleVal = $state('')
  let currentUserId = $state(null)
  let devPanel = $state(null)

  $effect(() => {
    const unsub1 = userRole.subscribe(v => userRoleVal = v)
    const unsub2 = user.subscribe(v => currentUserId = v?.id || null)
    return () => { unsub1(); unsub2() }
  })

  const isOwner = $derived(userRoleVal === 'developer' || (currentUserId && item.created_by === currentUserId))

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
    {#if item.creator}
      <div class="bg-white rounded-[24px] border-2 border-[#B7D9BC] p-4 shadow-sm w-full text-left">
        <div class="flex items-center gap-2 mb-2">
          <span class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="text-sm text-primary">👤</span>
          </span>
          <p class="text-xs font-bold text-primary">Dibuat oleh</p>
        </div>
        <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{item.creator}</p>
      </div>
    {/if}
    {#if isOwner}
      <DevPanel bind:this={devPanel} {item} isDeveloper={userRoleVal === 'developer'} />
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
