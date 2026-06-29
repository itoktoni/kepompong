const templateModules = {
  storytelling: null,
  bermain_peran: () => import('./templates/bermain_peran_pdf.svelte'),
  permainan: () => import('./templates/permainan_pdf.svelte'),
  monolog: () => import('./templates/monolog_pdf.svelte'),
  proyek_kreatif: () => import('./templates/proyek_kreatif_pdf.svelte'),
  musik_gerak: () => import('./templates/musik_gerak_pdf.svelte'),
  puzzle: () => import('./templates/puzzle_pdf.svelte'),
  mindfulness: () => import('./templates/mindfulness_pdf.svelte'),
  outdoor: () => import('./templates/outdoor_pdf.svelte'),
  ilmu_pengetahuan: () => import('./templates/ilmu_pengetahuan_pdf.svelte'),
  tebak_tebakan: () => import('./templates/tebak_tebakan_pdf.svelte'),
  permainan_tangan: () => import('./templates/permainan_tangan_pdf.svelte'),
  latihan_otak: () => import('./templates/latihan_otak_pdf.svelte'),
  komik: () => import('./templates/komik_pdf.svelte'),
  worksheet: () => import('./templates/worksheet_pdf.svelte'),
  coloring: () => import('./templates/coloring_pdf.svelte'),
  mengenal_kata: () => import('./templates/mengenal_kata_pdf.svelte'),
}

async function fetchAsDataUrl(url) {
  try {
    return await new Promise((resolve, reject) => {
      const img = new Image()
      img.crossOrigin = 'anonymous'
      img.onload = () => {
        const canvas = document.createElement('canvas')
        canvas.width = img.naturalWidth
        canvas.height = img.naturalHeight
        canvas.getContext('2d').drawImage(img, 0, 0)
        resolve(canvas.toDataURL('image/png'))
      }
      img.onerror = reject
      img.src = url
    })
  } catch {
    return null
  }
}

async function embedImages(container) {
  const imgs = Array.from(container.querySelectorAll('img[src]'))
  if (!imgs.length) return
  await Promise.allSettled(imgs.map(async (img) => {
    const src = img.src
    if (src.startsWith('data:')) return
    const dataUrl = await fetchAsDataUrl(src)
    if (dataUrl) {
      img.src = dataUrl
      await new Promise(r => {
        if (img.complete) r()
        else { img.onload = r; img.onerror = r }
      })
    } else {
      img.remove()
    }
  }))
}

async function capturePage(html2canvas, el) {
  const { PAGE_W } = await import('./templates/_img.js')
  await embedImages(el)
  return html2canvas(el, {
    scale: 2,
    useCORS: true,
    allowTaint: true,
    backgroundColor: '#ffffff',
    width: PAGE_W,
    height: el.offsetHeight,
    windowWidth: PAGE_W,
  })
}

async function generateFromHtmlPages(htmlPages, filename, fontsCSS, extraCSS) {
  const { default: jsPDF } = await import('jspdf')
  const { default: html2canvas } = await import('html2canvas')
  const { PAGE_W, PAGE_H } = await import('./templates/_img.js')

  const styleEl = document.createElement('style')
  styleEl.textContent = (fontsCSS || '') + (extraCSS || '')
  document.head.appendChild(styleEl)

  const container = document.createElement('div')
  container.style.cssText = 'position:fixed;left:-9999px;top:0;z-index:-1;pointer-events:none'
  document.body.appendChild(container)

  try {
    await Promise.all([
      document.fonts.load('700 25px "Fredoka"'),
      document.fonts.load('600 25px "Fredoka"'),
      document.fonts.load('400 25px "Fredoka"'),
    ]).catch(() => {})

    await document.fonts.ready

    const pageWmm = PAGE_W * 25.4 / 96
    const pageHmm = PAGE_H * 25.4 / 96
    const pdf = new jsPDF('p', 'mm', [pageWmm, pageHmm])
    const pdfW = pdf.internal.pageSize.getWidth()
    const pdfH = pdf.internal.pageSize.getHeight()

    for (let i = 0; i < htmlPages.length; i++) {
      if (i > 0) pdf.addPage()
      const pageEl = document.createElement('div')
      pageEl.innerHTML = htmlPages[i]
      container.appendChild(pageEl)
      await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)))
      const canvas = await capturePage(html2canvas, pageEl.firstChild)
      const imgData = canvas.toDataURL('image/jpeg', 0.95)
      pdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH)
      container.removeChild(pageEl)
    }

    pdf.save(filename)
  } finally {
    if (container.parentNode) document.body.removeChild(container)
    if (styleEl.parentNode) document.head.removeChild(styleEl)
  }
}

