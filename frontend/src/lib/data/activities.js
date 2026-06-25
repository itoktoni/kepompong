import { writable, get } from 'svelte/store'
import { getWorksheetTypes } from './worksheetTypes.js'
import { getAllActivities, getSetting } from '../db.js'
import { isOffline } from '../utils/network.js'

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
  tebak_tebakan: 'guesses',
  permainan_tangan: 'handgames',
  latihan_otak: 'braintrains',
  komik: 'comics',
  mengenal_kata: 'objects',
}

const defaultMeta = {
  storytelling: { emoji: '📖', title: 'Story Telling', desc: 'Mengambil hikmak dan pelajaran dari kisah teladan.', color: '#4CAF50', bg: '#E8F5E9', feature: 'story' },
  bermain_peran: { emoji: '🎭', title: 'Bermain Peran', desc: 'Anak belajar memahami perspektif orang lain melalui peran.', color: '#FF9800', bg: '#FFF3E0', feature: 'roleplay' },
  permainan: { emoji: '🎲', title: 'Permainan', desc: 'Anak belajar aturan, kerja sama, dan sportivitas.', color: '#E91E63', bg: '#FCE4EC', feature: 'game' },
  monolog: { emoji: '🎤', title: 'Monolog', desc: 'Anak belajar berani tampil dan berbicara di depan umum.', color: '#9C27B0', bg: '#F3E5F5', feature: 'monolog' },
  proyek_kreatif: { emoji: '🎨', title: 'Proyek Kreatif & Seni', desc: 'Anak belajar mengekspresikan diri melalui seni.', color: '#2196F3', bg: '#E3F2FD', feature: 'project' },
  musik_gerak: { emoji: '🎵', title: 'Musik & Gerak', desc: 'Mengingat hal baik dari hal yang menyenangkan.', color: '#FF5722', bg: '#FBE9E7', feature: 'music' },
  puzzle: { emoji: '🧩', title: 'Puzzle & Problem Solving', desc: 'Anak belajar berpikir logis dan memecahkan masalah.', color: '#673AB7', bg: '#EDE7F6', feature: 'puzzle' },
  mindfulness: { emoji: '🧘', title: 'Mindfulness & Refleksi', desc: 'Anak belajar mengenali perasaan dan menenangkan diri.', color: '#795548', bg: '#EFEBE9', feature: 'mindfulness' },
  outdoor: { emoji: '🌿', title: 'Outdoor Exploration', desc: 'Anak belajar mengenal alam dan lingkungan sekitar.', color: '#009688', bg: '#E0F2F1', feature: 'outdoor' },
  ilmu_pengetahuan: { emoji: '🔬', title: 'Ilmu Pengetahuan & Literasi', desc: 'Anak belajar sains, eksperimen, dan meningkatkan kemampuan literasi.', color: '#0D47A1', bg: '#E3F2FD', feature: 'ilmu_pengetahuan' },
  tebak_tebakan: { emoji: '🤔', title: 'Tebak-tebakan', desc: 'Mengajak anak berpikir dengan teka-teki yang seru.', color: '#FF6F00', bg: '#FFF8E1', feature: 'guess' },
  permainan_tangan: { emoji: '🤲', title: 'Permainan Tangan', desc: 'Anak belajar koordinasi, ritme, dan kerja sama melalui permainan tangan.', color: '#AD1457', bg: '#FCE4EC', feature: 'handgame' },
  latihan_otak: { emoji: '🧠', title: 'Latihan Otak', desc: 'Anak melatih konsentrasi, daya ingat, dan kemampuan berpikir logis.', color: '#283593', bg: '#E8EAF6', feature: 'braintrain' },
  komik: { emoji: '💬', title: 'Komik Anak', desc: 'Memahami hikmah dari cerita bergambar.', color: '#E65100', bg: '#FFF3E0', feature: 'comic' },
  worksheet: { emoji: '📝', title: 'Worksheet Anak', desc: 'Worksheet latihan menulis kutipan inspiratif untuk anak.', color: '#176c33', bg: '#E1F2E5', feature: 'worksheet' },
  mengenal_kata: { emoji: '🪣', title: 'Mengenal Kata', desc: 'Aktivitas tambahan mengisi waktu luang.', color: '#5D4037', bg: '#EFEBE9', feature: 'objects' },
}

