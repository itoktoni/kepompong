import Dexie from 'dexie'

const db = new Dexie('JejakTumbuh')

db.version(2).stores({
  anak: '++id, nama',
  challenges: '++id, anakId, category',
  challengeHistory: '++id, anakId, category',
  checklists: '++id, anakId',
  schedules: '++id, anakId',
  worksheets: '++id, anakId',
  settings: 'key'
})

db.version(3).stores({
  anak: '++id, nama',
  challenges: '++id, anakId, category',
  challengeHistory: '++id, anakId, category',
  checklists: '++id, anakId',
  schedules: '++id, anakId',
  scheduleHistories: '++id, anakId, scheduleId, date',
  worksheets: '++id, anakId',
  settings: 'key'
})

export default db

export async function getAnakList() {
  return db.anak.toArray()
}

export async function saveAnak(anak) {
  const { challenges, challengeHistory, challenge_history, checklists, schedules, worksheets, ...anakData } = anak
  return db.anak.put(JSON.parse(JSON.stringify(anakData)))
}

export async function saveAnakBatch(anakList) {
  await db.transaction('rw', db.anak, async () => {
    await db.anak.clear()
    for (const anak of anakList) {
      const { challenges, challengeHistory, challenge_history, checklists, schedules, worksheets, ...anakData } = anak
      await db.anak.put(JSON.parse(JSON.stringify(anakData)))
    }
  })
}

export async function removeAnak(id) {
  await db.transaction('rw', db.anak, db.challenges, db.challengeHistory, db.checklists, db.schedules, db.scheduleHistories, async () => {
    await db.anak.delete(id)
    await db.challenges.where('anakId').equals(id).delete()
    await db.challengeHistory.where('anakId').equals(id).delete()
    await db.checklists.where('anakId').equals(id).delete()
    await db.schedules.where('anakId').equals(id).delete()
    await db.scheduleHistories.where('anakId').equals(id).delete()
  })
}

export async function getChallenges(anakId) {
  return db.challenges.where('anakId').equals(anakId).toArray()
}

export async function saveChallenge(item) { return db.challenges.put(item) }
export async function removeChallenge(id) { return db.challenges.delete(id) }

export async function getChallengeHistory(anakId) {
  return db.challengeHistory.where('anakId').equals(anakId).toArray()
}

export async function saveChallengeHistory(item) { return db.challengeHistory.add(item) }

export async function removeChallengeHistories(anakId) { return db.challengeHistory.where('anakId').equals(anakId).delete() }

export async function getChecklists(anakId) {
  const lists = await db.checklists.where('anakId').equals(anakId).toArray()
  for (const cl of lists) cl.items = cl.items || []
  return lists
}

export async function saveChecklist(item) { return db.checklists.put(item) }
export async function removeChecklist(id) { return db.checklists.delete(id) }

export async function getSchedules(anakId) {
  return db.schedules.where('anakId').equals(anakId).toArray()
}

export async function saveSchedule(item) { return db.schedules.put(item) }
export async function removeSchedule(id) { return db.schedules.delete(id) }

export async function getScheduleHistories(anakId, date) {
  let query = db.scheduleHistories.where('anakId').equals(anakId)
  if (date) {
    return query.filter(h => h.date === date).toArray()
  }
  return query.toArray()
}

export async function saveScheduleHistory(item) { return db.scheduleHistories.put(item) }
export async function removeScheduleHistory(scheduleId, date) {
  const existing = await db.scheduleHistories.where({ scheduleId, date }).first()
  if (existing) return db.scheduleHistories.delete(existing.id)
}
export async function removeScheduleHistories(scheduleId) {
  return db.scheduleHistories.where('scheduleId').equals(scheduleId).delete()
}

export async function getSetting(key) {
  const row = await db.settings.get(key)
  return row?.value
}

export async function saveSetting(key, value) {
  return db.settings.put({ key, value })
}

export async function getWorksheets(anakId) {
  return db.worksheets.where('anakId').equals(anakId).toArray()
}

export async function saveWorksheet(item) { return db.worksheets.put(item) }
export async function removeWorksheet(id) { return db.worksheets.delete(id) }

export async function clearAllUserData() {
  await db.transaction('rw', db.anak, db.challenges, db.challengeHistory, db.checklists, db.schedules, db.scheduleHistories, db.worksheets, async () => {
    await db.anak.clear()
    await db.challenges.clear()
    await db.challengeHistory.clear()
    await db.checklists.clear()
    await db.schedules.clear()
    await db.scheduleHistories.clear()
    await db.worksheets.clear()
  })
  localStorage.removeItem('lk_anak_cache')
}

function cleanRecord(obj, foreignKey, foreignValue) {
  const { anak_id, id, ...rest } = obj
  const record = { ...rest, [foreignKey]: foreignValue }
  if (id != null) record.id = id
  return record
}

export async function syncServerData(anakList) {
  await db.transaction('rw', db.anak, db.challenges, db.challengeHistory, db.checklists, db.schedules, db.scheduleHistories, db.worksheets, async () => {
    for (const anak of anakList) {
      if (!anak.id) continue
      const anakId = anak.id
      await db.anak.put({
        id: anakId,
        nama: anak.nama,
        gender: anak.gender,
        umur: anak.umur,
        tanggal: anak.tanggal_lahir || anak.tanggal,
        bulan: anak.bulan_lahir || anak.bulan,
        tahun: anak.tahun_lahir || anak.tahun,
        tanggal_lahir: anak.tanggal_lahir,
        bulan_lahir: anak.bulan_lahir,
        tahun_lahir: anak.tahun_lahir,
        emoji: anak.emoji,
        avatar: anak.avatar,
        skills: anak.skills || [],
        history: anak.history || [],
        completed_skills: anak.completed_skills || [],
        settings: anak.settings || [],
      })

      if (Array.isArray(anak.challenges)) {
        await db.challenges.where('anakId').equals(anakId).delete()
        for (const c of anak.challenges) {
          await db.challenges.put(cleanRecord(c, 'anakId', anakId))
        }
      }

      const historyData = anak.challengeHistory || anak.challenge_history
      if (Array.isArray(historyData)) {
        await db.challengeHistory.where('anakId').equals(anakId).delete()
        for (const h of historyData) {
          await db.challengeHistory.put(cleanRecord(h, 'anakId', anakId))
        }
      }

      if (Array.isArray(anak.checklists)) {
        await db.checklists.where('anakId').equals(anakId).delete()
        for (const cl of anak.checklists) {
          const record = cleanRecord(cl, 'anakId', anakId)
          if (record.id && !record.serverId) record.serverId = record.id
          await db.checklists.put(record)
        }
      }

      if (Array.isArray(anak.schedules)) {
        await db.schedules.where('anakId').equals(anakId).delete()
        for (const s of anak.schedules) {
          const record = cleanRecord(s, 'anakId', anakId)
          if (record.id && !record.serverId) record.serverId = record.id
          await db.schedules.put(record)
        }
      }

      if (Array.isArray(anak.worksheets)) {
        await db.worksheets.where('anakId').equals(anakId).delete()
        for (const w of anak.worksheets) {
          await db.worksheets.put(cleanRecord(w, 'anakId', anakId))
        }
      }
    }
  })
}
