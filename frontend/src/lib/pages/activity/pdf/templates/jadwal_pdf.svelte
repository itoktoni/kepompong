<script>
  import { APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { schedules = [], childName = 'Anak', today = '' } = $props()

  const undone = schedules.filter(s => !s.done)
  const done = schedules.filter(s => s.done)

  const itemsPerPage = 8
  const pages = []
  for (let i = 0; i < undone.length; i += itemsPerPage) {
    pages.push(undone.slice(i, i + itemsPerPage))
  }
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  <div style="font-size:56px;margin-bottom:16px">📅</div>
  <h1 style="font-size:24px;color:#176C33;font-weight:800;text-align:center;margin:0 0 8px;line-height:1.2">Jadwal Harian</h1>
  <p style="font-size:14px;color:#6B7280;text-align:center;margin:0 0 6px">{childName}</p>
  <p style="font-size:11px;color:#9CA3AF;text-align:center;margin:0">{today}</p>
  <div style="display:flex;gap:24px;margin-top:24px">
    <div style="text-align:center">
      <p style="font-size:28px;font-weight:800;color:#176C33;margin:0">{undone.length}</p>
      <p style="font-size:10px;color:#6B7280;margin:0">Aktif</p>
    </div>
    <div style="text-align:center">
      <p style="font-size:28px;font-weight:800;color:#9CA3AF;margin:0">{done.length}</p>
      <p style="font-size:10px;color:#6B7280;margin:0">Selesai</p>
    </div>
  </div>
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each pages as pageItems, pi}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:32px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid #B7D9BC">
      <span style="font-size:20px">⏰</span>
      <h2 style="font-size:16px;color:#176C33;font-weight:700;margin:0">Jadwal</h2>
      <span style="margin-left:auto;font-size:10px;color:#9CA3AF">Hal. {pi + 1}</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;gap:10px">
      {#each pageItems as s, i}
        <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:#FFF9F3;border:2px solid #B7D9BC;border-radius:14px">
          <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;color:#176C33;font-weight:700">{pi * itemsPerPage + i + 1}</div>
          <div style="flex:1;min-width:0">
            <p style="font-size:13px;color:#1C1B1F;font-weight:600;margin:0">{s.label}</p>
          </div>
          <span style="font-size:11px;color:#6B7280;font-weight:500;white-space:nowrap">{s.time}</span>
        </div>
      {/each}
    </div>
    <div style="margin-top:auto;padding-top:16px;text-align:center;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/each}

{#if done.length > 0}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:32px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;padding-bottom:12px;border-bottom:2px solid #B7D9BC">
      <span style="font-size:20px">✅</span>
      <h2 style="font-size:16px;color:#176C33;font-weight:700;margin:0">Selesai</h2>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;gap:10px">
      {#each done as s, i}
        <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:#F0FAF2;border:2px solid #B7D9BC;border-radius:14px">
          <div style="width:32px;height:32px;border-radius:50%;background:#176C33;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;color:#fff">✓</div>
          <div style="flex:1;min-width:0">
            <p style="font-size:13px;color:#79747E;font-weight:500;margin:0;text-decoration:line-through">{s.label}</p>
          </div>
          <span style="font-size:11px;color:#9CA3AF;font-weight:500;white-space:nowrap">{s.time}</span>
        </div>
      {/each}
    </div>
    <div style="margin-top:auto;padding-top:16px;text-align:center;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/if}
