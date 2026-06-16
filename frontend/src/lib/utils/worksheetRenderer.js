// Worksheet HTML Renderer — opens standalone printable HTML in new window
// Each template generates a full HTML page with A4 layout, print toolbar, and @media print CSS

const BASE_STYLES = `
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap');
  @page { size: A4; margin: 0; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Nunito', sans-serif; background: #f0f0f0; }
  .toolbar {
    position: fixed; top: 0; left: 0; right: 0;
    background: linear-gradient(135deg, #176c33, #2E7D32);
    color: white; padding: 10px 20px;
    display: flex; align-items: center; justify-content: space-between;
    z-index: 9999; box-shadow: 0 2px 12px rgba(0,0,0,0.3);
  }
  .toolbar-title { font-size: 15px; font-weight: 700; }
  .toolbar-actions { display: flex; gap: 8px; }
  .btn-toolbar {
    background: white; color: #176c33; border: none;
    padding: 8px 18px; border-radius: 8px;
    font-weight: 700; font-size: 13px; cursor: pointer;
  }
  .btn-toolbar:hover { opacity: 0.9; }
  .page-container { padding: 70px 20px 20px; display: flex; flex-direction: column; align-items: center; gap: 20px; }
  .ws-page {
    width: 794px; min-height: 1123px; background: white;
    padding: 40px 50px; box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    page-break-after: always; position: relative;
  }
  .ws-page:last-child { page-break-after: auto; }
  .ws-header { display: flex; gap: 8px; margin-bottom: 20px; }
  .ws-header-box {
    border: 2.5px solid #222; border-radius: 12px;
    padding: 10px 16px; display: flex; align-items: center; gap: 8px;
  }
  .ws-title-box { flex: 2; justify-content: center; }
  .ws-name-box { flex: 1.5; }
  .ws-date-box { flex: 1; }
  .ws-title { font-size: 20px; font-weight: 800; color: #222; }
  .ws-label { font-size: 13px; font-weight: 700; color: #222; }
  .ws-value { font-size: 13px; color: #555; border-bottom: 1px dashed #aaa; flex: 1; min-width: 60px; }
  .ws-section-title { font-size: 16px; font-weight: 700; color: #176c33; margin: 16px 0 10px; }
  @media print {
    body { background: white; }
    .toolbar { display: none !important; }
    .page-container { padding: 0; }
    .ws-page { box-shadow: none; width: 100%; min-height: 0; height: 297mm; padding: 12mm 15mm; overflow: hidden; }
  }
`

