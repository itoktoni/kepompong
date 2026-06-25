<script>
  import { coverImage, APP_NAME } from './_img.js'
  let { item, type } = $props()
  const questions = item.questions || item.data?.questions || []
  const cover = coverImage(type, item)
  const PAGE_H = 1123
</script>

<div style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;background:linear-gradient(135deg,#E0F2F1 0%,#fff 100%);font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
  {#if cover}
    <img src={cover} alt="" style="width:320px;height:320px;object-fit:cover;border-radius:24px;border:4px solid #80CBC4;box-shadow:0 8px 32px rgba(0,0,0,0.12);margin-bottom:32px" />
  {:else}
    <div style="font-size:80px;margin-bottom:24px">&#x1F9E9;</div>
  {/if}
  <h1 style="font-size:36px;color:#00695C;font-weight:800;text-align:center;margin:0 0 12px;line-height:1.2">{item.title}</h1>
  {#if item.desc}<p style="font-size:16px;color:#6B7280;text-align:center;margin:0 0 8px;max-width:500px">{item.desc}</p>{/if}
  {#if item.creator}<p style="font-size:13px;color:#9CA3AF;margin-top:16px">Dibuat oleh: {item.creator}</p>{/if}
  <div style="margin-top:auto;padding-top:32px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
</div>

{#each questions as q, i}
  <div style="width:794px;height:{PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:48px 48px 40px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;background:#fff">
    <div style="text-align:center;margin-bottom:20px">
      <span style="display:inline-block;width:36px;height:36px;border-radius:50%;background:#00695C;color:#fff;font-size:16px;font-weight:700;line-height:36px">{i + 1}</span>
    </div>
    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:0">
      <p style="font-size:24px;color:#1F2937;text-align:center;line-height:1.6;max-width:600px;font-weight:600;margin:0 0 24px">
        {q.question || q.text || q.q || ''}
      </p>
      {#if q.options?.length}
        <div style="display:flex;flex-direction:column;gap:10px;max-width:450px;width:100%">
          {#each q.options as opt, j}
            <div style="padding:12px 16px;background:#E0F2F1;border-radius:10px;font-size:17px;color:#1F2937;text-align:center">{opt}</div>
          {/each}
        </div>
      {/if}
      {#if q.answer}
        <p style="font-size:18px;color:#00695C;font-weight:700;margin:20px 0 0">&#x2705; {q.answer}</p>
      {/if}
    </div>
    <div style="text-align:center;padding-top:16px;font-size:11px;color:#9CA3AF">{APP_NAME}</div>
  </div>
{/each}
