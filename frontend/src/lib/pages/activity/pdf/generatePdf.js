const templateModules = {
  storytelling: () => import('./templates/storytelling_pdf.svelte'),
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
    const resp = await fetch(url, { credentials: 'omit' })
    if (!resp.ok) return null
    const blob = await resp.blob()
    return new Promise((resolve, reject) => {
      const reader = new FileReader()
      reader.onload = () => resolve(reader.result)
      reader.onerror = reject
      reader.readAsDataURL(blob)
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
  await embedImages(el)
  return html2canvas(el, {
    scale: 2,
    useCORS: false,
    allowTaint: true,
    backgroundColor: '#ffffff',
    width: 794,
    height: el.offsetHeight,
    windowWidth: 794,
  })
}

export async function generatePdf(item, type) {
  const { mount, unmount } = await import('svelte')
  const { default: jsPDF } = await import('jspdf')
  const { default: html2canvas } = await import('html2canvas')

  const loader = templateModules[type]
  if (!loader) throw new Error(`No PDF template for type: ${type}`)
  const mod = await loader()
  const Component = mod.default

  const target = document.createElement('div')
  target.style.cssText = 'position:fixed;left:-9999px;top:0;z-index:-1;pointer-events:none'
  document.body.appendChild(target)

  let component
  try {
    component = mount(Component, { target, props: { item, type } })
    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)))

    const sections = Array.from(target.children)

    if (!sections.length) return

    const pdf = new jsPDF('p', 'mm', 'a4')
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
  }
}
