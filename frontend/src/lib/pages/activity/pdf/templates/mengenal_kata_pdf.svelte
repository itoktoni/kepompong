<script>
  import { pageImage, coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const slides = item.slides || item.data?.slides || []
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #B7D9BC;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1FAA3;</div>
  {/if}
  <h1 style="font-size:22px;color:#176C33;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}
    <p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>
  {/if}
  {#if item.creator}
    <p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>
  {/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each slides as s, i}
  {@const imgSrc = pageImage(type, item, s, i)}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#176C33;color:#fff;font-size:10px;font-weight:700;line-height:24px">{i + 1}</span>
    </div>

    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      {#if imgSrc}
        <img src={imgSrc} alt="" style="max-width:100%;max-height:260px;object-fit:contain;border-radius:10px;margin-bottom:16px" />
      {/if}
      <div style="text-align:center;max-width:360px">
        <p style="font-size:16px;color:#5D4037;font-weight:800;margin:0 0 4px;word-break:normal;overflow-wrap:normal;hyphens:none">{s.nama}</p>
        {#if s.english}
          <p style="font-size:10px;color:#9CA3AF;font-style:italic;margin:0 0 16px">{s.english}</p>
        {/if}
        {#if s.digunakan_untuk}
          <p style="font-size:10px;color:#1F2937;line-height:1.5;margin:0 0 6px"><strong>Digunakan:</strong> {s.digunakan_untuk}</p>
        {/if}
        {#if s.fungsi}
          <p style="font-size:10px;color:#1F2937;line-height:1.5;margin:0 0 6px"><strong>Fungsi:</strong> {s.fungsi}</p>
        {/if}
        {#if s.spesifikasi}
          <p style="font-size:10px;color:#1F2937;line-height:1.5;margin:0 0 6px"><strong>Ciri:</strong> {s.spesifikasi}</p>
        {/if}
      </div>
    </div>
  </div>
{/each}
