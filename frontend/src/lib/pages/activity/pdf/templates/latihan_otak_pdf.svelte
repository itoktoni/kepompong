<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const exercises = item.exercises || item.data?.exercises || []
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#EDE7F6 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #B39DDB;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F9E0;</div>
  {/if}
  <h1 style="font-size:22px;color:#4527A0;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each exercises as ex, i}
  {@const text = typeof ex === 'string' ? ex : ex.text || ex.question || ''}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#4527A0;color:#fff;font-size:10px;font-weight:700;line-height:24px">{i + 1}</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      <p style="font-size:14px;color:#1F2937;text-align:center;line-height:1.6;max-width:360px;font-weight:600;margin:0;word-break:normal;overflow-wrap:normal;hyphens:none">
        {text}
      </p>
      {#if ex.options?.length}
        <div style="display:flex;flex-direction:column;gap:6px;max-width:260px;width:100%;margin-top:12px">
          {#each ex.options as opt}
            <div style="padding:7px 10px;background:#EDE7F6;border-radius:6px;font-size:10px;color:#1F2937;text-align:center">{opt}</div>
          {/each}
        </div>
      {/if}
      {#if ex.answer}
        <p style="font-size:11px;color:#4527A0;font-weight:700;margin:12px 0 0;word-break:normal;overflow-wrap:normal;hyphens:none">&#x2705; {ex.answer}</p>
      {/if}
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/each}
