import { coverImage, pageImage, APP_NAME, PAGE_W, PAGE_H } from './templates/_img.js'

const FONTS_CSS = `
@font-face { font-family: 'Fredoka'; src: url('/fonts/Fredoka-Regular.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
@font-face { font-family: 'Fredoka'; src: url('/fonts/Fredoka-Medium.ttf') format('truetype'); font-weight: 500; font-style: normal; font-display: swap; }
@font-face { font-family: 'Fredoka'; src: url('/fonts/Fredoka-SemiBold.ttf') format('truetype'); font-weight: 600; font-style: normal; font-display: swap; }
@font-face { font-family: 'Fredoka'; src: url('/fonts/Fredoka-Bold.ttf') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }
.drop-cap::first-letter { float:left; font-family:'Fredoka',sans-serif; font-size:44px; line-height:36px; padding-top:4px; padding-right:6px; padding-left:2px; color:#005da7; font-weight:700; }
`

function esc(s) {
  if (!s) return ''
  return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')
}

function coverPage(item, coverSrc) {
  const imgHtml = coverSrc
    ? `<img src="${esc(coverSrc)}" style="width:100%;max-width:280px;aspect-ratio:1/1;object-fit:cover;border-radius:16px;border:5px solid #e0e3e5;box-shadow:0 8px 24px rgba(0,0,0,0.12)" />`
    : `<div style="font-size:60px">&#x1F4D6;</div>`

  return `<div style="width:${PAGE_W}px;height:${PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:space-between;padding:60px 36px;text-align:center;background:#fff;position:relative;overflow:hidden;font-family:'Fredoka',sans-serif">
    <div style="position:absolute;inset:0;opacity:0.04;pointer-events:none;background-image:url('https://www.transparenttextures.com/patterns/paper.png')"></div>
    <div style="z-index:10;margin-top:48px">
      <h1 style="font-family:'Fredoka',sans-serif;font-size:32px;line-height:1.15;letter-spacing:-0.02em;font-weight:700;color:#005da7;margin:0 0 16px;max-width:460px">${esc(item.title)}</h1>
      ${item.creator ? `<p style="font-family:'Fredoka',sans-serif;font-size:14px;line-height:1.4;font-weight:600;color:#717783;margin:0">Authored by <span style="color:#735c00;font-weight:700;font-style:italic">${esc(item.creator)}</span></p>` : ''}
    </div>
    <div style="z-index:10">${imgHtml}</div>
    <div style="z-index:10;margin-bottom:48px;display:flex;flex-direction:column;align-items:center">
      <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin:20px 0">
        <div style="width:6px;height:6px;border-radius:50%;background:#005da7"></div>
        <div style="width:8px;height:8px;border-radius:50%;background:#005da7"></div>
        <div style="width:6px;height:6px;border-radius:50%;background:#005da7"></div>
      </div>
      <p style="font-size:10px;line-height:1;letter-spacing:0.05em;font-weight:700;text-transform:uppercase;color:#717783;margin:0">${esc(APP_NAME)}</p>
    </div>
  </div>`
}

function contentPage(pageData, index, totalPages, imgSrc) {
  const isFirst = index === 0
  const text = esc(pageData.text || pageData.desc || pageData.step || pageData.narrator || '')
  const dropCapStyle = isFirst ? 'drop-cap' : ''

  const imgHtml = imgSrc
    ? `<div style="width:100%;aspect-ratio:1/1;margin-bottom:24px;border-radius:12px;overflow:hidden;border:5px solid #eceef0;box-shadow:0 3px 12px rgba(0,0,0,0.06)"><img src="${esc(imgSrc)}" style="width:100%;height:100%;object-fit:cover;display:block" /></div>`
    : ''

  return `<div style="width:${PAGE_W}px;height:${PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;padding:36px 40px 52px;background:#fff;position:relative;overflow:hidden;font-family:'Fredoka',sans-serif">
    <div style="position:absolute;inset:0;opacity:0.04;pointer-events:none;background-image:url('https://www.transparenttextures.com/patterns/paper.png')"></div>
    <div style="flex:1;display:flex;flex-direction:column;z-index:10">
      ${imgHtml}
      <p style="font-size:22px;line-height:1.7;font-family:'Fredoka',sans-serif;font-weight:500;color:#191c1e;margin:0;text-align:left;word-break:normal;overflow-wrap:normal;hyphens:none">${text}</p>
    </div>
    <div style="position:absolute;bottom:40px;left:0;right:0;text-align:center;font-family:'Fredoka',sans-serif;font-weight:700;font-size:11px;color:#64748b;z-index:10">${index + 2}</div>
  </div>`
}

