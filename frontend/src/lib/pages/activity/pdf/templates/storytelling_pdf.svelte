<script>
  import { pageImage, coverImage , APP_NAME } from './_img.js'
  let { item, type } = $props()
  const pages = item.pages || item.data?.pages || []
  const slug = item.slug || item.id
  const cover = coverImage(type, item)
  const PAGE_H = 1123
</script>

<div class="cover" style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:320px;height:320px;object-fit:cover;border-radius:24px;border:4px solid #B7D9BC;box-shadow:0 8px 32px rgba(0,0,0,0.12);margin-bottom:32px" />
  {:else}
    <div style="font-size:80px;margin-bottom:24px">&#x1F4D6;</div>
  {/if}
  <h1 style="font-size:36px;color:#176C33;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}
    <p style="font-size:16px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:500px">{item.desc}</p>
  {/if}
  {#if item.creator}
    <p style="font-size:13px;color:#9CA3AF;margin-top:16px">Dibuat oleh: {item.creator}</p>
  {/if}
  <div style="margin-top:auto;padding-top:32px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each pages as page, i}
  {@const imgSrc = pageImage(type, item, page, i)}
  <div class="story-page" style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:48px 48px 40px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:20px">
      <span style="display:inline-block;width:36px;height:36px;border-radius:50%;background:#176C33;color:#fff;font-size:16px;font-weight:700;line-height:36px">{i + 1}</span>
    </div>

    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      {#if imgSrc}
        <img src={imgSrc} alt="" style="max-width:100%;max-height:520px;object-fit:contain;border-radius:16px;margin-bottom:28px" />
      {/if}
      <p style="font-size:20px;color:#1F2937;text-align:center;line-height:1.6;max-width:600px;font-weight:500;margin:0">
        {page.text || page.desc || page.step || page.narrator || ''}
      </p>
    </div>
  </div>
{/each}

{#if item.moral}
  <div class="moral-page" style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:64px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
    <div style="font-size:64px;margin-bottom:20px">&#x1F4AC;</div>
    <h2 style="font-size:28px;color:#176C33;font-weight:800;margin:0 0 24px">Pelajaran</h2>
    <p style="font-size:22px;color:#1F2937;text-align:center;line-height:1.7;max-width:550px;font-style:italic;font-weight:500">
      {item.moral}
    </p>
    <div style="margin-top:auto;padding-top:32px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