function todayStr() {
  return new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function headerHTML(title, emoji) {
  return `
    <div class="ws-header">
      <div class="ws-header-box ws-title-box">
        <span class="ws-title">${emoji} ${title}</span>
      </div>
      <div class="ws-header-box ws-name-box">
        <span class="ws-label">Nama:</span>
        <span class="ws-value">&nbsp;</span>
      </div>
      <div class="ws-header-box ws-date-box">
        <span class="ws-label">Tgl:</span>
        <span class="ws-value">${todayStr()}</span>
      </div>
    </div>`
}

function wrapPage(title, emoji, bodyContent, extraStyles = '') {
  return `<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>${title}</title>
<style>${BASE_STYLES}${extraStyles}</style></head>
<body>
<div class="toolbar">
  <span class="toolbar-title">${emoji} ${title}</span>
  <div class="toolbar-actions">
    <button class="btn-toolbar" onclick="window.print()">Print / Save PDF</button>
    <button class="btn-toolbar" onclick="window.close()">Tutup</button>
  </div>
</div>
<div class="page-container">
  <div class="ws-page">
    ${headerHTML(title, emoji)}
    ${bodyContent}
  </div>
</div>
</body></html>`
}

function openWorksheet(html) {
  const w = window.open('', '_blank', 'width=850,height=1100')
  w.document.write(html)
  w.document.close()
}

// ============ HUBUNGKAN TITIK (DOT-TO-DOT) ============
export function renderDotToDot() {
  const shapes = [
    { name: 'Bintang', points: [[50,5],[62,35],[95,38],[70,58],[80,90],[50,72],[20,90],[30,58],[5,38],[38,35]] },
    { name: 'Rumah', points: [[30,80],[30,45],[50,20],[70,45],[70,80]] },
    { name: 'Ikan', points: [[20,50],[35,35],[55,40],[70,35],[85,50],[70,65],[55,60],[35,65],[20,50]] },
    { name: 'Pohon', points: [[50,10],[35,30],[25,50],[40,50],[40,85],[60,85],[60,50],[75,50],[65,30],[50,10]] },
    { name: 'Kucing', points: [[35,25],[30,10],[40,20],[50,15],[60,20],[70,10],[65,25],[75,40],[75,60],[65,75],[50,80],[35,75],[25,60],[25,40],[35,25]] },
  ]
  const shape = shapes[Math.floor(Math.random() * shapes.length)]

  const dotsHTML = shape.points.map((p, i) =>
    `<div style="position:absolute;left:${p.x}%;top:${p.y}%;transform:translate(-50%,-50%);width:28px;height:28px;border-radius:50%;border:2px solid #00897B;background:white;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#00695C;">${i + 1}</div>`
  ).join('')

  const svgLine = shape.points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ') + ' Z'

  const styles = `
    .dot-area { position:relative; width:100%; max-width:420px; height:380px; background:#E0F2F1; border:2px solid #00897B; border-radius:16px; margin:10px auto; }
    .hint-box { text-align:center; font-size:20px; font-weight:700; color:#00695C; margin-top:16px; padding:12px; background:#E0F2F1; border-radius:10px; }
    .answer-area { position:relative; width:100%; max-width:420px; height:380px; border:2px dashed #B7D9BC; border-radius:16px; margin:10px auto; }
  `

  const body = `
    <div class="dot-area">${dotsHTML}</div>
    <div class="hint-box">Gambar apa yang terbentuk? Tulis di bawah!</div>
    <div style="margin-top:12px;border-bottom:2px dashed #aaa;height:40px;"></div>
    <div style="text-align:center;margin-top:20px;"><span style="font-size:16px;font-weight:700;color:#176c33;">Halaman 2: Gambar sendiri!</span></div>
    <div class="answer-area">
      <svg viewBox="0 0 100 100" style="width:100%;height:100%;opacity:0.08;">
        <path d="${svgLine}" fill="none" stroke="#00897B" stroke-width="0.5" stroke-dasharray="2,2"/>
      </svg>
    </div>
    <div style="text-align:center;margin-top:8px;font-size:12px;color:#888;">Hubungkan titik di atas, lalu gambar bentuk yang sama di kotak ini!</div>
  `

  openWorksheet(wrapPage('Hubungkan Titik', '🔗', body, styles))
}

// ============ TRACING HURUF ============
export function renderTracingHuruf() {
  const letters = 'ABCDEFGHIJKLM'.split('')
  const styles = `
    .trace-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:10px; }
    .trace-cell { border:2px solid #B7D9BC; border-radius:12px; padding:10px; text-align:center; }
    .trace-letter { font-size:60px; font-weight:800; color:#ddd; line-height:1; user-select:none; -webkit-text-stroke:2px #ccc; }
    .trace-line { border-bottom:2px dashed #ccc; margin-top:30px; height:0; }
  `

  const cells = letters.map(l =>
    `<div class="trace-cell"><div class="trace-letter">${l}</div><div class="trace-line"></div><div class="trace-line"></div></div>`
  ).join('')

  const body = `
    <p class="ws-section-title">Ikuti garis putus-putus huruf berikut!</p>
    <div class="trace-grid">${cells}</div>
  `

  openWorksheet(wrapPage('Mengikuti Garis Huruf', '✏️', body, styles))
}

// ============ TRACING ANGKA ============
export function renderTracingAngka() {
  const numbers = ['1','2','3','4','5','6','7','8','9','10']
  const styles = `
    .trace-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-top:10px; }
    .trace-cell { border:2px solid #B7D9BC; border-radius:12px; padding:10px; text-align:center; }
    .trace-num { font-size:70px; font-weight:800; color:#ddd; line-height:1; user-select:none; -webkit-text-stroke:2px #ccc; }
    .trace-line { border-bottom:2px dashed #ccc; margin-top:30px; height:0; }
  `

  const cells = numbers.map(n =>
    `<div class="trace-cell"><div class="trace-num">${n}</div><div class="trace-line"></div><div class="trace-line"></div></div>`
  ).join('')

  const body = `
    <p class="ws-section-title">Ikuti garis putus-putus angka berikut!</p>
    <div class="trace-grid">${cells}</div>
  `

  openWorksheet(wrapPage('Mengikuti Garis Angka', '🔢', body, styles))
}

// ============ MEWARNAI HURUF ============
export function renderMewarnaiHuruf() {
  const letters = 'ABCDEFGHIJKLM'.split('')
  const styles = `
    .color-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-top:10px; }
    .color-cell { border:3px solid #90CAF9; border-radius:16px; padding:16px; text-align:center; }
    .color-letter { font-size:80px; font-weight:800; color:#1565C0; line-height:1; }
  `

  const cells = letters.map(l =>
    `<div class="color-cell"><div class="color-letter">${l}</div></div>`
  ).join('')

  const body = `
    <p class="ws-section-title">Warnai huruf-huruf berikut dengan krayon!</p>
    <div class="color-grid">${cells}</div>
  `

  openWorksheet(wrapPage('Mewarnai Huruf', '🖍️', body, styles))
}

// ============ MEWARNAI ANGKA ============
export function renderMewarnaiAngka() {
  const items = [
    { num: '1', emoji: '🍎' }, { num: '2', emoji: '🍊' }, { num: '3', emoji: '🍋' },
    { num: '4', emoji: '🍇' }, { num: '5', emoji: '🍓' }, { num: '6', emoji: '🍌' },
    { num: '7', emoji: '🥝' }, { num: '8', emoji: '🍑' }, { num: '9', emoji: '🍒' },
    { num: '10', emoji: '🍉' },
  ]
  const styles = `
    .num-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-top:10px; }
    .num-cell { border:3px solid #CE93D8; border-radius:16px; padding:14px; text-align:center; display:flex; align-items:center; gap:12px; }
    .num-big { font-size:48px; font-weight:800; color:#6A1B9A; min-width:60px; }
    .num-emoji { font-size:40px; }
    .num-dots { display:flex; gap:4px; flex-wrap:wrap; justify-content:center; }
    .num-dot { width:20px; height:20px; border-radius:50%; border:2px solid #CE93D8; }
  `

  const cells = items.map(it => {
    const dots = Array.from({ length: parseInt(it.num) }, () => '<div class="num-dot"></div>').join('')
    return `<div class="num-cell"><span class="num-big">${it.num}</span><span class="num-emoji">${it.emoji}</span><div class="num-dots">${dots}</div></div>`
  }).join('')

  const body = `
    <p class="ws-section-title">Warnai lingkaran sesuai angka!</p>
    <div class="num-grid">${cells}</div>
  `

  openWorksheet(wrapPage('Mewarnai Angka', '🎨', body, styles))
}

// ============ PENJUMLAHAN ============
export function renderPenjumlahan() {
  const items = Array.from({ length: 8 }, () => {
    const a = Math.floor(Math.random() * 9) + 1
    const b = Math.floor(Math.random() * (10 - a)) + 1
    return { a, b, op: '+' }
  })
  const styles = `
    .math-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-top:10px; }
    .math-cell { border:2.5px solid #A5D6A7; border-radius:14px; padding:14px; text-align:center; }
    .math-problem { font-size:32px; font-weight:800; color:#2E7D32; }
    .math-answer { border-bottom:2.5px dashed #2E7D32; width:50px; height:36px; display:inline-block; margin-left:8px; }
  `

  const cells = items.map(it =>
    `<div class="math-cell"><span class="math-problem">${it.a} ${it.op} ${it.b} =</span><span class="math-answer"></span></div>`
  ).join('')

  const body = `
    <p class="ws-section-title">Isi jawaban yang benar!</p>
    <div class="math-grid">${cells}</div>
  `

  openWorksheet(wrapPage('Penjumlahan', '➕', body, styles))
}

// ============ PENGURANGAN ============
export function renderPengurangan() {
  const items = Array.from({ length: 8 }, () => {
    const a = Math.floor(Math.random() * 9) + 2
    const b = Math.floor(Math.random() * (a - 1)) + 1
    return { a, b, op: '−' }
  })
  const styles = `
    .math-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:16px; margin-top:10px; }
    .math-cell { border:2.5px solid #90CAF9; border-radius:14px; padding:14px; text-align:center; }
    .math-problem { font-size:32px; font-weight:800; color:#1565C0; }
    .math-answer { border-bottom:2.5px dashed #1565C0; width:50px; height:36px; display:inline-block; margin-left:8px; }
  `

  const cells = items.map(it =>
    `<div class="math-cell"><span class="math-problem">${it.a} ${it.op} ${it.b} =</span><span class="math-answer"></span></div>`
  ).join('')

  const body = `
    <p class="ws-section-title">Isi jawaban yang benar!</p>
    <div class="math-grid">${cells}</div>
  `

  openWorksheet(wrapPage('Pengurangan', '➖', body, styles))
}

// ============ MAZE (LABIRIN) ============
export function renderMaze() {
  const cols = 8, rows = 10
  const cells = Array.from({ length: rows }, () =>
    Array.from({ length: cols }, () => ({ top: true, right: true, bottom: true, left: true, visited: false }))
  )
  const stack = [[0, 0]]
  cells[0][0].visited = true
  const dirs = [
    { dr: -1, dc: 0, wall: 'top', opposite: 'bottom' },
    { dr: 0, dc: 1, wall: 'right', opposite: 'left' },
    { dr: 1, dc: 0, wall: 'bottom', opposite: 'top' },
    { dr: 0, dc: -1, wall: 'left', opposite: 'right' },
  ]
  while (stack.length > 0) {
    const [r, c] = stack[stack.length - 1]
    const neighbors = []
    for (const d of dirs) {
      const nr = r + d.dr, nc = c + d.dc
      if (nr >= 0 && nr < rows && nc >= 0 && nc < cols && !cells[nr][nc].visited) {
        neighbors.push({ nr, nc, wall: d.wall, opposite: d.opposite })
      }
    }
    if (neighbors.length > 0) {
      const { nr, nc, wall, opposite } = neighbors[Math.floor(Math.random() * neighbors.length)]
      cells[r][c][wall] = false
      cells[nr][nc][opposite] = false
      cells[nr][nc].visited = true
      stack.push([nr, nc])
    } else stack.pop()
  }
  cells[0][0].left = false
  cells[rows - 1][cols - 1].right = false

  const cellSize = 40
  const w = cols * cellSize, h = rows * cellSize
  let lines = ''
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const x = c * cellSize, y = r * cellSize
      const cell = cells[r][c]
      if (cell.top) lines += `<line x1="${x}" y1="${y}" x2="${x + cellSize}" y2="${y}" stroke="#333" stroke-width="2"/>`
      if (cell.right) lines += `<line x1="${x + cellSize}" y1="${y}" x2="${x + cellSize}" y2="${y + cellSize}" stroke="#333" stroke-width="2"/>`
      if (cell.bottom) lines += `<line x1="${x}" y1="${y + cellSize}" x2="${x + cellSize}" y2="${y + cellSize}" stroke="#333" stroke-width="2"/>`
      if (cell.left) lines += `<line x1="${x}" y1="${y}" x2="${x}" y2="${y + cellSize}" stroke="#333" stroke-width="2"/>`
    }
  }

  const styles = `.maze-wrap { text-align:center; margin-top:10px; } .maze-svg { border:2px solid #333; border-radius:8px; }`

  const body = `
    <p class="ws-section-title">Temukan jalan dari START ke FINISH!</p>
    <div class="maze-wrap">
      <svg class="maze-svg" width="${w + 4}" height="${h + 4}" viewBox="-2 -2 ${w + 4} ${h + 4}">
        ${lines}
        <text x="${cellSize / 2}" y="${cellSize / 2 + 5}" text-anchor="middle" font-size="14" font-weight="bold" fill="#176c33">▶</text>
        <text x="${(cols - 1) * cellSize + cellSize / 2}" y="${(rows - 1) * cellSize + cellSize / 2 + 5}" text-anchor="middle" font-size="14" font-weight="bold" fill="#D32F2F">★</text>
      </svg>
    </div>
  `

  openWorksheet(wrapPage('Labirin', '🏁', body, styles))
}

