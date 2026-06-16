import { writable, derived, get } from 'svelte/store'
import { getAnakList as dbGetAnakList, saveAnak as dbSaveAnak, saveAnakBatch as dbSaveAnakBatch, removeAnak as dbRemoveAnak, clearAllUserData, getSetting } from '../db.js'
import * as api from '../services/api.js'

async function shouldAutoSync() {
  if (!api.isAuthenticated()) return false
  const val = await getSetting('autoSync')
  return val !== false
}

const cachedAnakList = (() => {
  if (typeof localStorage === 'undefined') return []
  try {
    const raw = localStorage.getItem('lk_anak_cache')
    return raw ? JSON.parse(raw) : []
  } catch { return [] }
})()

export const anakList = writable(cachedAnakList)

export const allHistory = derived(anakList, ($anakList) => {
  return $anakList
    .flatMap(a => (a.history || []).map(h => ({ ...h, anakNama: a.nama, anakEmoji: a.emoji })))
    .sort((a, b) => {
      const parse = s => {
        const [d, m, y] = s.split(' ')
        const months = { Jan: 0, Feb: 1, Mar: 2, Apr: 3, Mei: 4, Jun: 5, Jul: 6, Agu: 7, Sep: 8, Okt: 9, Nov: 10, Des: 11 }
        return new Date(y, months[m], d)
      }
      return parse(b.date) - parse(a.date)
    })
})

export async function loadAnakList() {
  if (api.isAuthenticated()) {
    try {
      const serverList = await api.getAnakList()
      const seen = new Map()
      for (const a of serverList) seen.set(a.id, a)
      const deduped = [...seen.values()]
      const mapped = deduped.map(a => ({
        ...a,
        tanggal: a.tanggal_lahir != null ? String(a.tanggal_lahir) : '',
        bulan: a.bulan_lahir != null ? String(a.bulan_lahir) : '',
        tahun: a.tahun_lahir != null ? String(a.tahun_lahir) : '',
        serverSynced: true,
        skills: (a.skills || []).map(s => ({ ...s, activities: s.activities || [] })),
        completedSkills: a.completed_skills || a.completedSkills || [],
      }))
      anakList.set(mapped)
      localStorage.setItem('lk_anak_cache', JSON.stringify(mapped))
      await dbSaveAnakBatch(mapped)
      return
    } catch (e) { /* ignore */ }
  }
  const localList = await dbGetAnakList()
  const result = localList.map(a => ({ ...a, serverSynced: false }))
  anakList.set(result)
  localStorage.setItem('lk_anak_cache', JSON.stringify(result))
}

export async function validateAndClearIfDifferentUser(userId) {
  const storedUserId = localStorage.getItem('lk_cache_user_id')
  if (storedUserId && String(storedUserId) !== String(userId)) {
    await clearAllUserData()
    anakList.set([])
  }
  localStorage.setItem('lk_cache_user_id', String(userId))
}

export async function addAnak(anak) {
  if (await shouldAutoSync()) {
    const payload = {
      nama: anak.nama,
      gender: anak.gender,
      agama: anak.agama,
      umur: anak.umur,
      tanggal_lahir: anak.tanggal || anak.tanggal_lahir,
      bulan_lahir: anak.bulan || anak.bulan_lahir,
      tahun_lahir: anak.tahun || anak.tahun_lahir,
      emoji: anak.emoji,
      settings: anak.settings,
    }
    const saved = await api.addAnak(payload)
    await loadAnakList()
    return saved.id
  }
  const id = await dbSaveAnak(anak)
  anak.id = id
  anak.serverSynced = false
  anakList.update(list => [...list, anak])
  return id
}

export async function updateAnak(anak) {
  if (await shouldAutoSync()) {
    try {
      const payload = {
        nama: anak.nama,
        gender: anak.gender,
        agama: anak.agama,
        umur: anak.umur,
        tanggal_lahir: anak.tanggal || anak.tanggal_lahir,
        bulan_lahir: anak.bulan || anak.bulan_lahir,
        tahun_lahir: anak.tahun || anak.tahun_lahir,
        emoji: anak.emoji,
        settings: anak.settings,
      }
      await api.updateAnak(anak.id, payload)
      await loadAnakList()
    } catch (e) { /* ignore */ }
  }
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
}

export async function deleteAnak(id) {
  if (await shouldAutoSync()) {
    try { await api.deleteAnak(id) } catch (e) { /* ignore */ }
  }
  await dbRemoveAnak(id)
  anakList.update(list => list.filter(a => a.id !== id))
}

export async function resetSkill({ anak, skill }) {
  const idx = anak.completedSkills.findIndex(s => s.key === skill.key)
  if (idx > -1) {
    anak.completedSkills.splice(idx, 1)
    anak.skills.push({ ...skill, progress: 0, activities: skill.activities || [] })
    if (await shouldAutoSync()) {
      try {
        await api.deleteCompletedSkill(anak.id, skill.key)
        await api.addSkill(anak.id, { key: skill.key, emoji: skill.emoji, title: skill.title, pilar: skill.pilar, color: skill.color })
      } catch (e) { /* ignore */ }
    }
    await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
    anakList.update(list => list)
  }
}

export async function deleteSkill({ anak, skill }) {
  const idx = (anak.skills || []).findIndex(s => s.key === skill.key)
  if (idx > -1) {
    anak.skills.splice(idx, 1)
    if (await shouldAutoSync()) {
      try { await api.deleteSkill(anak.id, skill.key) } catch (e) { /* ignore */ }
    }
    await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
    anakList.update(list => list)
  }
}

export async function addSkill(anakId, skillData) {
  const list = get(anakList)
  const anak = list.find(a => a.id === anakId)
  if (!anak) return
  if (!anak.skills) anak.skills = []
  const exists = anak.skills.some(s => s.key === skillData.key)
  if (exists) return
  anak.skills.push({
    key: skillData.key,
    emoji: skillData.emoji,
    title: skillData.title,
    pilar: skillData.pilar,
    progress: 0,
    color: skillData.color,
    activities: []
  })
  if (await shouldAutoSync()) {
    try { await api.addSkill(anakId, skillData) } catch (e) { /* ignore */ }
  }
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
  anakList.update(list => list)
}

export async function addActivity(anakId, skillKey, activityData) {
  const list = get(anakList)
  const anak = list.find(a => a.id === anakId)
  if (!anak) return
  const skill = (anak.skills || []).find(s => s.key === skillKey)
  if (!skill) return
  if (!skill.activities) skill.activities = []
  const exists = skill.activities.some(a => a.title === activityData.title)
  if (exists) return
  skill.activities.push({
    title: activityData.title,
    emoji: activityData.emoji,
    feature: activityData.feature,
    date: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
  })
  if (await shouldAutoSync()) {
    try {
      await api.addActivity(anakId, { skill_key: skillKey, title: activityData.title, emoji: activityData.emoji, feature: activityData.feature })
    } catch (e) { /* ignore */ }
  }
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
  anakList.update(list => list)
}
