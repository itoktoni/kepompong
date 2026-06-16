import { writable, get } from 'svelte/store'
import { getWorksheetTypes } from './worksheetTypes.js'

const contentKeyMap = {
  storytelling: 'stories',
  bermain_peran: 'roles',
  permainan: 'games',
  monolog: 'scripts',
  proyek_kreatif: 'projects',
  musik_gerak: 'songs',
  puzzle: 'puzzles',
  mindfulness: 'exercises',
  outdoor: 'activities',
  ilmu_pengetahuan: 'experiments',
}

const defaultMeta = {
  storytelling: { emoji: '📖', title: 'Story Telling', desc: 'Anak belajar mendengar, bercerita dan menyampaikan ide secara verbal.', color: '#4CAF50', bg: '#E8F5E9', feature: 'story' },
  bermain_peran: { emoji: '🎭', title: 'Bermain Peran', desc: 'Anak belajar memahami perspektif orang lain melalui peran.', color: '#FF9800', bg: '#FFF3E0', feature: 'roleplay' },
  permainan: { emoji: '🎲', title: 'Permainan', desc: 'Anak belajar aturan, kerja sama, dan sportivitas.', color: '#E91E63', bg: '#FCE4EC', feature: 'game' },
  monolog: { emoji: '🎤', title: 'Monolog', desc: 'Anak belajar berani tampil dan berbicara di depan umum.', color: '#9C27B0', bg: '#F3E5F5', feature: 'monolog' },
  proyek_kreatif: { emoji: '🎨', title: 'Proyek Kreatif & Seni', desc: 'Anak belajar mengekspresikan diri melalui seni.', color: '#2196F3', bg: '#E3F2FD', feature: 'project' },
  musik_gerak: { emoji: '🎵', title: 'Musik & Gerak', desc: 'Anak belajar ritme, koordinasi, dan ekspresi tubuh.', color: '#FF5722', bg: '#FBE9E7', feature: 'music' },
  puzzle: { emoji: '🧩', title: 'Puzzle & Problem Solving', desc: 'Anak belajar berpikir logis dan memecahkan masalah.', color: '#673AB7', bg: '#EDE7F6', feature: 'puzzle' },
  mindfulness: { emoji: '🧘', title: 'Mindfulness & Refleksi', desc: 'Anak belajar mengenali perasaan dan menenangkan diri.', color: '#795548', bg: '#EFEBE9', feature: 'mindfulness' },
  outdoor: { emoji: '🌿', title: 'Outdoor Exploration', desc: 'Anak belajar mengenal alam dan lingkungan sekitar.', color: '#009688', bg: '#E0F2F1', feature: 'outdoor' },
  ilmu_pengetahuan: { emoji: '🔬', title: 'Ilmu Pengetahuan & Literasi', desc: 'Anak belajar sains, eksperimen, dan meningkatkan kemampuan literasi.', color: '#0D47A1', bg: '#E3F2FD', feature: 'ilmu_pengetahuan' },
  worksheet: { emoji: '📝', title: 'Worksheet Anak', desc: 'Worksheet latihan menulis kutipan inspiratif untuk anak.', color: '#176c33', bg: '#E1F2E5', feature: 'worksheet' },
}

function normalizeItem(item, type) {
  const content = item.data || {}
  const contentKey = contentKeyMap[type]
  const ages = item.ages || []

  const normalized = {
    id: item.id,
    slug: item.slug || '',
    title: item.title,
    image: item.image,
    desc: item.desc,
    moral: item.moral,
    ages,
    skills: item.skills || [],
    agama: item.agama || [],
    plans: item.plans || [],
    views: item.views || 0,
    status: item.status || 'approved',
    prompt: item.prompt || '',
    creator: item.creator || '',
  }

  if (contentKey && contentKey === 'stories') {
    normalized.pages = content.pages || []
  } else if (contentKey === 'roles') {
    normalized.roles = content.roles || []
    normalized.pages = content.pages || []
  } else if (contentKey === 'games') {
    normalized.how = content.how || ''
    normalized.rules = content.rules || []
  } else if (contentKey === 'scripts') {
    normalized.script = content.script || ''
    normalized.tips = content.tips || []
  } else if (contentKey === 'projects') {
    normalized.duration = content.duration || ''
    normalized.difficulty = content.difficulty || ''
    normalized.materials = content.materials || []
    normalized.steps = content.steps || []
  } else if (contentKey === 'songs') {
    normalized.lyrics = content.lyrics || ''
    normalized.moves = content.moves || []
  } else if (contentKey === 'puzzles') {
    normalized.questions = content.questions || []
  } else if (contentKey === 'exercises') {
    normalized.steps = content.steps || []
    normalized.benefit = content.benefit || ''
  } else if (contentKey === 'activities') {
    normalized.steps = content.steps || []
    normalized.observation = content.observation || ''
  } else if (contentKey === 'experiments') {
    normalized.materials = content.materials || []
    normalized.steps = content.steps || []
    normalized.explanation = content.explanation || ''
  }

  return normalized
}

export function buildAktivitasDataFromAPI(groupedData) {
  return Object.entries(defaultMeta).map(([key, m]) => {
    if (key === 'worksheet') {
      return { key, ...m, ages: [], worksheets: getWorksheetTypes() }
    }

    const items = (groupedData[key] || []).map(item => normalizeItem(item, key))
    const ages = [...new Set(items.flatMap(i => i.ages || []))].sort((a, b) => a - b)
    const contentKey = contentKeyMap[key]

    return { key, ...m, ages, [contentKey]: items }
  })
}

export const aktivitasData = writable(Object.entries(defaultMeta).map(([key, m]) => {
  if (key === 'worksheet') return { key, ...m, ages: [], worksheets: getWorksheetTypes() }
  const contentKey = contentKeyMap[key]
  return { key, ...m, ages: [], [contentKey]: [] }
}))

export function setAktivitasData(data) {
  aktivitasData.set(data)
}

export function filterActivities({ childAge, childAgama, planId, skillKey, pilarKey } = {}) {
  return get(aktivitasData).map(a => {
    const contentKey = contentKeyMap[a.key]
    const items = (a[contentKey] || []).filter(item => {
      const ageOk = childAge == null || (item.ages && item.ages.includes(childAge))
      const agamaOk = !childAgama || !item.agama || !item.agama.length || item.agama.includes(childAgama)
      const planOk = !planId || !item.plans || !item.plans.length || item.plans.includes(planId)
      const skillOk = !skillKey || !item.skills || item.skills.includes(skillKey)
      const pilarOk = !pilarKey || !item.skills || !item.skills.length || true
      return ageOk && agamaOk && planOk && skillOk && pilarOk
    })
    return { ...a, [contentKey]: items }
  }).filter(a => {
    if (a.key === 'worksheet') return true
    const contentKey = contentKeyMap[a.key]
    return (a[contentKey] || []).length > 0
  })
}
