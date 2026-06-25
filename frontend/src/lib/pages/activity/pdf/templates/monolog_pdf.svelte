<script>
  let { item } = $props()
  const script = item.script || item.data?.script || ''
  const tips = item.tips || item.data?.tips || []
</script>

<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1F2937;padding:32px;width:794px;box-sizing:border-box;background:#fff">
  <div style="text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:3px solid #B7D9BC">
    <div style="font-size:48px;line-height:1">&#x1F3A4;</div>
    <h1 style="font-size:24px;color:#176C33;margin:8px 0 4px;font-weight:800;line-height:1.3">{item.title}</h1>
    {#if item.desc}<p style="font-size:12px;color:#6B7280;margin:4px 0">{item.desc}</p>{/if}
    {#if item.creator}<p style="font-size:11px;color:#6B7280;margin-top:6px">Dibuat oleh: {item.creator}</p>{/if}
  </div>

  {#if script}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F3A4; Naskah</div>
      <div style="font-size:13px;line-height:1.8;font-style:italic">"{script}"</div>
    </div>
  {/if}
  {#if tips.length}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F4A1; Tips</div>
      <ul style="margin:0;padding-left:20px">{#each tips as t}<li style="margin-bottom:4px;font-size:13px;line-height:1.5">{t}</li>{/each}</ul>
    </div>
  {/if}

  <div style="text-align:center;margin-top:32px;padding-top:12px;border-top:2px solid #B7D9BC;font-size:10px;color:#9CA3AF">Jejak Tumbuh &mdash; Pendamping Anak</div>
</div>
