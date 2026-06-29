<script>
  import { pageImage, coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const materials = item.materials || item.data?.materials || []
  const steps = item.steps || item.data?.steps || []
  const pages = item.pages || item.data?.pages || []
  const explanation = item.explanation || item.data?.explanation || ''
  const allSteps = pages.length ? pages : steps
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#E3F2FD 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #90CAF9;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F52C;</div>
  {/if}
  <h1 style="font-size:22px;color:#1565C0;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#if materials.length}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#1565C0;color:#fff;font-size:10px;font-weight:700;line-height:24px">&#x1F9F0;</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0;gap:7px;max-width:300px">
      {#each materials as m}
        <div style="display:flex;align-items:center;gap:6px;width:100%;padding:7px 10px;background:#E3F2FD;border-radius:6px">
          <span style="color:#1565C0;font-size:10px">&#x25AA;</span>
          <p style="font-size:10px;color:#1F2937;margin:0">{m}</p>
        </div>
      {/each}
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}

{#each allSteps as step, i}
  {@const s = typeof step === 'string' ? {} : step}
  {@const imgSrc = pageImage(type, item, s, i)}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#1565C0;color:#fff;font-size:10px;font-weight:700;line-height:24px">{i + 1}</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      {#if imgSrc}
        <img src={imgSrc} alt="" style="max-width:100%;max-height:260px;object-fit:contain;border-radius:10px;margin-bottom:16px" />
      {/if}
      <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.6;max-width:360px;font-weight:500;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">
        {typeof step === 'string' ? step : step.text || step.desc || step.step || ''}
      </p>
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/each}

{#if explanation}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px;background:linear-gradient(135deg,#E3F2FD 0%,#fff 100%);font-family:'Fredoka',sans-serif">
    <div style="font-size:36px;margin-bottom:12px">&#x1F4A1;</div>
    <h2 style="font-size:16px;color:#1565C0;font-weight:800;margin:0 0 14px">Penjelasan</h2>
    <p style="font-size:12px;color:#1F2937;text-align:center;line-height:1.7;max-width:320px;font-weight:500;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">{explanation}</p>
    <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
