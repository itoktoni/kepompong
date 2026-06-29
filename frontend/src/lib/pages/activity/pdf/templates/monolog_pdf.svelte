<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const script = item.script || item.data?.script || ''
  const tips = item.tips || item.data?.tips || []
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#E3F2FD 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #90CAF9;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F3A4;</div>
  {/if}
  <h1 style="font-size:22px;color:#0D47A1;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#if script}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#0D47A1;color:#fff;font-size:10px;font-weight:700;line-height:24px">&#x1F3A4;</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.8;max-width:360px;font-style:italic;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">
        "{script}"
      </p>
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}

{#if tips.length}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#0D47A1;color:#fff;font-size:10px;font-weight:700;line-height:24px">&#x1F4A1;</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0;gap:10px;max-width:320px">
      {#each tips as tip, i}
        <div style="display:flex;align-items:flex-start;gap:7px;width:100%">
          <span style="width:16px;height:16px;border-radius:50%;background:#E3F2FD;color:#0D47A1;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0">{i + 1}</span>
          <p style="font-size:11px;color:#1F2937;line-height:1.5;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">{tip}</p>
        </div>
      {/each}
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
