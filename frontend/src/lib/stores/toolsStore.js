import { writable, derived, get } from 'svelte/store'
import {
  getChallenges, saveChallenge as dbSaveChallenge, removeChallenge as dbRemoveChallenge,
  getChallengeHistory, saveChallengeHistory as dbSaveChallengeHistory,
  getChecklists, saveChecklist as dbSaveChecklist, removeChecklist as dbRemoveChecklist,
  getSchedules as dbGetSchedules, saveSchedule as dbSaveSchedule, removeSchedule as dbRemoveSchedule,
  getScheduleHistories as dbGetScheduleHistories, saveScheduleHistory as dbSaveScheduleHistory,
  removeScheduleHistory as dbRemoveScheduleHistory, removeScheduleHistories as dbRemoveScheduleHistories,
  getWorksheets, saveWorksheet as dbSaveWorksheet, removeWorksheet as dbRemoveWorksheet,
  getSetting, getAnakList as dbGetAnakList
} from '../db.js'
import { autoSync } from './syncStore.js'
import * as api from '../services/api.js'

async function shouldAutoSync() {
  if (!api.isAuthenticated()) return false
  const val = await getSetting('autoSync')
  return val !== false
}

function isAutoSyncEnabled() {
  let enabled = true
  const unsubscribe = autoSync.subscribe(v => enabled = v)
  unsubscribe()
  return enabled
}

async function ensureAnakOnServer(anakId) {
  if (!anakId) return null
  try {
    const serverList = await api.getAnakList()
    const found = serverList.find(a => a.id === anakId)
    if (found) return found.id
    const localList = await dbGetAnakList()
    const local = localList.find(a => a.id === anakId)
    if (local) {
      const saved = await api.addAnak({
        nama: local.nama,
        gender: local.gender,
        umur: local.umur,
        tanggal_lahir: local.tanggal_lahir || local.tanggal,
        bulan_lahir: local.bulan_lahir || local.bulan,
        tahun_lahir: local.tahun_lahir || local.tahun,
        emoji: local.emoji,
        skills: local.skills || [],
        history: local.history || [],
        completed_skills: local.completed_skills || [],
        settings: local.settings || [],
      })
      return saved.id
    }
  } catch (e) { /* ignore */ }
  return null
}

const emptyToolsData = { challenges: [], challengeHistory: [], checklists: [], schedules: [], worksheets: [] }

export const anakToolsData = writable({})
export const toolsAnakId = writable(null)

function getAnakToolsData(toolsMap, anakId) {
  if (!toolsMap[anakId]) {
    toolsMap[anakId] = JSON.parse(JSON.stringify(emptyToolsData))
  }
  return toolsMap[anakId]
}

export const toolsData = derived(
  [anakToolsData, toolsAnakId],
  ([$anakToolsData, $toolsAnakId]) => getAnakToolsData($anakToolsData, $toolsAnakId)
)

export async function loadToolsData(anakListArr) {
  const toolsMap = {}
  const syncEnabled = isAutoSyncEnabled()
  const today = new Date().toISOString().slice(0, 10)
  for (const anak of anakListArr) {
    const challenges = await getChallenges(anak.id)
    const challengeHistory = await getChallengeHistory(anak.id)
    const checklists = await getChecklists(anak.id)
    // Load schedules based on autoSync setting
    let schedules = []
    if (syncEnabled && api.isAuthenticated()) {
      try {
        schedules = await api.getSchedules(anak.id) || []
        for (const s of schedules) {
          if (s.anak_id !== undefined) {
            s.anakId = s.anak_id
            delete s.anak_id
          }
          // Map server id to serverId so toggle/update works
          if (s.id && !s.serverId) {
            s.serverId = s.id
          }
        }
      } catch (e) {
        // Fallback to local
        schedules = await dbGetSchedules(anak.id)
      }
    } else {
      schedules = await dbGetSchedules(anak.id)
    }

    // Load schedule histories for today to determine done status
    let histories = []
    if (syncEnabled && api.isAuthenticated()) {
      try {
        const res = await api.getScheduleHistories(anak.id, today)
        histories = res?.histories || []
        // Save to local DB
        for (const h of histories) {
          await dbSaveScheduleHistory({ ...h, anakId: anak.id, scheduleId: h.schedule_id || h.id })
        }
      } catch (e) {
        histories = await dbGetScheduleHistories(anak.id, today)
      }
    } else {
      histories = await dbGetScheduleHistories(anak.id, today)
    }

    // Mark schedules as done based on histories
    const historyScheduleIds = new Set(histories.map(h => h.schedule_id || h.scheduleId))
    for (const s of schedules) {
      s.done = historyScheduleIds.has(s.serverId || s.id)
      s.date = today
    }

    const worksheets = await getWorksheets(anak.id)
    toolsMap[anak.id] = { challenges, challengeHistory, checklists, schedules, worksheets }
  }
  anakToolsData.set(toolsMap)
  if (anakListArr.length) {
    const currentId = get(toolsAnakId)
    if (!currentId) toolsAnakId.set(anakListArr[0].id)
  }
}