function normalizeItem(item, type) {
  // Support both legacy (item.data) and new flat structure
  const content = item.data || {}
  const contentKey = contentKeyMap[type]
  const ages = (item.ages || []).map(Number)

  // Get content fields - prefer flat structure, fallback to item.data
  const getField = (field, fallback = null) => {
    if (item[field] !== undefined) return item[field]
    if (content[field] !== undefined) return content[field]
    return fallback
  }

  const normalized = {
    id: item.id,
    slug: item.slug || '',
    title: item.title,
    image: item.image,
    emoji: getField('emoji', null),
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
    normalized.pages = getField('pages', [])
  } else if (contentKey === 'roles') {
    normalized.roles = getField('roles', [])
    normalized.pages = getField('pages', [])
  } else if (contentKey === 'games') {
    normalized.how = getField('how', '')
    normalized.rules = getField('rules', [])
  } else if (contentKey === 'scripts') {
    normalized.script = getField('script', '')
    normalized.tips = getField('tips', [])
  } else if (contentKey === 'projects') {
    normalized.duration = getField('duration', '')
    normalized.difficulty = getField('difficulty', '')
    normalized.materials = getField('materials', [])
    normalized.steps = getField('steps', [])
  } else if (contentKey === 'songs') {
    normalized.lyrics = getField('lyrics', '')
    normalized.moves = getField('moves', [])
    normalized.audio_url = getField('audio_url', '')
  } else if (contentKey === 'puzzles') {
    normalized.questions = getField('questions', [])
  } else if (contentKey === 'exercises') {
    normalized.steps = getField('steps', [])
    normalized.benefit = getField('benefit', '')
  } else if (contentKey === 'activities') {
    normalized.steps = getField('steps', [])
    normalized.observation = getField('observation', '')
    normalized.pages = getField('pages', [])
  } else if (contentKey === 'experiments') {
    normalized.materials = getField('materials', [])
    normalized.steps = getField('steps', [])
    normalized.explanation = getField('explanation', '')
  } else if (contentKey === 'guesses') {
    normalized.questions = getField('questions', [])
  } else if (contentKey === 'handgames') {
    normalized.how = getField('how', '')
    normalized.rules = getField('rules', [])
    normalized.moves = getField('moves', [])
    normalized.lyrics = getField('lyrics', '')
  } else if (contentKey === 'braintrains') {
    normalized.exercises = getField('exercises', [])
  } else if (contentKey === 'comics') {
    normalized.pages = getField('pages', [])
  } else if (contentKey === 'objects') {
    normalized.slides = getField('slides', [])
    normalized.tags = getField('tags', [])
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
      const ageOk = childAge == null || (item.ages && item.ages.some(a => Number(a) === Number(childAge)))
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

/**
 * Load activities from local Dexie database (for offline mode)
 * @returns {Promise<Object>} Grouped activities data
 */
export async function loadActivitiesFromLocal() {
  try {
    const grouped = await getAllActivities()
    return grouped
  } catch (e) {
    console.warn('[Activities] Failed to load from local DB:', e)
    return {}
  }
}

/**
 * Check if activities are available offline
 * @returns {Promise<boolean>}
 */
export async function hasOfflineActivities() {
  try {
    const grouped = await getAllActivities()
    return Object.keys(grouped).length > 0
  } catch (e) {
    return false
  }
}

/**
 * Initialize activities data - loads from local Dexie DB first, then syncs with server if online
 * This ensures activities are available offline after initial download
 */
export async function initializeActivitiesFromCache() {
  try {
    // First, try to load from local Dexie database (for offline access)
    const localGrouped = await getAllActivities()
    if (localGrouped && Object.keys(localGrouped).length > 0) {
      const aktivitas = buildAktivitasDataFromAPI(localGrouped)
      setAktivitasData(aktivitas)
      console.log('[Activities] Loaded from local DB:', Object.keys(localGrouped).length, 'categories')
      return true
    }

    // Fallback: try localStorage cache
    const cached = await getSetting('activities_cache')
    if (cached && Object.keys(cached).length > 0) {
      const aktivitas = buildAktivitasDataFromAPI(cached)
      setAktivitasData(aktivitas)
      console.log('[Activities] Loaded from localStorage cache:', Object.keys(cached).length, 'categories')
      return true
    }

    return false
  } catch (e) {
    console.warn('[Activities] Failed to initialize from cache:', e)
    return false
  }
}
