<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#FFF9C4 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #FFF176;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F58C;&#xFE0F;</div>
  {/if}
  <h1 style="font-size:22px;color:#F57F17;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px;font-family:'Fredoka',sans-serif;background:#fff">
  <p style="font-size:12px;color:#6B7280;text-align:center;line-height:1.7;max-width:300px;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">
    Gunakan Ctrl+P untuk mencetak halaman ini dan mewarnai gambar.
  </p>
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>
