import { snapdom } from '@zumer/snapdom'

const appName = import.meta.env.VITE_APP_NAME || 'Jejak Tumbuh'
const appTagline = import.meta.env.VITE_APP_TAGLINE || 'Pendamping Anak'
const appUrl = import.meta.env.VITE_APP_URL || 'https://halobunda.app'

function generateRefCode() {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'
  let code = ''
  for (let i = 0; i < 6; i++) code += chars[Math.floor(Math.random() * chars.length)]
  return code
}

function buildCardHTML({ type, title, emoji, childName, category, points, maxPoints, percent, items, referralCode, isComplete, notes }) {
  const refUrl = referralCode ? `${appUrl}?ref=${referralCode}` : appUrl
  const refDisplay = referralCode ? `${appUrl.replace('https://', '')}?ref=${referralCode}` : appUrl.replace('https://', '')

  let headline = ''
  let description = ''
  let trophyEmoji = '💪'

  if (type === 'challenge') {
    headline = isComplete ? 'YAY! HEBAT MAKSIMAL' : 'TERUS SEMANGAT YA!'
    if (isComplete) {
      description = `Selamat untuk <b>${childName}</b> baru saja menyelesaikan <b>"${title}"</b> dengan ${maxPoints} poin!`
      trophyEmoji = '🏆'
    } else {
      const pct = maxPoints > 0 ? Math.min(100, Math.round((points / maxPoints) * 100)) : 0
      description = `<b>${childName}</b> sedang mengerjakan <b>"${title}"</b>${notes ? ` ${notes}` : ''}`
    }
  } else if (type === 'checklist') {
    const doneCount = items.filter(i => i.done).length
    headline = 'CHECKLIST SELESAI!'
    description = `<b>${childName}</b> sudah menyelesaikan <b>"${title}"</b> ${doneCount}/${items.length} item.`
    trophyEmoji = '✅'
  } else if (type === 'jadwal') {
    const doneCount = items.filter(i => i.done).length
    headline = 'JADWAL HARIAN'
    description = `<b>${childName}</b> sudah menyelesaikan ${doneCount}/${items.length} jadwal hari ini.`
    trophyEmoji = '📅'
  }

  let listHTML = ''
  if (type === 'checklist' || type === 'jadwal') {
    const shown = items.slice(0, 6)
    listHTML = `
      <div style="margin-top:14px;text-align:left">
        ${shown.map(item => `
          <div style="display:flex;align-items:center;gap:8px;padding:5px 0;font-size:13px;font-weight:700;color:#4b5563">
            <span style="font-size:14px;flex-shrink:0">${item.done ? '✅' : '⬜'}</span>
            <span style="flex:1;${item.done ? 'text-decoration:line-through;opacity:0.6' : ''}">${item.label}</span>
            ${item.time ? `<span style="font-size:11px;color:#9ca3af;flex-shrink:0">${item.time}</span>` : ''}
          </div>
        `).join('')}
        ${items.length > 6 ? `<p style="font-size:11px;color:#9ca3af;text-align:center;margin:6px 0 0">+${items.length - 6} lainnya</p>` : ''}
      </div>
    `
  }

  let progressHTML = ''
  if (type === 'challenge') {
    const pct = maxPoints > 0 ? Math.min(100, Math.round((points / maxPoints) * 100)) : 0
    progressHTML = `
      <div style="margin-top:14px;background:rgba(46,125,50,0.08);border-radius:16px;padding:14px 16px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <span style="font-size:12px;font-weight:800;color:#2e7d32">${pct}% progress</span>
          <span style="font-size:12px;font-weight:700;color:#2e7d32">${points}/${maxPoints} poin</span>
        </div>
        <div style="width:100%;height:10px;background:rgba(46,125,50,0.15);border-radius:9999px;overflow:hidden">
          <div style="height:100%;background:#2e7d32;border-radius:9999px;width:${pct}%"></div>
        </div>
      </div>
    `
  } else if (type === 'checklist') {
    progressHTML = `
      <div style="margin-top:14px;background:rgba(46,125,50,0.08);border-radius:16px;padding:14px 16px">
        <div style="width:100%;height:10px;background:rgba(46,125,50,0.15);border-radius:9999px;overflow:hidden;margin-bottom:8px">
          <div style="height:100%;background:#2e7d32;border-radius:9999px;width:${percent}%"></div>
        </div>
        <p style="font-size:13px;font-weight:900;color:#2e7d32;margin:0;text-align:right">${items.filter(i => i.done).length} / ${items.length} Selesai</p>
      </div>
    `
  }

  return `
    <div style="width:380px;min-height:540px;background-color:#2e7d32;border-radius:0;padding:30px 30px 10px;box-sizing:border-box;position:relative;overflow:hidden;box-shadow:0 20px 0px #1b4d1f,0 30px 50px rgba(27,77,31,0.35);display:flex;flex-direction:column;justify-content:space-between;font-family:'Nunito Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif">
      <div style="position:absolute;width:100px;height:100px;border-radius:50%;background:#ffeaa7;top:-50px;right:-50px;opacity:0.7;z-index:1"></div>
      <div style="position:absolute;width:160px;height:160px;border-radius:50%;background:#a1d2ff;bottom:-80px;left:-80px;opacity:0.7;z-index:1"></div>
      <div style="position:absolute;width:60px;height:60px;border-radius:50%;background:#a3e6b9;top:18%;left:-30px;opacity:0.3;z-index:1"></div>

      <div style="display:flex;align-items:center;z-index:3">
        <div style="background:#fffef9;color:#2e7d32;padding:10px 24px;border-radius:40px;font-weight:900;font-size:14px;box-shadow:0 6px 0px #e6e4dc;display:flex;align-items:center;gap:8px;white-space:nowrap">
          <span style="font-size:18px">✨</span> ${appName} — ${appTagline}
        </div>
      </div>

      <div style="background:#fffef9;border-radius:44px;padding:30px 24px;margin-top:20px;box-shadow:0 12px 0px #ebe9df;z-index:3;flex-grow:1;display:flex;flex-direction:column;justify-content:center;position:relative">
        <h1 style="font-size:30px;font-weight:900;line-height:1.15;color:#2e7d32;margin:0 0 10px 0;text-transform:uppercase;letter-spacing:-0.5px">${headline}</h1>
        <p style="font-size:14px;font-weight:700;color:#4b5563;line-height:1.6;margin:0">${description}</p>
        ${listHTML}
        ${progressHTML}
      </div>

      <div style="display:flex;justify-content:space-between;align-items:center;padding-top:15px;margin-bottom:30px;margin-top:30px;z-index:3;border-top:4px dotted rgba(255,255,255,0.4);position:relative">
        <div style="background:rgba(255,255,255,0.9);border:2.5px solid rgba(255,255,255,0.35);padding:10px 22px;border-radius:20px;display:flex;flex-direction:column;align-items:flex-start;gap:2px;max-width:75%;box-shadow:0 4px 0px rgba(27,77,31,0.2)">
          <span style="font-size:10px;font-weight:900;color:#2e7d32;text-transform:uppercase;letter-spacing:0.8px">Yuk selesaikan misi lagi</span>
          <span style="text-decoration:none;color:#2e7d32;font-size:13px;font-weight:900;word-break:break-all">${refDisplay}</span>
        </div>
        <div style="font-size:46px;filter:drop-shadow(0px 5px 0px rgba(0,0,0,0.1));margin-right:5px">${trophyEmoji}</div>
      </div>
    </div>
  `
}

