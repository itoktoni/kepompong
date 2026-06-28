<script>
  import { generatePdf } from './pdf/index.js'
  import { userRole, user } from '../../stores/authStore.js'
  import DevPanel from '../../components/DevPanel.svelte'

  let { item, bg, onclick, type } = $props()

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

  async function handleDownload() {
    if (downloading) return
    downloading = true
    try { await generatePdf(item, type) }
    catch (e) { console.error('PDF download failed:', e) }
    finally { downloading = false }
  }
</script>

<button class="bento-card group bg-canvas-cream rounded-[24px] overflow-hidden border-4 border-[#B7D9BC] shadow-md cursor-pointer transition-all hover:shadow-lg flex flex-col text-left w-full"
  {onclick}>
  <div class="p-5 flex flex-col flex-1">
    <div class="flex items-start justify-between mb-3">
      <div class="w-12 h-12 rounded-[16px] flex items-center justify-center text-2xl border-2 border-white shadow-sm" style="background: {bg}">
        {item.emoji || '🎤'}
      </div>
      {#if item.tips?.length}
        <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: {bg}; color: #9C27B0">
          {item.tips.length} tips
        </span>
      {/if}
    </div>
    <h3 class="font-headline-md text-headline-md mb-2">{item.title}</h3>
    {#if item.desc}
      <p class="text-sm text-on-surface-variant mb-3 line-clamp-2">{item.desc}</p>
    {/if}
    {#if item.script}
      <div class="bg-white rounded-xl p-3 mb-3 border border-[#B7D9BC]/50">
        <p class="text-xs text-on-surface-variant line-clamp-3 italic">"{item.script}"</p>
      </div>
    {/if}
    {#if item.tips?.length}
      <div class="bg-success-soft rounded-xl p-3 mb-3 border border-[#B7D9BC]/50">
        <p class="text-xs text-primary">
          <span class="font-bold">💡 Tips:</span> {item.tips[0]}
        </p>
      </div>
    {/if}
    {#if item.creator}
      <div class="bg-white rounded-[24px] border-2 border-[#B7D9BC] p-4 shadow-sm">
        <div class="flex items-center gap-2 mb-2">
          <span class="w-7 h-7 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="text-sm text-primary">👤</span>
          </span>
          <p class="text-xs font-bold text-primary">Dibuat oleh</p>
        </div>
        <p class="text-sm text-on-surface-variant leading-relaxed whitespace-pre-line">{item.creator}</p>
      </div>
    {/if}
    {#if item.views}
      <span class="flex items-center gap-1 text-[11px] text-on-surface-variant">
        <span class="text-sm">👁</span>
        {item.views}
      </span>
    {/if}
    {#if isOwner}
      <DevPanel bind:this={devPanel} {item} isDeveloper={userRoleVal === 'developer'} />
    {/if}
    <div class="flex items-center gap-2 text-primary font-label-lg mt-auto pt-3 border-t-2 border-[#B7D9BC]/50">
      <span onclick={(e) => { e.stopPropagation(); handleDownload() }}
        class="w-7 h-7 rounded-full bg-white border border-[#B7D9BC] flex items-center justify-center text-xs hover:bg-success-soft transition-colors cursor-pointer shrink-0 {downloading ? 'opacity-50 pointer-events-none' : ''}"
        title="Download PDF" role="button" tabindex="0">
        {downloading ? '⏳' : '📥'}
      </span>
      <span class="text-xl">🎤</span>
      Lihat Naskah
      <span class="text-xl ml-auto group-hover:translate-x-1 transition-transform">→</span>
    </div>
  </div>
</button>
