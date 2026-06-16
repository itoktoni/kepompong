export function calcAge(tahun, bulan, tanggal) {
  if (!tahun) return null
  const now = new Date()
  let age = now.getFullYear() - tahun
  if (bulan && (now.getMonth() + 1 < bulan || (now.getMonth() + 1 === bulan && now.getDate() < tanggal))) {
    age--
  }
  return Math.max(0, age)
}

export function ageLabel(tahun, bulan, tanggal) {
  const age = calcAge(tahun, bulan, tanggal)
  if (age === null) return '-'
  return `${age} tahun`
}

export function ageGroup(tahun) {
  const age = calcAge(tahun)
  if (age === null) return 'all'
  if (age <= 3) return '0-3'
  if (age <= 5) return '4-5'
  if (age <= 7) return '6-7'
  if (age <= 10) return '8-10'
  return '11+'
}
