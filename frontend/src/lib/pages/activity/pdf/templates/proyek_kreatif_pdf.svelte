<script>
  let { item } = $props()
  const materials = item.materials || item.data?.materials || []
  const steps = item.steps || item.data?.steps || []
  const pages = item.pages || item.data?.pages || []
  const allSteps = pages.length ? pages : steps
</script>

<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1F2937;padding:32px;width:794px;box-sizing:border-box;background:#fff">
  <div style="text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:3px solid #B7D9BC">
    <div style="font-size:48px;line-height:1">&#x1F3A8;</div>
    <h1 style="font-size:24px;color:#176C33;margin:8px 0 4px;font-weight:800;line-height:1.3">{item.title}</h1>
    {#if item.desc}<p style="font-size:12px;color:#6B7280;margin:4px 0">{item.desc}</p>{/if}
    {#if item.creator}<p style="font-size:11px;color:#6B7280;margin-top:6px">Dibuat oleh: {item.creator}</p>{/if}
  </div>

  {#if item.duration}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x23F1; Durasi</div>
      <div style="font-size:13px;line-height:1.6">{item.duration}</div>
    </div>
  {/if}
  {#if item.difficulty}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F4CA; Tingkat Kesulitan</div>
      <div style="font-size:13px;line-height:1.6">{item.difficulty}</div>
    </div>
  {/if}
  {#if materials.length}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F9F0; Bahan</div>
      <ul style="margin:0;padding-left:20px">{#each materials as m}<li style="margin-bottom:4px;font-size:13px;line-height:1.5">{m}</li>{/each}</ul>
    </div>
  {/if}

  {#each allSteps as step, i}
    <div style="margin-bottom:12px;padding:14px;background:#fff;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="display:flex;gap:10px;align-items:flex-start">
        <div style="width:28px;height:28px;border-radius:50%;background:#176C33;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">{i + 1}</div>
        <div style="flex:1"><div style="font-size:13px;line-height:1.7">{typeof step === 'string' ? step : step.text || step.desc || step.step || ''}</div></div>
      </div>
    </div>
  {/each}

  {#if item.explanation}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F4A1; Penjelasan</div>
      <div style="font-size:13px;line-height:1.6">{item.explanation}</div>
    </div>
  {/if}

  <div style="text-align:center;margin-top:32px;padding-top:12px;border-top:2px solid #B7D9BC;font-size:10px;color:#9CA3AF">Jejak Tumbuh &mdash; Pendamping Anak</div>
</div>
