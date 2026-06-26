import { get } from 'svelte/store'
import { pilars as pilarsStore } from '../stores/authStore.js'

const fallbackPilars = [
  { key: 'spiritual', emoji: '🙏', title: 'Spiritual & Nilai Kehidupan', subtitle: 'Kompas moral & makna hidup', color: '#4CAF50', bg: '#E8F5E9', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'karakter', emoji: '🦁', title: 'Karakter & Mental', subtitle: 'Tangguh & bertanggung jawab', color: '#FF9800', bg: '#FFF3E0', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'kreatifitas', emoji: '📚', title: 'Kreatifitas & Inovasi', subtitle: 'Suka belajar & rasa ingin tahu', color: '#2196F3', bg: '#E3F2FD', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'disiplin', emoji: '🧠', title: 'Disiplin & Kebiasaan Baik', subtitle: 'Fokus & atur diri sendiri', color: '#9C27B0', bg: '#F3E5F5', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'kemandirian', emoji: '🧹', title: 'Kemandirian & Life Skills', subtitle: 'Mandiri mengurus diri', color: '#3F51B5', bg: '#E8EAF6', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'sosial', emoji: '🤝', title: 'Sosial & Komunikasi', subtitle: 'Bergaul & bekerja sama', color: '#8D6E63', bg: '#EFEBE9', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'emosi', emoji: '❤️', title: 'Pengelolaan Emosi & Keluarga', subtitle: 'Dicintai, aman & dekat keluarga', color: '#F44336', bg: '#FFEBEE', ages: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] },
  { key: 'kesehatan', emoji: '💪', title: 'Kesehatan & Olahraga', subtitle: 'Tubuh sehat & aktif bergerak', color: '#009688', bg: '#E0F2F1', ages: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] }
]

export function getPilars() {
  const storeData = get(pilarsStore)
  return storeData.length ? storeData : fallbackPilars
}

export const pilars = fallbackPilars

export function filterPilars(childAge, childAgama, planId) {
  const list = getPilars()
  return list.filter(p => {
    const ages = Array.isArray(p.ages) ? p.ages : []
    const agama = Array.isArray(p.agama) ? p.agama : []
    const plans = Array.isArray(p.plans) ? p.plans : []
    const ageOk = childAge == null || ages.some(a => Number(a) === Number(childAge))
    const agamaOk = !agama.length || !childAgama || agama.includes(childAgama)
    const planOk = !plans.length || !planId || plans.includes(planId)
    return ageOk && agamaOk && planOk
  })
}
