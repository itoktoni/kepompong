<script>
  let { item } = $props()
  const players = item.players || item.data?.players || ''
  const materials = item.materials || item.data?.materials || []
  const rules = item.rules || item.data?.rules || []
</script>

<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1F2937;padding:32px;width:794px;box-sizing:border-box;background:#fff">
  <div style="text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:3px solid #B7D9BC">
    <div style="font-size:48px;line-height:1">&#x1F3B2;</div>
    <h1 style="font-size:24px;color:#176C33;margin:8px 0 4px;font-weight:800;line-height:1.3">{item.title}</h1>
    {#if item.desc}<p style="font-size:12px;color:#6B7280;margin:4px 0">{item.desc}</p>{/if}
    {#if item.creator}<p style="font-size:11px;color:#6B7280;margin-top:6px">Dibuat oleh: {item.creator}</p>{/if}
  </div>

  {#if players}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F465; Pemain</div>
      <div style="font-size:13px;line-height:1.6">{players}</div>
    </div>
  {/if}
  {#if materials.length}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F9F0; Bahan</div>
      <ul style="margin:0;padding-left:20px">{#each materials as m}<li style="margin-bottom:4px;font-size:13px;line-height:1.5">{m}</li>{/each}</ul>
    </div>
  {/if}
  {#if rules.length}
    <div style="margin-bottom:14px;padding:14px;background:#F0FAF2;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="font-weight:700;color:#176C33;margin-bottom:8px;font-size:13px">&#x1F4CB; Aturan</div>
      <ul style="margin:0;padding-left:20px">{#each rules as r}<li style="margin-bottom:4px;font-size:13px;line-height:1.5">{r}</li>{/each}</ul>
    </div>
  {/if}

  <div style="text-align:center;margin-top:32px;padding-top:12px;border-top:2px solid #B7D9BC;font-size:10px;color:#9CA3AF">Jejak Tumbuh &mdash; Pendamping Anak</div>
</div>
