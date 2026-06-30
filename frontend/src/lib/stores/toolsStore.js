import { writable, derived, get } from 'svelte/store'
import {
  getChallenges, saveChallenge as dbSaveChallenge, removeChallenge as dbRemoveChallenge,
  getChallengeHistory, saveChallengeHistory as dbSaveChallengeHistory, removeChallengeHistories as dbRemoveChallengeHistories,
  getChecklists, saveChecklist as dbSaveChecklist, removeChecklist as dbRemoveChecklist,
  getSchedules as dbGetSchedules, saveSchedule as dbSaveSchedule, removeSchedule as dbRemoveSchedule,
  getScheduleHistories as dbGetScheduleHistories, saveScheduleHistory as dbSaveScheduleHistory,
  removeScheduleHistory as dbRemoveScheduleHistory, removeScheduleHistories as dbRemoveScheduleHistories,
  getWorksheets, saveWorksheet as dbSaveWorksheet, removeWorksheet as dbRemoveWorksheet,
  getAnakList as dbGetAnakList
} from '../db.js'
import { kategoriChallenge } from '../data/challenge.js'
import { syncOrQueue, getServerAnakId } from '../services/syncService.js'
import { autoSync } from './syncStore.js'
import * as api from '../services/api.js'
import { isOffline } from '../utils/network.js'

const emptyToolsData = { challenges: [], challengeHistory: [], checklists: [], schedules: [], worksheets: [] }

export const anakToolsData = writable({})
export const toolsAnakId = writable(null)

function getAnakToolsData(toolsMap, anakId) {
  if (!toolsMap[anakId]) toolsMap[anakId] = JSON.parse(JSON.stringify(emptyToolsData))
  return toolsMap[anakId]
}

export const toolsData = derived(
  [anakToolsData, toolsAnakId],
  ([$anakToolsData, $toolsAnakId]) => getAnakToolsData($anakToolsData, $toolsAnakId)
)

if (typeof window !== 'undefined') {
  window.addEventListener('sync-id-update', (e) => {
    const { table, anakId, localId, serverId } = e.detail
    anakToolsData.update(map => {
      const data = map[anakId]
      if (!data) return map
      const list = data[table]
      if (!list) return map
      const item = list.find(i => i.id === localId)
      if (item) item.serverId = serverId
      return map
    })
  })
}

export async function loadToolsData(anakListArr) {
  const toolsMap = {}
  const today = new Date().toISOString().slice(0, 10)
  for (const anak of anakListArr) {
    const challenges = await getChallenges(anak.id)
    for (const c of challenges) {
      const cat = kategoriChallenge[c.category]
      if (cat) { if (!c.color) c.color = cat.color; if (!c.bg) c.bg = cat.bg; if (!c.emoji) c.emoji = cat.emoji }
    }
    const challengeHistory = await getChallengeHistory(anak.id)
    for (const h of challengeHistory) {
      const cat = kategoriChallenge[h.category]
      if (cat) { if (!h.color) h.color = cat.color; if (!h.emoji) h.emoji = cat.emoji }
    }
    const checklists = await getChecklists(anak.id)
    const schedules = await dbGetSchedules(anak.id)
    const histories = await dbGetScheduleHistories(anak.id, today)
    const historyScheduleIds = new Set(histories.map(h => h.schedule_id || h.scheduleId))
    for (const s of schedules) { s.done = historyScheduleIds.has(s.serverId || s.id); s.date = today }
    const worksheets = await getWorksheets(anak.id)
    toolsMap[anak.id] = { challenges, challengeHistory, checklists, schedules, worksheets }
  }
  anakToolsData.set(toolsMap)
  if (anakListArr.length) {
    const currentId = get(toolsAnakId)
    if (!currentId) toolsAnakId.set(anakListArr[0].id)
  }
}

