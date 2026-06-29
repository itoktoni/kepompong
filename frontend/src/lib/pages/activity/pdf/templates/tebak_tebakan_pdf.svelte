<script>
  import { coverImage, APP_NAME, PAGE_W, PAGE_H } from './_img.js'
  let { item, type } = $props()
  const questions = item.questions || item.data?.questions || []
  const cover = coverImage(type, item)
</script>

<div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;background:linear-gradient(135deg,#FFF8E1 0%,#fff 100%);font-family:'Fredoka',sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:100%;height:auto;object-fit:contain;border-radius:14px;border:3px solid #FFE082;box-shadow:0 4px 16px rgba(0,0,0,0.12);margin-bottom:18px" />
  {:else}
    <div style="font-size:48px;margin-bottom:14px">&#x1F914;</div>
  {/if}
  <h1 style="font-size:22px;color:#F57F17;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:10px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:300px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:9px;color:#9CA3AF;margin-top:10px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:20px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each questions as q, i}
  <div style="width:{PAGE_W}px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:28px 28px 24px;font-family:'Fredoka',sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:12px">
      <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:#F57F17;color:#fff;font-size:10px;font-weight:700;line-height:24px">{i + 1}</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      <p style="font-size:14px;color:#1F2937;text-align:center;line-height:1.6;max-width:360px;font-weight:600;margin:0 0 14px;word-break:normal;overflow-wrap:normal;hyphens:none">
        {q.question || q.text || q.q || ''}
      </p>
      {#if q.options?.length}
        <div style="display:flex;flex-direction:column;gap:6px;max-width:260px;width:100%">
          {#each q.options as opt}
            <div style="padding:7px 10px;background:#FFF8E1;border-radius:6px;font-size:10px;color:#1F2937;text-align:center">{opt}</div>
          {/each}
        </div>
      {/if}
      {#if q.answer}
        <p style="font-size:11px;color:#F57F17;font-weight:700;margin:12px 0 0;word-break:normal;overflow-wrap:normal;hyphens:none">&#x2705; {q.answer}</p>
      {/if}
      {#if q.explanation}
        <p style="font-size:9px;color:#6B7280;margin:7px 0 0;max-width:300px;text-align:center">{q.explanation}</p>
      {/if}
    </div>
    <div style="text-align:center;padding-top:10px;font-size:8px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/each}
