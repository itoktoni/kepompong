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

export async function generatePdf(item, type) {
  const { mount, unmount } = await import('svelte')
  const { default: jsPDF } = await import('jspdf')
  const { default: html2canvas } = await import('html2canvas')

  const loader = templateModules[type]
  if (!loader) throw new Error(`No PDF template for type: ${type}`)
  const mod = await loader()
  const Component = mod.default

  const target = document.createElement('div')
  target.style.cssText = 'position:fixed;left:-9999px;top:0;width:794px;z-index:-1;pointer-events:none'
  document.body.appendChild(target)

  let component
  try {
    component = mount(Component, { target, props: { item } })

    await new Promise(r => requestAnimationFrame(() => requestAnimationFrame(r)))

    const contentEl = target.firstElementChild || target

    const canvas = await html2canvas(contentEl, {
      scale: 2,
      useCORS: true,
      allowTaint: true,
      backgroundColor: '#ffffff',
      width: 794,
      windowWidth: 794,
    })

    const imgData = canvas.toDataURL('image/jpeg', 0.95)
    const pdf = new jsPDF('p', 'mm', 'a4')
    const pdfWidth = pdf.internal.pageSize.getWidth()
    const pdfHeight = pdf.internal.pageSize.getHeight()
    const margin = 10
    const contentWidth = pdfWidth - margin * 2
    const imgHeight = (canvas.height * contentWidth) / canvas.width

    let heightLeft = imgHeight
    let position = margin

    pdf.addImage(imgData, 'JPEG', margin, position, contentWidth, imgHeight)
    heightLeft -= (pdfHeight - margin * 2)

    while (heightLeft > 0) {
      position = -(pdfHeight - margin * 2) + margin
      pdf.addPage()
      pdf.addImage(imgData, 'JPEG', margin, position, contentWidth, imgHeight)
      heightLeft -= (pdfHeight - margin * 2)
    }

    const slug = item.slug || item.id || 'activity'
    pdf.save(`${type}_${slug}.pdf`)
  } finally {
    if (component) unmount(component)
    if (target.parentNode) document.body.removeChild(target)
  }
}