export async function refreshSchedules(anakId) {
  if (!anakId) return
  const existing = get(anakToolsData)
  if (!existing[anakId]?.schedules?.length) {
    const today = new Date().toISOString().slice(0, 10)
    const schedules = await dbGetSchedules(anakId)
    const histories = await dbGetScheduleHistories(anakId, today)
    const historyScheduleIds = new Set(histories.map(h => h.schedule_id || h.scheduleId))
    for (const s of schedules) { s.done = historyScheduleIds.has(s.serverId || s.id); s.date = today }
    anakToolsData.update(map => { getAnakToolsData(map, anakId).schedules = schedules; return map })
  }
  if (get(autoSync) && !isOffline() && api.isAuthenticated()) {
    pullSchedulesFromServer(anakId).catch(() => {})
  }
}

export async function refreshChecklists(anakId) {
  if (!anakId) return
  const existing = get(anakToolsData)
  if (!existing[anakId]?.checklists?.length) {
    const checklists = await getChecklists(anakId)
    anakToolsData.update(map => { getAnakToolsData(map, anakId).checklists = checklists; return map })
  }
  if (get(autoSync) && !isOffline() && api.isAuthenticated()) {
    pullChecklistsFromServer(anakId).catch(() => {})
  }
}

export async function refreshChallenges(anakId) {
  if (!anakId) return
  const existing = get(anakToolsData)
  if (!existing[anakId]?.challenges?.length) {
    const challenges = await getChallenges(anakId)
    for (const c of challenges) {
      const cat = kategoriChallenge[c.category]
      if (cat) { if (!c.color) c.color = cat.color; if (!c.bg) c.bg = cat.bg; if (!c.emoji) c.emoji = cat.emoji }
    }
    const challengeHistory = await getChallengeHistory(anakId)
    for (const h of challengeHistory) {
      const cat = kategoriChallenge[h.category]
      if (cat) { if (!h.color) h.color = cat.color; if (!h.emoji) h.emoji = cat.emoji }
    }
    anakToolsData.update(map => { getAnakToolsData(map, anakId).challenges = challenges; getAnakToolsData(map, anakId).challengeHistory = challengeHistory; return map })
  }
  if (get(autoSync) && !isOffline() && api.isAuthenticated()) {
    pullChallengesFromServer(anakId).catch(() => {})
  }
}

async function pullChallengesFromServer(anakId) {
  try {
    const serverId = await getServerAnakId(anakId)
    if (!serverId) return
    const serverChallenges = await api.getChallenges(serverId)
    const serverHistory = await api.getChallengeHistory(serverId)
    for (const c of serverChallenges) {
      const cat = kategoriChallenge[c.category]
      if (cat) { if (!c.color) c.color = cat.color; if (!c.bg) c.bg = cat.bg; if (!c.emoji) c.emoji = cat.emoji }
      await dbSaveChallenge({ ...c, anakId, serverId: c.id })
    }
    for (const h of serverHistory) {
      const cat = kategoriChallenge[h.category]
      if (cat) { if (!h.color) h.color = cat.color; if (!h.emoji) h.emoji = cat.emoji }
      await dbSaveChallengeHistory({ ...h, anakId })
    }
    anakToolsData.update(map => {
      getAnakToolsData(map, anakId).challenges = serverChallenges.map(c => ({ ...c, serverId: c.id }))
      getAnakToolsData(map, anakId).challengeHistory = serverHistory
      return map
    })
  } catch (e) {
    console.warn('[ToolsStore] pullChallengesFromServer:', e.message)
  }
}

async function pullChecklistsFromServer(anakId) {
  try {
    const serverId = await getServerAnakId(anakId)
    if (!serverId) return
    const serverChecklists = await api.getChecklists(serverId)
    for (const cl of serverChecklists) {
      await dbSaveChecklist({ ...cl, anakId, serverId: cl.id })
    }
    anakToolsData.update(map => {
      getAnakToolsData(map, anakId).checklists = serverChecklists.map(cl => ({ ...cl, serverId: cl.id }))
      return map
    })
  } catch (e) {
    console.warn('[ToolsStore] pullChecklistsFromServer:', e.message)
  }
}

