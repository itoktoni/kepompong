<script>
  import { coverImage, APP_NAME } from './_img.js'
  let { item, type } = $props()
  const players = item.players || item.data?.players || ''
  const materials = item.materials || item.data?.materials || []
  const rules = item.rules || item.data?.rules || []
  const cover = coverImage(type, item)
  const PAGE_H = 1123
</script>

<div style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;background:linear-gradient(135deg,#FFF3E0 0%,#fff 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:320px;height:320px;object-fit:cover;border-radius:24px;border:4px solid #FFCC80;box-shadow:0 8px 32px rgba(0,0,0,0.12);margin-bottom:32px" />
  {:else}
    <div style="font-size:80px;margin-bottom:24px">&#x1F3B2;</div>
  {/if}
  <h1 style="font-size:36px;color:#E65100;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:16px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:500px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:13px;color:#9CA3AF;margin-top:16px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:32px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#if players || materials.length || rules.length}
  <div style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:48px 48px 40px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:20px">
      <span style="display:inline-block;width:36px;height:36px;border-radius:50%;background:#E65100;color:#fff;font-size:16px;font-weight:700;line-height:36px">i</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0;gap:24px;max-width:600px;margin:0 auto">
      {#if players}
        <div style="width:100%;padding:16px;background:#FFF3E0;border-radius:12px;border:2px solid #FFCC80">
          <p style="font-weight:700;color:#E65100;margin:0 0 8px;font-size:15px">&#x1F465; Pemain</p>
          <p style="font-size:16px;color:#1F2937;margin:0;line-height:1.5">{players}</p>
        </div>
      {/if}
      {#if materials.length}
        <div style="width:100%;padding:16px;background:#FFF3E0;border-radius:12px;border:2px solid #FFCC80">
          <p style="font-weight:700;color:#E65100;margin:0 0 8px;font-size:15px">&#x1F9F0; Bahan</p>
          {#each materials as m}<p style="font-size:16px;color:#1F2937;margin:0 0 4px;line-height:1.5">&#x25AA; {m}</p>{/each}
        </div>
      {/if}
      {#if rules.length}
        <div style="width:100%;padding:16px;background:#FFF3E0;border-radius:12px;border:2px solid #FFCC80">
          <p style="font-weight:700;color:#E65100;margin:0 0 8px;font-size:15px">&#x1F4CB; Aturan</p>
          {#each rules as r}<p style="font-size:16px;color:#1F2937;margin:0 0 4px;line-height:1.5">&#x2714; {r}</p>{/each}
        </div>
      {/if}
    </div>
    <div style="text-align:center;padding-top:16px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