async function generateStorytellingPdf(item) {
  const { buildStorytellingPages, getDropCapCSS } = await import('./htmlTemplatePdf.js')
  const htmlPages = buildStorytellingPages(item)
  const slug = item.slug || item.id || 'story'
  await generateFromHtmlPages(htmlPages, `storytelling_${slug}.pdf`, null, getDropCapCSS())
}

async function generateSvelteTemplatePdf(item, type) {
  const { mount, unmount } = await import('svelte')
  const { PAGE_W, PAGE_H } = await import('./templates/_img.js')

  const loader = templateModules[type]
  if (!loader) throw new Error(`No PDF template for type: ${type}`)
  const mod = await loader()
  const Component = mod.default

  const styleEl = document.createElement('style')
  styleEl.textContent = `@font-face{font-family:'Fredoka';src:url('/fonts/Fredoka-Regular.ttf') format('truetype');font-weight:400;font-style:normal;font-display:swap}@font-face{font-family:'Fredoka';src:url('/fonts/Fredoka-Medium.ttf') format('truetype');font-weight:500;font-style:normal;font-display:swap}@font-face{font-family:'Fredoka';src:url('/fonts/Fredoka-SemiBold.ttf') format('truetype');font-weight:600;font-style:normal;font-display:swap}@font-face{font-family:'Fredoka';src:url('/fonts/Fredoka-Bold.ttf') format('truetype');font-weight:700;font-style:normal;font-display:swap}`
  document.head.appendChild(styleEl)

  const target = document.createElement('div')
  target.style.cssText = 'position:fixed;left:-9999px;top:0;z-index:-1;pointer-events:none'
  document.body.appendChild(target)

  let component
  try {
    await Promise.all([
      document.fonts.load('700 22px "Fredoka"'),
      document.fonts.load('600 22px "Fredoka"'),
      document.fonts.load('400 12px "Fredoka"'),
    ]).catch(() => {})
    await document.fonts.ready

    component = mount(Component, { target, props: { item, type } })
    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)))

    const sections = Array.from(target.children)
    if (!sections.length) return

    const { default: jsPDF } = await import('jspdf')
    const { default: html2canvas } = await import('html2canvas')

    const pageWmm = PAGE_W * 25.4 / 96
    const pageHmm = PAGE_H * 25.4 / 96
    const pdf = new jsPDF('p', 'mm', [pageWmm, pageHmm])
    const pdfW = pdf.internal.pageSize.getWidth()
    const pdfH = pdf.internal.pageSize.getHeight()

    for (let i = 0; i < sections.length; i++) {
      if (i > 0) pdf.addPage()
      const canvas = await capturePage(html2canvas, sections[i])
      const imgData = canvas.toDataURL('image/jpeg', 0.95)
      pdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH)
    }

    const slug = item.slug || item.id || 'activity'
    pdf.save(`${type}_${slug}.pdf`)
  } finally {
    if (component) unmount(component)
    if (target.parentNode) document.body.removeChild(target)
    if (styleEl.parentNode) document.head.removeChild(styleEl)
  }
}

export async function generatePdf(item, type) {
  if (type === 'storytelling') {
    return generateStorytellingPdf(item)
  }
  return generateSvelteTemplatePdf(item, type)
}