async function pullSchedulesFromServer(anakId) {
  try {
    const serverId = await getServerAnakId(anakId)
    if (!serverId) return
    const serverSchedules = await api.getSchedules(serverId)
    const today = new Date().toISOString().slice(0, 10)
    const histories = await dbGetScheduleHistories(anakId, today)
    const historyScheduleIds = new Set(histories.map(h => h.schedule_id || h.scheduleId))
    for (const s of serverSchedules) {
      s.done = historyScheduleIds.has(s.serverId || s.id)
      s.date = today
      await dbSaveSchedule({ ...s, anakId, serverId: s.id })
    }
    anakToolsData.update(map => {
      getAnakToolsData(map, anakId).schedules = serverSchedules.map(s => ({ ...s, serverId: s.id }))
      return map
    })
  } catch (e) {
    console.warn('[ToolsStore] pullSchedulesFromServer:', e.message)
  }
}

export async function addChallenge(item) {
  const currentId = get(toolsAnakId)
  anakToolsData.update(map => { getAnakToolsData(map, currentId).challenges.push(item); return map })
  dbSaveChallenge({ ...item, anakId: currentId })
  syncOrQueue('addChallenge', { anakId: currentId, data: { ...item } })
}

export async function addPoint({ id, amount }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const c = getAnakToolsData(map, currentId).challenges.find(c => c.id === id)
  if (c) {
    c.points = Math.min(c.maxPoints, c.points + amount)
    anakToolsData.set(map)
    await dbSaveChallenge({ ...c, anakId: currentId, dirty: true })
    if (c.serverId) syncOrQueue('updateChallenge', { anakId: currentId, challengeId: c.serverId, data: { points: c.points } })
  }
}

export async function removePoint({ id }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const c = getAnakToolsData(map, currentId).challenges.find(c => c.id === id)
  if (c) {
    c.points = Math.max(0, c.points - 1)
    anakToolsData.set(map)
    await dbSaveChallenge({ ...c, anakId: currentId, dirty: true })
    if (c.serverId) syncOrQueue('updateChallenge', { anakId: currentId, challengeId: c.serverId, data: { points: c.points } })
  }
}

export async function editChallenge(data) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const c = getAnakToolsData(map, currentId).challenges.find(c => c.id === data.id)
  if (c) {
    Object.assign(c, data)
    anakToolsData.set(map)
    dbSaveChallenge({ ...c, anakId: currentId, dirty: true })
    if (c.serverId) syncOrQueue('updateChallenge', { anakId: currentId, challengeId: c.serverId, data })
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
    if (removed?.serverId) syncOrQueue('deleteChallenge', { anakId: currentId, challengeId: removed.serverId })
  }
}

export async function addChallengeHistory(item) {
  const currentId = get(toolsAnakId)
  anakToolsData.update(map => { getAnakToolsData(map, currentId).challengeHistory.push(item); return map })
  dbSaveChallengeHistory({ ...item, anakId: currentId })
  syncOrQueue('addChallengeHistory', { anakId: currentId, data: { ...item } })
}

export async function addChecklist(item) {
  const currentId = get(toolsAnakId)
  anakToolsData.update(map => {
    const list = getAnakToolsData(map, currentId).checklists
    const idx = list.findIndex(c => c.id === item.id)
    if (idx >= 0) list[idx] = item; else list.push(item)
    return map
  })
  dbSaveChecklist({ ...JSON.parse(JSON.stringify(item)), anakId: currentId })
  const sid = item.serverId || (await (async () => { try { const row = await (await import('../db.js')).default.checklists.get(item.id); return row?.serverId } catch { return null } })())
  if (sid) {
    syncOrQueue('updateChecklist', { anakId: currentId, checklistId: sid, data: { title: item.title, items: item.items } })
  } else {
    syncOrQueue('addChecklist', { anakId: currentId, data: JSON.parse(JSON.stringify(item)) })
  }
}

export async function removeChecklist(index) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const removed = getAnakToolsData(map, currentId).checklists.splice(index, 1)[0]
  anakToolsData.set(map)
  if (removed?.id) dbRemoveChecklist(removed.id)
  if (removed?.serverId) syncOrQueue('deleteChecklist', { anakId: currentId, checklistId: removed.serverId })
}