// ============ WORD SEARCH ============
export function renderWordSearch() {
  const wordSets = [
    ['KUCING', 'ANJING', 'IKAN', 'BURUNG', 'KELINCI'],
    ['APEL', 'MANGGA', 'JERUK', 'PISANG', 'ANGGUR'],
    ['MERAH', 'BIRU', 'HIJAU', 'KUNING', 'PINK'],
  ]
  const words = wordSets[Math.floor(Math.random() * wordSets.length)]
  const size = 10
  const grid = Array.from({ length: size }, () => Array(size).fill(''))
  const placed = []
  for (const word of words) {
    const upper = word.toUpperCase()
    let ok = false
    for (let attempt = 0; attempt < 50 && !ok; attempt++) {
      const dir = Math.random() < 0.5 ? [0, 1] : [1, 0]
      const maxR = dir[0] === 0 ? size : size - upper.length
      const maxC = dir[1] === 0 ? size : size - upper.length
      const sr = Math.floor(Math.random() * maxR), sc = Math.floor(Math.random() * maxC)
      let can = true
      for (let i = 0; i < upper.length; i++) {
        const r = sr + i * dir[0], c = sc + i * dir[1]
        if (grid[r][c] !== '' && grid[r][c] !== upper[i]) { can = false; break }
      }
      if (can) {
        for (let i = 0; i < upper.length; i++) {
          grid[sr + i * dir[0]][sc + i * dir[1]] = upper[i]
        }
        placed.push(upper); ok = true
      }
    }
  }
  const alpha = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
  for (let r = 0; r < size; r++) for (let c = 0; c < size; c++) if (!grid[r][c]) grid[r][c] = alpha[Math.floor(Math.random() * 26)]

  const cellSize = 36
  const tableHTML = grid.map(row =>
    `<tr>${row.map(ch => `<td style="width:${cellSize}px;height:${cellSize}px;text-align:center;font-size:16px;font-weight:700;border:1px solid #ddd;">${ch}</td>`).join('')}</tr>`
  ).join('')

  const wordsHTML = placed.map(w => `<span style="display:inline-block;background:#FFF3E0;border:2px solid #E65100;border-radius:8px;padding:4px 12px;font-size:14px;font-weight:700;color:#E65100;margin:4px;">${w}</span>`).join('')

  const styles = `.ws-table { border-collapse:collapse; margin:10px auto; } .word-list { text-align:center; margin-top:16px; }`

  const body = `
    <p class="ws-section-title">Temukan kata-kata tersembunyi!</p>
    <table class="ws-table"><tbody>${tableHTML}</tbody></table>
    <div class="word-list"><p style="font-weight:700;margin-bottom:8px;">Cari kata ini:</p>${wordsHTML}</div>
  `

  openWorksheet(wrapPage('Mencari Kata', '🔍', body, styles))
}

// ============ TEMPLATE MAP ============
const templateMap = {
  dot_to_dot: renderDotToDot,
  tracing_huruf: renderTracingHuruf,
  tracing_angka: renderTracingAngka,
  mewarnai_alfabet: renderMewarnaiHuruf,
  mewarnai_angka: renderMewarnaiAngka,
  penjumlahan: renderPenjumlahan,
  pengurangan: renderPengurangan,
  maze: renderMaze,
  word_search: renderWordSearch,
}

export function openWorksheetByType(typeId) {
  const fn = templateMap[typeId]
  if (fn) fn()
  else alert('Worksheet template belum tersedia untuk: ' + typeId)
}

export function hasWorksheetTemplate(typeId) {
  return !!templateMap[typeId]
}