async function captureAndShare(html, filename) {
  const container = document.createElement('div')
  container.style.position = 'fixed'
  container.style.left = '-9999px'
  container.style.top = '0'
  container.style.zIndex = '-1'
  container.style.pointerEvents = 'none'
  container.innerHTML = html
  document.body.appendChild(container)

  try {
    const blob = await snapdom.toBlob(container.firstElementChild, { type: 'jpeg', quality: 0.92 })
    const file = new File([blob], `${filename}.jpg`, { type: 'image/jpeg' })

    if (navigator.share && navigator.canShare({ files: [file] })) {
      await navigator.share({ files: [file], title: filename }).catch(() => {})
    } else {
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `${filename}.jpg`
      a.click()
      URL.revokeObjectURL(url)
    }
  } catch (e) {
    return false
  } finally {
    document.body.removeChild(container)
  }
  return true
}

export async function shareProgress(data) {
  const { title, category, emoji, color, points, maxPoints, notes, childName, isComplete } = data
  const refCode = data.referralCode || generateRefCode()
  const html = buildCardHTML({
    type: 'challenge',
    title,
    emoji,
    childName,
    category,
    points,
    maxPoints,
    notes,
    referralCode: refCode,
    isComplete
  })
  const ok = await captureAndShare(html, `challenge-${title.replace(/\s+/g, '-').toLowerCase()}`)
  if (!ok) {
    const pct = maxPoints > 0 ? Math.min(100, Math.round((points / maxPoints) * 100)) : 0
    const text = isComplete
      ? `🏆 ${childName} menyelesaikan "${title}" dengan ${maxPoints} poin!\n\nKategori: ${category}\nReferral: ${refCode}`
      : `💪 Progress "${title}": ${pct}% (${points}/${maxPoints} poin)\n\nAnak: ${childName}\nKategori: ${category}\nReferral: ${refCode}`
    if (navigator.share) {
      navigator.share({ title: `Progress: ${title}`, text }).catch(() => {})
    } else {
      navigator.clipboard?.writeText(text)
    }
  }
}