export async function addChecklistItem({ checklistId, item }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const cl = getAnakToolsData(map, currentId).checklists.find(c => c.id === checklistId)
  if (cl) {
    cl.items.push(item)
    anakToolsData.set(map)
    dbSaveChecklist({ ...JSON.parse(JSON.stringify(cl)), anakId: currentId })
    if (cl.serverId) syncOrQueue('updateChecklist', { anakId: currentId, checklistId: cl.serverId, data: { items: cl.items } })
  }
}

export async function removeChecklistItem({ checklistId, itemIndex }) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const cl = getAnakToolsData(map, currentId).checklists.find(c => c.id === checklistId)
  if (cl) {
    cl.items.splice(itemIndex, 1)
    anakToolsData.set(map)
    dbSaveChecklist({ ...JSON.parse(JSON.stringify(cl)), anakId: currentId })
    if (cl.serverId) syncOrQueue('updateChecklist', { anakId: currentId, checklistId: cl.serverId, data: { items: cl.items } })
  }
}

export async function addSchedule(item) {
  const currentId = get(toolsAnakId)
  if (!item.id) item.id = Date.now()
  anakToolsData.update(map => { getAnakToolsData(map, currentId).schedules.push(item); return map })
  dbSaveSchedule({ ...item, anakId: currentId })
  syncOrQueue('addSchedule', { anakId: currentId, data: { ...item } })
}

export async function updateSchedule(item, data) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const schedules = getAnakToolsData(map, currentId).schedules
  const idx = schedules.findIndex(s => s === item || s.id === item.id)
  if (idx === -1) return
  Object.assign(schedules[idx], data)
  anakToolsData.set(map)

  if (data.done !== undefined) {
    const today = new Date().toISOString().slice(0, 10)
    const now = new Date().toTimeString().slice(0, 5)
    const scheduleId = schedules[idx].serverId || schedules[idx].id
    if (data.done) dbSaveScheduleHistory({ anakId: currentId, scheduleId, date: today, time: now })
    else dbRemoveScheduleHistory(scheduleId, today)
  }
  dbSaveSchedule({ ...schedules[idx], anakId: currentId })
  if (schedules[idx]?.serverId) {
    if (data.done !== undefined) syncOrQueue('toggleScheduleDone', { anakId: currentId, scheduleId: schedules[idx].serverId, date: new Date().toISOString().slice(0, 10), time: new Date().toTimeString().slice(0, 5) })
    else syncOrQueue('updateSchedule', { anakId: currentId, scheduleId: schedules[idx].serverId, data })
  }
}

export async function removeSchedule(item) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const schedules = getAnakToolsData(map, currentId).schedules
  const idx = schedules.findIndex(s => s === item || s.id === item.id)
  if (idx === -1) return
  const removed = schedules.splice(idx, 1)[0]
  anakToolsData.set(map)
  if (removed?.id) { dbRemoveSchedule(removed.id); dbRemoveScheduleHistories(removed.id) }
  if (removed?.serverId) syncOrQueue('deleteSchedule', { anakId: currentId, scheduleId: removed.serverId })
}

export async function addWorksheet(item) {
  const currentId = get(toolsAnakId)
  const id = await dbSaveWorksheet({ ...item, anakId: currentId })
  item.id = id
  anakToolsData.update(map => { getAnakToolsData(map, currentId).worksheets.push(item); return map })
  syncOrQueue('addWorksheet', { anakId: currentId, data: { ...item } })
  return id
}

export async function removeWorksheetItem(id) {
  const currentId = get(toolsAnakId)
  const map = get(anakToolsData)
  const worksheets = getAnakToolsData(map, currentId).worksheets
  const idx = worksheets.findIndex(w => w.id === id)
  if (idx === -1) return
  const removed = worksheets.splice(idx, 1)[0]
  anakToolsData.set(map)
  dbRemoveWorksheet(id)
  if (removed?.serverId) syncOrQueue('deleteWorksheet', { anakId: currentId, worksheetId: removed.serverId })
}
