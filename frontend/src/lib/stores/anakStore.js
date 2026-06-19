import { writable, derived, get } from 'svelte/store'
import { getAnakList as dbGetAnakList, saveAnak as dbSaveAnak, saveAnakBatch as dbSaveAnakBatch, removeAnak as dbRemoveAnak, clearAllUserData } from '../db.js'
import * as api from '../services/api.js'
import { isOffline } from '../utils/network.js'

const cachedAnakList = (() => {
  if (typeof localStorage === 'undefined') return []
  try { const raw = localStorage.getItem('lk_anak_cache'); return raw ? JSON.parse(raw) : [] } catch { return [] }
})()

export const anakList = writable(cachedAnakList)

export const allHistory = derived(anakList, ($anakList) => {
  return $anakList
    .flatMap(a => (a.history || []).map(h => ({ ...h, anakNama: a.nama, anakEmoji: a.emoji })))
    .sort((a, b) => {
      const parse = s => { const [d, m, y] = s.split(' '); const months = { Jan: 0, Feb: 1, Mar: 2, Apr: 3, Mei: 4, Jun: 5, Jul: 6, Agu: 7, Sep: 8, Okt: 9, Nov: 10, Des: 11 }; return new Date(y, months[m], d) }
      return parse(b.date) - parse(a.date)
    })
})

export async function loadAnakList() {
  if (isOffline()) {
    const localList = await dbGetAnakList()
    if (localList.length > 0) {
      const result = localList.map(a => ({ ...a, serverSynced: true }))
      anakList.set(result)
      localStorage.setItem('lk_anak_cache', JSON.stringify(result))
    } else {
      anakList.set([])
    }
    return
  }
  try {
    const serverList = await api.getAnakList()
    const seen = new Map()
    for (const a of serverList) seen.set(a.id, a)
    const mapped = [...seen.values()].map(a => ({
      ...a,
      tanggal: a.tanggal_lahir != null ? String(a.tanggal_lahir) : '',
      bulan: a.bulan_lahir != null ? String(a.bulan_lahir) : '',
      tahun: a.tahun_lahir != null ? String(a.tahun_lahir) : '',
      serverSynced: true,
      skills: (a.skills || []).map(s => ({ ...s, activities: s.activities || [] })),
      completedSkills: a.completed_skills || a.completedSkills || [],
    }))
    await dbSaveAnakBatch(mapped)
    anakList.set(mapped)
    localStorage.setItem('lk_anak_cache', JSON.stringify(mapped))
  } catch (e) {
    const localList = await dbGetAnakList()
    if (localList.length > 0) {
      const result = localList.map(a => ({ ...a, serverSynced: true }))
      anakList.set(result)
      localStorage.setItem('lk_anak_cache', JSON.stringify(result))
    } else {
      anakList.set([])
      localStorage.setItem('lk_anak_cache', JSON.stringify([]))
    }
  }
}

export async function validateAndClearIfDifferentUser(userId) {
  const storedUserId = localStorage.getItem('lk_cache_user_id')
  if (storedUserId && String(storedUserId) !== String(userId)) { await clearAllUserData(); anakList.set([]) }
  localStorage.setItem('lk_cache_user_id', String(userId))
}

export async function addAnak(anak) {
  const payload = { nama: anak.nama, gender: anak.gender, agama: anak.agama, umur: anak.umur, tanggal_lahir: anak.tanggal, bulan_lahir: anak.bulan, tahun_lahir: anak.tahun, emoji: anak.emoji, settings: anak.settings }
  const res = await api.addAnak(payload)
  const serverAnak = res.anak || res
  const localRecord = { ...anak, id: serverAnak.id, serverSynced: true }
  await dbSaveAnak(localRecord)
  anakList.update(list => [...list, localRecord])
  return serverAnak.id
}

export async function updateAnak(anak) {
  const updatedAnak = JSON.parse(JSON.stringify(anak))
  const payload = { nama: updatedAnak.nama, gender: updatedAnak.gender, agama: updatedAnak.agama, umur: updatedAnak.umur, tanggal_lahir: updatedAnak.tanggal, bulan_lahir: updatedAnak.bulan, tahun_lahir: updatedAnak.tahun, emoji: updatedAnak.emoji, settings: updatedAnak.settings }
  await api.updateAnak(updatedAnak.id, payload)
  updatedAnak.serverSynced = true
  await dbSaveAnak(updatedAnak)
  anakList.update(list => {
    const newList = list.map(a => a.id === anak.id ? { ...a, ...updatedAnak } : a)
    localStorage.setItem('lk_anak_cache', JSON.stringify(newList))
    return newList
  })
}

export async function deleteAnak(id) {
  await api.deleteAnak(id)
  await dbRemoveAnak(id)
  anakList.update(list => list.filter(a => a.id !== id))
}

export async function resetSkill({ anak, skill }) {
  const idx = anak.completedSkills.findIndex(s => s.key === skill.key)
  if (idx === -1) return
  anak.completedSkills.splice(idx, 1)
  anak.skills.push({ ...skill, progress: 0, activities: skill.activities || [] })
  await api.deleteCompletedSkill(anak.id, skill.key)
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
  anakList.update(list => list.map(a => a.id === anak.id ? { ...anak } : a))
}

export async function deleteSkill({ anak, skill }) {
  const idx = (anak.skills || []).findIndex(s => s.key === skill.key)
  if (idx === -1) return
  anak.skills.splice(idx, 1)
  await api.deleteSkill(anak.id, skill.key)
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
  anakList.update(list => list.map(a => a.id === anak.id ? { ...anak } : a))
}

export async function addSkill(anakId, skillData) {
  const list = get(anakList)
  const anak = list.find(a => a.id === anakId)
  if (!anak) return
  if (!anak.skills) anak.skills = []
  if (anak.skills.some(s => s.key === skillData.key)) return
  anak.skills.push({ key: skillData.key, emoji: skillData.emoji, title: skillData.title, pilar: skillData.pilar, progress: 0, color: skillData.color, activities: [] })
  await api.addSkill(anakId, skillData)
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
  anakList.update(list => list.map(a => a.id === anakId ? { ...anak } : a))
}

export async function addActivity(anakId, skillKey, activityData) {
  const list = get(anakList)
  const anak = list.find(a => a.id === anakId)
  if (!anak) return
  const skill = (anak.skills || []).find(s => s.key === skillKey)
  if (!skill) return
  if (!skill.activities) skill.activities = []
  if (skill.activities.some(a => a.title === activityData.title)) return
  skill.activities.push({ title: activityData.title, emoji: activityData.emoji, feature: activityData.feature, date: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) })
  await api.addActivity(anakId, { skill_key: skillKey, title: activityData.title, emoji: activityData.emoji, feature: activityData.feature })
  await dbSaveAnak(JSON.parse(JSON.stringify(anak)))
  anakList.update(list => list.map(a => a.id === anakId ? { ...anak } : a))
}