export async function shareChallenge(data) {
  const { title, category, emoji, maxPoints, childName } = data
  const refCode = data.referralCode || generateRefCode()
  const html = buildCardHTML({
    type: 'challenge',
    title,
    emoji,
    childName,
    category,
    points: maxPoints,
    maxPoints,
    referralCode: refCode,
    isComplete: true
  })
  const ok = await captureAndShare(html, `challenge-selesai-${title.replace(/\s+/g, '-').toLowerCase()}`)
  if (!ok) {
    const text = `🏆 ${childName} menyelesaikan challenge "${title}" dengan ${maxPoints} poin!\n\nKategori: ${category}\nReferral: ${refCode}`
    if (navigator.share) {
      navigator.share({ title: `Challenge: ${title}`, text }).catch(() => {})
    } else {
      navigator.clipboard?.writeText(text)
    }
  }
}

export async function shareChecklistImage(title, items, checkedCount, percent, options = {}) {
  const refCode = options.referralCode || generateRefCode()
  const childName = options.childName || 'Anak'
  const html = buildCardHTML({
    type: 'checklist',
    title,
    childName,
    items,
    percent,
    referralCode: refCode
  })
  const ok = await captureAndShare(html, `checklist-${title.replace(/\s+/g, '-').toLowerCase()}`)
  if (!ok) {
    const text = `✅ Checklist "${title}": ${checkedCount}/${items.length} selesai\n\nReferral: ${refCode}`
    if (navigator.share) {
      navigator.share({ title: `Checklist: ${title}`, text }).catch(() => {})
    } else {
      navigator.clipboard?.writeText(text)
    }
  }
}

export async function shareJadwalImage(items, options = {}) {
  const refCode = options.referralCode || generateRefCode()
  const childName = options.childName || 'Anak'
  const doneCount = items.filter(i => i.done).length
  const html = buildCardHTML({
    type: 'jadwal',
    title: 'Jadwal Harian',
    childName,
    items,
    referralCode: refCode
  })
  const ok = await captureAndShare(html, 'jadwal-harian')
  if (!ok) {
    const text = `📅 Jadwal Harian ${childName}\n\n` + items.map(i => `${i.done ? '✅' : '⬜'} ${i.label} ${i.time || ''}`).join('\n') + `\n\n${doneCount}/${items.length} selesai\nReferral: ${refCode}`
    if (navigator.share) {
      navigator.share({ title: 'Jadwal Harian', text }).catch(() => {})
    } else {
      navigator.clipboard?.writeText(text)
    }
  }
}

export function generateReferralCode() {
  return generateRefCode()
}
