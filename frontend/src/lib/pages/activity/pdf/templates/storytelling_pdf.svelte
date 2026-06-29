<script>
  import { pageImage, coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const pages = item.pages || item.data?.pages || []
  const slug = item.slug || item.id
  const cover = coverImage(type, item)
</script>

<div class="cover" style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:{PAGE_W}px;height:{PAGE_H}px;object-fit:contain;border-radius:14px;border:3px solid #B7D9BC;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F4D6;</div>
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

{#each pages as page, i}
  {@const imgSrc = pageImage(type, item, page, i)}
  <div class="story-page" style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#176C33;color:#fff;font-size:10px;font-weight:700;line-height:24px">{i + 1}</span>
    </div>

    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      {#if imgSrc}
        <!-- UPDATE DI SINI: Menghapus !important dan menambahkan object-fit -->
        <img src={imgSrc} alt="" style="width: 300px; height: 300px; aspect-ratio: 1 / 1; object-fit: cover; border-radius: 10px; margin-bottom: 16px" />
      {/if}
      <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.6;font-weight:500;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">
        {page.text || page.desc || page.step || page.narrator || ''}
      </p>
    </div>
  </div>
{/each}

{#if item.moral}
  <div class="moral-page" style="box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);font-family:'Fredoka',sans-serif">
    <div style="font-size:36px;margin-bottom:12px">&#x1F4AC;</div>
    <h2 style="font-size:16px;color:#176C33;font-weight:800;margin:0 0 14px">Pelajaran</h2>
    <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.7;max-width:320px;font-style:italic;font-weight:500">
      {item.moral}
    </p>
    <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
