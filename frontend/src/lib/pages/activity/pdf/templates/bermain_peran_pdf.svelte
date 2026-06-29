<script>
  import { pageImage, coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const roles = item.roles || item.data?.roles || []
  const pages = item.pages || item.data?.pages || []
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#FFF3E0 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #FFCC80;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F3AD;</div>
  {/if}
  <h1 style="font-size:22px;color:#E65100;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each pages as page, i}
  {@const imgSrc = pageImage(type, item, page, i)}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#E65100;color:#fff;font-size:10px;font-weight:700;line-height:24px">{i + 1}</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      {#if imgSrc}
        <img src={imgSrc} alt="" style="max-width:100%;max-height:260px;object-fit:contain;border-radius:10px;margin-bottom:16px" />
      {/if}
      <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.6;max-width:360px;font-weight:500;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">
        {page.narrator || page.text || page.desc || ''}
      </p>
      {#if page.dialog?.length}
        <div style="margin-top:10px;max-width:320px;width:100%">
          {#each page.dialog as d}
            <p style="font-size:10px;margin:0 0 8px;text-align:left"><strong style="color:#E65100">{d.role || d.name}:</strong> {d.text}</p>
          {/each}
        </div>
      {/if}
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/each}