export async function addChallenge(item) {
  const currentId = get(toolsAnakId)
  anakToolsData.update(map => {
    getAnakToolsData(map, currentId).challenges.push(item)
    return map
  })
  dbSaveChallenge({ ...item, anakId: currentId })
  if (await shouldAutoSync()) {
    try {
      const serverAnakId = await ensureAnakOnServer(currentId)
      if (!serverAnakId) return
      const saved = await api.addChallenge(serverAnakId, item)
      if (saved?.id) item.serverId = saved.id
    } catch (e) { /* ignore */ }
  }
}

export async function addPoint({ id, amount }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const c = getAnakToolsData(map, currentId).challenges.find(c => c.id === id)
  if (c) {
    c.points = Math.min(c.maxPoints, c.points + amount)
    anakToolsData.set(map)
    dbSaveChallenge({ ...c, anakId: currentId })
    if (await shouldAutoSync() && c.serverId) {
      try { await api.updateChallenge(currentId, c.serverId, { points: c.points }) } catch (e) { /* ignore */ }
    }
  }
}

export async function removePoint({ id }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const c = getAnakToolsData(map, currentId).challenges.find(c => c.id === id)
  if (c) {
    c.points = Math.max(0, c.points - 1)
    anakToolsData.set(map)
    dbSaveChallenge({ ...c, anakId: currentId })
    if (await shouldAutoSync() && c.serverId) {
      try { await api.updateChallenge(currentId, c.serverId, { points: c.points }) } catch (e) { /* ignore */ }
    }
  }
}

export async function editChallenge(data) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const c = getAnakToolsData(map, currentId).challenges.find(c => c.id === data.id)
  if (c) {
    Object.assign(c, data)
    anakToolsData.set(map)
    dbSaveChallenge({ ...c, anakId: currentId })
    if (await shouldAutoSync() && c.serverId) {
      try { await api.updateChallenge(currentId, c.serverId, data) } catch (e) { /* ignore */ }
    }
  }
}

export async function deleteChallenge({ id }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const challenges = getAnakToolsData(map, currentId).challenges
  const idx = challenges.findIndex(c => c.id === id)
  if (idx > -1) {
    const removed = challenges.splice(idx, 1)[0]
    anakToolsData.set(map)
    dbRemoveChallenge(id)
    if (await shouldAutoSync() && removed?.serverId) {
      try { await api.deleteChallenge(currentId, removed.serverId) } catch (e) { /* ignore */ }
    }
  }
}

export async function addChallengeHistory(item) {
  const currentId = get(toolsAnakId)
  anakToolsData.update(map => {
    getAnakToolsData(map, currentId).challengeHistory.push(item)
    return map
  })
  dbSaveChallengeHistory({ ...item, anakId: currentId })
}

