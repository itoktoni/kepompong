<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const players = item.players || item.data?.players || ''
  const materials = item.materials || item.data?.materials || []
  const rules = item.rules || item.data?.rules || []
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#FFF3E0 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #FFCC80;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F3B2;</div>
  {/if}
  <h1 style="font-size:22px;color:#E65100;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#if players || materials.length || rules.length}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#E65100;color:#fff;font-size:10px;font-weight:700;line-height:24px">i</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0;gap:14px;max-width:360px;margin:0 auto">
      {#if players}
        <div style="width:100%;padding:10px;background:#FFF3E0;border-radius:7px;border:1px solid #FFCC80">
          <p style="font-weight:700;color:#E65100;margin:0 0 8px;font-size:9px">&#x1F465; Pemain</p>
          <p style="font-size:10px;color:#1F2937;margin:0;line-height:1.5">{players}</p>
        </div>
      {/if}
      {#if materials.length}
        <div style="width:100%;padding:10px;background:#FFF3E0;border-radius:7px;border:1px solid #FFCC80">
          <p style="font-weight:700;color:#E65100;margin:0 0 8px;font-size:9px">&#x1F9F0; Bahan</p>
          {#each materials as m}<p style="font-size:10px;color:#1F2937;margin:0 0 4px;line-height:1.5">&#x25AA; {m}</p>{/each}
        </div>
      {/if}
      {#if rules.length}
        <div style="width:100%;padding:10px;background:#FFF3E0;border-radius:7px;border:1px solid #FFCC80">
          <p style="font-weight:700;color:#E65100;margin:0 0 8px;font-size:9px">&#x1F4CB; Aturan</p>
          {#each rules as r}<p style="font-size:10px;color:#1F2937;margin:0 0 4px;line-height:1.5">&#x2714; {r}</p>{/each}
        </div>
      {/if}
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
