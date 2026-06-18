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

db.version(4).stores({
  anak: '++id, nama',
  challenges: '++id, anakId, category',
  challengeHistory: '++id, anakId, category',
  checklists: '++id, anakId',
  schedules: '++id, anakId',
  scheduleHistories: '++id, anakId, scheduleId, date',
  worksheets: '++id, anakId',
  settings: 'key',
  syncQueue: '++id, action, timestamp'
})

db.version(6).stores({
  anak: '++id, nama',
  challenges: '++id, anakId, category',
  challengeHistory: '++id, anakId, category',
  checklists: '++id, anakId',
  schedules: '++id, anakId',
  scheduleHistories: '++id, anakId, scheduleId, date',
  worksheets: '++id, anakId',
  settings: 'key',
  syncQueue: '++id, action, timestamp, status',
  activities: '++id, type, status, updatedAt'
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

export async function addToSyncQueue(entry) {
  return db.syncQueue.add({ ...entry, timestamp: Date.now(), attempts: 0, status: 'pending' })
}

export async function getSyncQueue() {
  return db.syncQueue.where('status').equals('pending').toArray()
}

export async function removeSyncQueueItem(id) {
  return db.syncQueue.delete(id)
}

export async function markSyncQueueDone(id) {
  return db.syncQueue.update(id, { status: 'synced' })
}

export async function clearSyncedQueue() {
  return db.syncQueue.where('status').equals('synced').delete()
}

export async function clearSyncQueue() {
  return db.syncQueue.clear()
}

export async function getSyncQueueCount() {
  return db.syncQueue.count()
}

function cleanRecord(obj, foreignKey, foreignValue) {
  const { anak_id, id, ...rest } = obj
  const record = { ...rest, [foreignKey]: foreignValue }
  if (id != null) record.id = id
  return record
}

export async function syncServerData(anakList) {
  const pendingQueue = await db.syncQueue.toArray()
  const pendingActions = new Set(pendingQueue.map(q => q.action))
  const hasEvalPending = pendingActions.has('addEvaluation')

  await db.transaction('rw', db.anak, db.challenges, db.challengeHistory, db.checklists, db.schedules, db.scheduleHistories, db.worksheets, db.settings, async () => {
    for (const anak of anakList) {
      if (!anak.id) continue
      const anakId = anak.id

      const historyData = anak.challenge_histories || anak.challengeHistory
      if (Array.isArray(historyData)) {
        await db.challengeHistory.where('anakId').equals(anakId).delete()
        for (const h of historyData) {
          await db.challengeHistory.put(cleanRecord(h, 'anakId', anakId))
        }
      }

      if (Array.isArray(anak.checklists) && !pendingActions.has('addChecklist') && !pendingActions.has('updateChecklist') && !pendingActions.has('deleteChecklist')) {
        await db.checklists.where('anakId').equals(anakId).delete()
        for (const cl of anak.checklists) {
          const record = cleanRecord(cl, 'anakId', anakId)
          if (record.id && !record.serverId) record.serverId = record.id
          await db.checklists.put(record)
        }
      }

      if (Array.isArray(anak.schedules) && !pendingActions.has('addSchedule') && !pendingActions.has('updateSchedule') && !pendingActions.has('deleteSchedule') && !pendingActions.has('toggleScheduleDone')) {
        await db.schedules.where('anakId').equals(anakId).delete()
        for (const s of anak.schedules) {
          const record = cleanRecord(s, 'anakId', anakId)
          if (record.id && !record.serverId) record.serverId = record.id
          await db.schedules.put(record)
        }
      }

      if (Array.isArray(anak.schedule_histories)) {
        await db.scheduleHistories.where('anakId').equals(anakId).delete()
        for (const sh of anak.schedule_histories) {
          const record = cleanRecord(sh, 'anakId', anakId)
          if (record.id && !record.serverId) record.serverId = record.id
          await db.scheduleHistories.put(record)
        }
      }

      if (Array.isArray(anak.worksheets)) {
        await db.worksheets.where('anakId').equals(anakId).delete()
        for (const w of anak.worksheets) {
          await db.worksheets.put(cleanRecord(w, 'anakId', anakId))
        }
      }

      if (Array.isArray(anak.evaluations) && !hasEvalPending) {
        const evals = anak.evaluations
        const active = evals.filter(e => e.evaluation_points < e.evaluation_max_points)
        const completed = evals.filter(e => e.evaluation_points >= e.evaluation_max_points)
        await saveSetting(`eval_cache_${anakId}`, {
          evaluations: evals,
          active,
          completed_count: completed.length,
          total_points: completed.reduce((s, e) => s + (e.evaluation_points || 0), 0),
          total_max: completed.reduce((s, e) => s + (e.evaluation_max_points || 0), 0),
        })
      }
    }
  })
}

// Activities storage - stores activity content by type for offline access
export async function saveActivities(groupedData) {
  await db.transaction('rw', db.activities, async () => {
    await db.activities.clear()
    for (const [type, items] of Object.entries(groupedData)) {
      if (Array.isArray(items)) {
        for (const item of items) {
          await db.activities.put({
            ...item,
            type,
            updatedAt: Date.now()
          })
        }
      }
    }
  })
}

export async function getActivitiesByType(type) {
  return db.activities.where('type').equals(type).toArray()
}

export async function getAllActivities() {
  const items = await db.activities.toArray()
  // Group by type
  const grouped = {}
  for (const item of items) {
    const t = item.type
    if (!grouped[t]) grouped[t] = []
    grouped[t].push(item)
  }
  return grouped
}

export async function downloadAllData(data) {
  const promises = []

  if (Array.isArray(data.anak_list)) {
    promises.push(syncServerData(data.anak_list))
  }

  if (data.pilars || data.skills) {
    promises.push(saveSetting('pilars_skills_cache', {
      pilars: data.pilars || [],
      skills: data.skills || [],
      savedAt: Date.now()
    }))
  }

  if (data.plans) {
    promises.push(saveSetting('plans_cache', data.plans))
  }

  if (data.worksheets) {
    promises.push(saveSetting('worksheets_cache', data.worksheets))
  }

  if (data.affiliate_config) {
    promises.push(saveSetting('affiliate_config_cache', data.affiliate_config))
  }

  // Save activities grouped data to Dexie for offline access (Dexie has larger capacity than localStorage)
  if (data.activities_grouped) {
    promises.push(saveActivities(data.activities_grouped))
  }

  promises.push(saveSetting('last_download_at', Date.now()))

  await Promise.all(promises)
}