export async function addChecklist(item) {
  const currentId = get(toolsAnakId)
  anakToolsData.update(map => {
    const list = getAnakToolsData(map, currentId).checklists
    const idx = list.findIndex(c => c.id === item.id)
    if (idx >= 0) {
      list[idx] = item
    } else {
      list.push(item)
    }
    return map
  })
  const plain = JSON.parse(JSON.stringify(item))
  dbSaveChecklist({ ...plain, anakId: currentId })
  if (await shouldAutoSync()) {
    try {
      const serverAnakId = await ensureAnakOnServer(currentId)
      if (!serverAnakId) return
      if (item.serverId) {
        await api.updateChecklist(serverAnakId, item.serverId, { title: item.title, items: item.items })
      } else {
        const saved = await api.addChecklist(serverAnakId, item)
        if (saved?.id) {
          item.serverId = saved.id
          dbSaveChecklist({ ...JSON.parse(JSON.stringify(item)), anakId: currentId })
        }
      }
    } catch (e) { /* ignore */ }
  }
}

export async function removeChecklist(index) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const removed = getAnakToolsData(map, currentId).checklists.splice(index, 1)[0]
  anakToolsData.set(map)
  if (removed?.id) dbRemoveChecklist(removed.id)
  if (await shouldAutoSync() && removed?.serverId) {
    try {
      const serverAnakId = await ensureAnakOnServer(currentId)
      if (serverAnakId) await api.deleteChecklist(serverAnakId, removed.serverId)
    } catch (e) { /* ignore */ }
  }
}

export async function addChecklistItem({ checklistId, item }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const cl = getAnakToolsData(map, currentId).checklists.find(c => c.id === checklistId)
  if (cl) {
    cl.items.push(item)
    anakToolsData.set(map)
    const plain = JSON.parse(JSON.stringify(cl))
    dbSaveChecklist({ ...plain, anakId: currentId })
    if (await shouldAutoSync() && cl.serverId) {
      try {
        const serverAnakId = await ensureAnakOnServer(currentId)
        if (serverAnakId) await api.updateChecklist(serverAnakId, cl.serverId, { items: cl.items })
      } catch (e) { /* ignore */ }
    }
  }
}

export async function removeChecklistItem({ checklistId, itemIndex }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const cl = getAnakToolsData(map, currentId).checklists.find(c => c.id === checklistId)
  if (cl) {
    cl.items.splice(itemIndex, 1)
    anakToolsData.set(map)
    const plain = JSON.parse(JSON.stringify(cl))
    dbSaveChecklist({ ...plain, anakId: currentId })
    if (await shouldAutoSync() && cl.serverId) {
      try {
        const serverAnakId = await ensureAnakOnServer(currentId)
        if (serverAnakId) await api.updateChecklist(serverAnakId, cl.serverId, { items: cl.items })
      } catch (e) { /* ignore */ }
    }
  }
}

export async function addSchedule(item) {
  const currentId = get(toolsAnakId)
  if (!item.id) item.id = Date.now()

  // Add to local state first
  anakToolsData.update(map => {
    getAnakToolsData(map, currentId).schedules.push(item)
    return map
  })

  // Save to Dexie for local storage
  dbSaveSchedule({ ...item, anakId: currentId })

  // Sync to server if autoSync enabled
  if (isAutoSyncEnabled() && api.isAuthenticated()) {
    try {
      const serverAnakId = await ensureAnakOnServer(currentId)
      if (serverAnakId) {
        const saved = await api.addSchedule(serverAnakId, item)
        if (saved?.id) {
          item.serverId = saved.id
        }
      }
    } catch (e) { /* ignore */ }
  }
}

