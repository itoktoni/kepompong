<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const content = item.content || item.data?.content || ''
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#E3F2FD 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #90CAF9;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F4DD;</div>
  {/if}
  <h1 style="font-size:22px;color:#1565C0;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#if content}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      <p style="font-size:11px;color:#1F2937;text-align:left;line-height:1.8;max-width:360px;white-space:pre-line;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">{content}</p>
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