function endPage(item) {
  const moralHtml = item.moral ? `
      <div style="margin-top:30px;margin-bottom:20px">
        <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:10px">
          <span style="font-size:18px">&#x1F4AC;</span>
          <p style="font-family:'Fredoka',sans-serif;font-size:18px;font-weight:600;color:#005da7;margin:0">Pelajaran</p>
        </div>
        <p style="font-family:'Fredoka',sans-serif;font-size:25px;line-height:1.5;color:#191c1e;text-align:center;max-width:340px;font-style:italic;font-weight:400;margin:0">${esc(item.moral)}</p>
      </div>` : ''

  const creatorHtml = item.creator ? `
      <div style="padding:12px 16px;background:#f7f9fb;border-radius:12px;border:2px dashed #c1c7d3;text-align:center">
        <p style="font-family:'Fredoka',sans-serif;font-size:9px;line-height:1;letter-spacing:0.05em;font-weight:600;text-transform:uppercase;color:#717783;margin:0 0 4px">A STORY CREATED FOR</p>
        <p style="font-family:'Fredoka',sans-serif;font-size:14px;line-height:1.4;font-weight:600;color:#191c1e;margin:0">${esc(item.creator)}</p>
      </div>` : ''

  return `<div style="width:${PAGE_W}px;height:${PAGE_H}px;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:36px 28px;background:linear-gradient(135deg,#F0FAF2 0%,#fff 100%);position:relative;overflow:hidden;font-family:'Fredoka',sans-serif">
    <div style="position:absolute;inset:0;opacity:0.04;pointer-events:none;background-image:url('https://www.transparenttextures.com/patterns/paper.png')"></div>
    <div style="z-index:10;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;width:100%;flex:1">
      <h2 style="font-family:'Fredoka',sans-serif;font-size:25px;line-height:1.2;font-weight:700;color:#005da7;margin:0 0 14px">The End</h2>
      <div style="display:flex;justify-content:center;gap:6px;margin-bottom:18px">
        <span style="font-size:18px;color:#005da7">&#x2B50;</span>
        <span style="font-size:24px;color:#005da7">&#x2B50;</span>
        <span style="font-size:18px;color:#005da7">&#x2B50;</span>
      </div>
      ${moralHtml}
      ${creatorHtml}
    </div>
    <div style="font-size:9px;color:#9CA3AF;z-index:10">${esc(APP_NAME)}</div>
  </div>`
}

export function buildStorytellingPages(item) {
  const type = 'storytelling'
  const pages = item.pages || item.data?.pages || []
  const slug = item.slug || item.id
  const cover = coverImage(type, item)

  const htmlPages = []

  htmlPages.push(coverPage(item, cover))

  for (let i = 0; i < pages.length; i++) {
    const imgSrc = pageImage(type, item, pages[i], i)
    htmlPages.push(contentPage(pages[i], i, pages.length, imgSrc))
  }

  htmlPages.push(endPage(item))

  return htmlPages
}

export function getFontsCSS() {
  return FONTS_CSS
}

export function getDropCapCSS() {
  return `.drop-cap::first-letter{float:left;font-family:'Fredoka',sans-serif;font-size:44px;line-height:36px;padding-top:4px;padding-right:6px;padding-left:2px;color:#005da7;font-weight:800}`
}
