<script>
  import { coverImage, APP_NAME } from './_img.js'
  let { item, type } = $props()
  const how = item.how || item.data?.how || ''
  const rules = item.rules || item.data?.rules || []
  const moves = item.moves || item.data?.moves || []
  const lyrics = item.lyrics || item.data?.lyrics || ''
  const cover = coverImage(type, item)
  const PAGE_H = 1123
</script>

<div style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;background:linear-gradient(135deg,#FCE4EC 0%,#fff 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:320px;height:320px;object-fit:cover;border-radius:24px;border:4px solid #F48FB1;box-shadow:0 8px 32px rgba(0,0,0,0.12);margin-bottom:32px" />
  {:else}
    <div style="font-size:80px;margin-bottom:24px">&#x1F932;</div>
  {/if}
  <h1 style="font-size:36px;color:#C2185B;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:16px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:500px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:13px;color:#9CA3AF;margin-top:16px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:32px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each [{label:'Cara Bermain',icon:'&#x1F4CB;',text:how},{label:'Aturan',icon:'&#x1F4DC;',items:rules},{label:'Gerakan',icon:'&#x1F932;',items:moves},{label:'Lirik',icon:'&#x1F3B5;',text:lyrics}] as section}
  {#if section.text || section.items?.length}
    <div style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:48px 48px 40px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#fff">
      <div style="text-align:center;margin-bottom:20px">
        <span style="display:inline-block;width:36px;height:36px;border-radius:50%;background:#C2185B;color:#fff;font-size:16px;font-weight:700;line-height:36px">{section.icon}</span>
      </div>
      <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
        <h3 style="font-size:24px;color:#C2185B;font-weight:800;margin:0 0 20px">{section.label}</h3>
        {#if section.text}
          <p style="font-size:20px;color:#1F2937;text-align:center;line-height:1.7;max-width:600px;font-weight:500;margin:0;white-space:pre-line">{section.text}</p>
        {/if}
        {#if section.items?.length}
          <div style="display:flex;flex-direction:column;gap:12px;max-width:550px">
            {#each section.items as item, i}
              <div style="display:flex;align-items:flex-start;gap:12px">
                <span style="width:28px;height:28px;border-radius:50%;background:#FCE4EC;color:#C2185B;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">{i + 1}</span>
                <p style="font-size:18px;color:#1F2937;line-height:1.5;margin:0">{item}</p>
              </div>
            {/each}
          </div>
        {/if}
      </div>
      <div style="text-align:center;padding-top:16px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
    </div>
  {/if}
{/each}
