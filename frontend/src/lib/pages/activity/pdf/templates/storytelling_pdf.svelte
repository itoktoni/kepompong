<script>
  let { item } = $props()
  const pages = item.pages || item.data?.pages || []
</script>

<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1F2937;padding:32px;width:794px;box-sizing:border-box;background:#fff">
  <div style="text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:3px solid #B7D9BC">
    <div style="font-size:48px;line-height:1">&#x1F4D6;</div>
    <h1 style="font-size:24px;color:#176C33;margin:8px 0 4px;font-weight:800;line-height:1.3">{item.title}</h1>
    {#if item.desc}
      <p style="font-size:12px;color:#6B7280;margin:4px 0">{item.desc}</p>
    {/if}
    {#if item.creator}
      <p style="font-size:11px;color:#6B7280;margin-top:6px">Dibuat oleh: {item.creator}</p>
    {/if}
  </div>

  {#each pages as page, i}
    <div style="margin-bottom:12px;padding:14px;background:#fff;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="display:flex;gap:10px;align-items:flex-start">
        <div style="width:28px;height:28px;border-radius:50%;background:#176C33;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">{i + 1}</div>
        <div style="flex:1">
          <div style="font-size:13px;line-height:1.7">{page.text || page.desc || page.step || page.narrator || ''}</div>
        </div>
      </div>
    </div>
  {/each}

  {#if item.moral}
    <div style="margin-top:24px;padding:16px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:6px;font-size:14px">&#x1F4AC; Pelajaran</div>
      <div style="font-size:13px;line-height:1.6">{item.moral}</div>
    </div>
  {/if}

  <div style="text-align:center;margin-top:32px;padding-top:12px;border-top:2px solid #B7D9BC;font-size:10px;color:#9CA3AF">
    Jejak Tumbuh &mdash; Pendamping Anak
  </div>
</div>
