<script>
  let { item } = $props()
  const slides = item.slides || item.data?.slides || []
</script>

<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:#1F2937;padding:32px;width:794px;box-sizing:border-box;background:#fff">
  <div style="text-align:center;margin-bottom:24px;padding-bottom:20px;border-bottom:3px solid #B7D9BC">
    <div style="font-size:48px;line-height:1">&#x1FAA3;</div>
    <h1 style="font-size:24px;color:#176C33;margin:8px 0 4px;font-weight:800;line-height:1.3">{item.title}</h1>
    {#if item.desc}<p style="font-size:12px;color:#6B7280;margin:4px 0">{item.desc}</p>{/if}
    {#if item.creator}<p style="font-size:11px;color:#6B7280;margin-top:6px">Dibuat oleh: {item.creator}</p>{/if}
  </div>

  {#each slides as s, i}
    <div style="margin-bottom:12px;padding:14px;background:#fff;border-radius:12px;border:2px solid #B7D9BC;page-break-inside:avoid">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px">
        <div style="width:32px;height:32px;border-radius:50%;background:#176C33;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">{i + 1}</div>
        <div>
          <div style="font-size:18px;font-weight:800;color:#5D4037">{s.nama}</div>
          {#if s.english}<div style="font-size:13px;color:#9CA3AF;font-style:italic">{s.english}</div>{/if}
        </div>
      </div>
      {#if s.digunakan_untuk}<div style="margin-bottom:2px;font-size:12px;line-height:1.6"><strong>Digunakan:</strong> {s.digunakan_untuk}</div>{/if}
      {#if s.fungsi}<div style="margin-bottom:2px;font-size:12px;line-height:1.6"><strong>Fungsi:</strong> {s.fungsi}</div>{/if}
      {#if s.spesifikasi}<div style="margin-bottom:2px;font-size:12px;line-height:1.6"><strong>Ciri:</strong> {s.spesifikasi}</div>{/if}
      {#if s.fakta}<div style="margin-top:6px;padding:8px 10px;background:#FFF8E1;border-radius:8px;font-size:12px"><strong>&#x1F4A1; Fakta:</strong> {s.fakta}</div>{/if}
    </div>
  {/each}

  <div style="text-align:center;margin-top:32px;padding-top:12px;border-top:2px solid #B7D9BC;font-size:10px;color:#9CA3AF">Jejak Tumbuh &mdash; Pendamping Anak</div>
</div>