export async function updateSchedule(item, data) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const schedules = getAnakToolsData(map, currentId).schedules
  const idx = schedules.findIndex(s => s === item || s.id === item.id)
  if (idx > -1) {
    Object.assign(schedules[idx], data)
    anakToolsData.set(map)

    // Update in Dexie
    dbSaveSchedule({ ...schedules[idx], anakId: currentId })

    // Sync to server if autoSync enabled
    if (isAutoSyncEnabled() && api.isAuthenticated()) {
      try {
        // If done status changed, call toggleDone API which creates/deletes ScheduleHistory
        if (data.done !== undefined && schedules[idx]?.serverId) {
          const today = new Date().toISOString().slice(0, 10)
          const now = new Date().toTimeString().slice(0, 5)
          const result = await api.toggleScheduleDone(currentId, schedules[idx].serverId, today, now)
          if (result && typeof result.done === 'boolean') {
            schedules[idx].done = result.done
            if (result.done) {
              dbSaveScheduleHistory({ anakId: currentId, scheduleId: schedules[idx].serverId, date: today, time: now })
            } else {
              dbRemoveScheduleHistory(schedules[idx].serverId, today)
            }
            anakToolsData.set(map)
          } else if (result && result.id) {
            // History created (backend returns full history object)
            schedules[idx].done = true
            dbSaveScheduleHistory({ anakId: currentId, scheduleId: schedules[idx].serverId, date: result.date || today, time: result.time || now })
            anakToolsData.set(map)
          }
        } else if (data.done !== undefined && !schedules[idx]?.serverId) {
          // Schedule not yet on server — need to create it first
          const serverAnakId = await ensureAnakOnServer(currentId)
          if (serverAnakId) {
            const saved = await api.addSchedule(serverAnakId, { label: schedules[idx].label, time: schedules[idx].time })
            if (saved?.id) {
              schedules[idx].serverId = saved.id
              const today = new Date().toISOString().slice(0, 10)
              const now = new Date().toTimeString().slice(0, 5)
              const result = await api.toggleScheduleDone(serverAnakId, saved.id, today, now)
              if (result && result.id) {
                schedules[idx].done = true
                dbSaveScheduleHistory({ anakId: currentId, scheduleId: saved.id, date: result.date || today, time: result.time || now })
                anakToolsData.set(map)
              }
            }
          }
        } else {
          const serverAnakId = await ensureAnakOnServer(currentId)
          if (serverAnakId && schedules[idx]?.serverId) {
            await api.updateSchedule(serverAnakId, schedules[idx].serverId, data)
          }
        }
      } catch (e) { /* ignore */ }
    } else if (data.done !== undefined) {
      // Offline: save history locally
      const today = new Date().toISOString().slice(0, 10)
      const now = new Date().toTimeString().slice(0, 5)
      if (data.done) {
        dbSaveScheduleHistory({ anakId: currentId, scheduleId: schedules[idx].id, date: today, time: now })
      } else {
        dbRemoveScheduleHistory(schedules[idx].id, today)
      }
    }
  }
}

export async function removeSchedule(item) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const schedules = getAnakToolsData(map, currentId).schedules
  const idx = schedules.findIndex(s => s === item || s.id === item.id)
  if (idx > -1) {
    const removed = schedules.splice(idx, 1)[0]
    anakToolsData.set(map)
    if (removed?.id) {
      dbRemoveSchedule(removed.id)
      dbRemoveScheduleHistories(removed.id)
    }
    if (await shouldAutoSync() && removed?.serverId) {
      try {
        const serverAnakId = await ensureAnakOnServer(currentId)
        if (serverAnakId) await api.deleteSchedule(serverAnakId, removed.serverId)
      } catch (e) { /* ignore */ }
    }
  }
}

export async function addWorksheet(item) {
  const currentId = get(toolsAnakId)
  const id = await dbSaveWorksheet({ ...item, anakId: currentId })
  item.id = id
  anakToolsData.update(map => {
    getAnakToolsData(map, currentId).worksheets.push(item)
    return map
  })
  if (await shouldAutoSync()) {
    try {
      const serverAnakId = await ensureAnakOnServer(currentId)
      if (!serverAnakId) return id
      const saved = await api.addWorksheet(serverAnakId, item)
      if (saved?.id) item.serverId = saved.id
    } catch (e) { /* ignore */ }
  }
  return id
}

export async function removeWorksheetItem(id) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const worksheets = getAnakToolsData(map, currentId).worksheets
  const idx = worksheets.findIndex(w => w.id === id)
  if (idx > -1) {
    const removed = worksheets.splice(idx, 1)[0]
    anakToolsData.set(map)
    dbRemoveWorksheet(id)
    if (await shouldAutoSync() && removed?.serverId) {
      try {
        const serverAnakId = await ensureAnakOnServer(currentId)
        if (serverAnakId) await api.deleteWorksheet(serverAnakId, removed.serverId)
      } catch (e) { /* ignore */ }
    }
  }
}
