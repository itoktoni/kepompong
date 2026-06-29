<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const how = item.how || item.data?.how || ''
  const rules = item.rules || item.data?.rules || []
  const moves = item.moves || item.data?.moves || []
  const lyrics = item.lyrics || item.data?.lyrics || ''
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#FCE4EC 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #F48FB1;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F932;</div>
  {/if}
  <h1 style="font-size:22px;color:#C2185B;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each [{label:'Cara Bermain',icon:'&#x1F4CB;',text:how},{label:'Aturan',icon:'&#x1F4DC;',items:rules},{label:'Gerakan',icon:'&#x1F932;',items:moves},{label:'Lirik',icon:'&#x1F3B5;',text:lyrics}] as section}
  {#if section.text || section.items?.length}
    <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
      <div style="text-align:center;margin-bottom:12px">
        <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#C2185B;color:#fff;font-size:10px;font-weight:700;line-height:24px">{section.icon}</span>
      </div>
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
        <h3 style="font-size:14px;color:#C2185B;font-weight:800;margin:0 0 20px">{section.label}</h3>
        {#if section.text}
          <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.7;max-width:360px;font-weight:500;margin:0;white-space:pre-line;word-break:normal;overflow-wrap:normal;hyphens:none">{section.text}</p>
        {/if}
        {#if section.items?.length}
          <div style="display:flex;flex-direction:column;gap:7px;max-width:320px">
            {#each section.items as item, i}
              <div style="display:flex;align-items:flex-start;gap:7px">
                <span style="width:16px;height:16px;border-radius:50%;background:#FCE4EC;color:#C2185B;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;flex-shrink:0">{i + 1}</span>
                <p style="font-size:11px;color:#1F2937;line-height:1.5;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">{item}</p>
              </div>
            {/each}
          </div>
        {/if}
      </div>
      <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
    </div>
  {/if}
{/each}
