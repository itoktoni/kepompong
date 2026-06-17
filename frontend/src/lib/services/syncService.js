import { getSyncQueue, removeSyncQueueItem, getSyncQueueCount, saveChallenge, saveChecklist, saveSchedule, saveWorksheet, addToSyncQueue as dbAddToSyncQueue } from '../db.js'
import * as api from '../services/api.js'
import { isOffline } from '../utils/network.js'
import { setSyncing, setPending, setCurrentAction, recordSyncResult } from '../stores/syncStatusStore.js'

const MAX_ATTEMPTS = 3
const BACKOFF_BASE = 5000
const TAG = '[Sync]'

function getBackoffDelay(attempts) {
  return BACKOFF_BASE * Math.pow(3, attempts)
}

export async function queue(action, payload) {
  await dbAddToSyncQueue({ action, payload })
  const count = await getSyncQueueCount()
  setPending(count)
  console.log(TAG, `+ Queued: ${action} (pending: ${count})`)
}

export async function processSyncQueue() {
  if (isOffline()) {
    console.log(TAG, 'Skip — offline')
    return { processed: 0, failed: 0 }
  }
  if (!api.isAuthenticated()) {
    console.log(TAG, 'Skip — not authenticated')
    return { processed: 0, failed: 0 }
  }

  const queue = await getSyncQueue()
  const count = queue.length
  if (!count) {
    console.log(TAG, 'Queue empty')
    return { processed: 0, failed: 0 }
  }

  console.log(TAG, `▶ Starting sync — ${count} item(s) in queue`)
  setSyncing(true)
  setPending(count)

  let processed = 0
  let failed = 0

  for (const entry of queue) {
    if (entry.attempts >= MAX_ATTEMPTS) {
      console.warn(TAG, `✗ Dropping ${entry.action} — max attempts reached`)
      await removeSyncQueueItem(entry.id)
      failed++
      continue
    }

    if (entry.nextRetryAt && Date.now() < entry.nextRetryAt) {
      continue
    }

    const label = `[${processed + failed + 1}/${count}] ${entry.action}`
    setCurrentAction(entry.action)

    try {
      await executeAction(entry)
      await removeSyncQueueItem(entry.id)
      processed++
      console.log(TAG, `✓ ${label}`)
    } catch (e) {
      console.warn(TAG, `✗ ${label} — ${e.message}`)
      const nextAttempt = entry.attempts + 1
      if (nextAttempt >= MAX_ATTEMPTS) {
        await removeSyncQueueItem(entry.id)
        failed++
      } else {
        const delay = getBackoffDelay(nextAttempt)
        const { db } = await import('../db.js')
        await db.syncQueue.update(entry.id, { attempts: nextAttempt, nextRetryAt: Date.now() + delay })
        console.log(TAG, `  ↻ Retry in ${delay / 1000}s (attempt ${nextAttempt}/${MAX_ATTEMPTS})`)
      }
    }
  }

  const remaining = await getSyncQueueCount()
  setPending(remaining)
  recordSyncResult(processed, failed)

  console.log(TAG, `■ Done — synced: ${processed}, failed: ${failed}, remaining: ${remaining}`)
  return { processed, failed }
}

async function executeAction(entry) {
  const { action, payload } = entry

  switch (action) {
    case 'addAnak': {
      const saved = await api.addAnak(payload.data)
      return saved
    }

    case 'updateAnak': {
      await api.updateAnak(payload.anakId, payload.data)
      return
    }

    case 'deleteAnak': {
      await api.deleteAnak(payload.anakId)
      return
    }

    case 'addSkill': {
      await api.addSkill(payload.anakId, payload.data)
      return
    }

    case 'deleteSkill': {
      await api.deleteSkill(payload.anakId, payload.skillKey)
      return
    }

    case 'resetSkill': {
      await api.deleteCompletedSkill(payload.anakId, payload.skillKey)
      await api.addSkill(payload.anakId, payload.skillData)
      return
    }

    case 'addActivity': {
      await api.addActivity(payload.anakId, payload.data)
      return
    }

    case 'addEvaluation': {
      await api.addEvaluation(payload.anakId, payload.data)
      return
    }

    case 'addChallenge': {
      const saved = await api.addChallenge(payload.anakId, payload.data)
      if (saved?.id) {
        payload.data.serverId = saved.id
        await saveChallenge({ ...payload.data, anakId: payload.anakId })
        if (typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('sync-id-update', { detail: { table: 'challenges', anakId: payload.anakId, localId: payload.data.id, serverId: saved.id } }))
        }
      }
      return saved
    }

    case 'updateChallenge': {
      if (!payload.challengeId) { console.warn(TAG, 'updateChallenge: no challengeId, skipping'); return }
      await api.updateChallenge(payload.anakId, payload.challengeId, payload.data)
      return
    }

    case 'deleteChallenge': {
      if (!payload.challengeId) { console.warn(TAG, 'deleteChallenge: no challengeId, skipping'); return }
      await api.deleteChallenge(payload.anakId, payload.challengeId)
      return
    }

    case 'addChecklist': {
      const saved = await api.addChecklist(payload.anakId, payload.data)
      if (saved?.id) {
        payload.data.serverId = saved.id
        await saveChecklist({ ...payload.data, anakId: payload.anakId })
        if (typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('sync-id-update', { detail: { table: 'checklists', anakId: payload.anakId, localId: payload.data.id, serverId: saved.id } }))
        }
      }
      return saved
    }

    case 'updateChecklist': {
      if (!payload.checklistId) { console.warn(TAG, 'updateChecklist: no checklistId, skipping'); return }
      await api.updateChecklist(payload.anakId, payload.checklistId, payload.data)
      return
    }

    case 'deleteChecklist': {
      if (!payload.checklistId) { console.warn(TAG, 'deleteChecklist: no checklistId, skipping'); return }
      await api.deleteChecklist(payload.anakId, payload.checklistId)
      return
    }

    case 'addSchedule': {
      const saved = await api.addSchedule(payload.anakId, payload.data)
      if (saved?.id) {
        payload.data.serverId = saved.id
        await saveSchedule({ ...payload.data, anakId: payload.anakId })
        if (typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('sync-id-update', { detail: { table: 'schedules', anakId: payload.anakId, localId: payload.data.id, serverId: saved.id } }))
        }
      }
      return saved
    }

    case 'updateSchedule': {
      if (!payload.scheduleId) { console.warn(TAG, 'updateSchedule: no scheduleId, skipping'); return }
      await api.updateSchedule(payload.anakId, payload.scheduleId, payload.data)
      return
    }

    case 'deleteSchedule': {
      if (!payload.scheduleId) { console.warn(TAG, 'deleteSchedule: no scheduleId, skipping'); return }
      await api.deleteSchedule(payload.anakId, payload.scheduleId)
      return
    }

    case 'toggleScheduleDone': {
      if (!payload.scheduleId) { console.warn(TAG, 'toggleScheduleDone: no scheduleId, skipping'); return }
      await api.toggleScheduleDone(payload.anakId, payload.scheduleId, payload.date, payload.time)
      return
    }

    case 'addWorksheet': {
      const saved = await api.addWorksheet(payload.anakId, payload.data)
      if (saved?.id) {
        payload.data.serverId = saved.id
        await saveWorksheet({ ...payload.data, anakId: payload.anakId })
        if (typeof window !== 'undefined') {
          window.dispatchEvent(new CustomEvent('sync-id-update', { detail: { table: 'worksheets', anakId: payload.anakId, localId: payload.data.id, serverId: saved.id } }))
        }
      }
      return saved
    }

    case 'deleteWorksheet': {
      await api.deleteWorksheet(payload.anakId, payload.worksheetId)
      return
    }

    default:
      console.warn(TAG, `Unknown action: ${action}`)
      await removeSyncQueueItem(entry.id)
  }
}

let isProcessing = false
let processingSince = 0

function setProcessing(val) {
  isProcessing = val
  processingSince = val ? Date.now() : 0
}

export async function trySync() {
  if (isOffline()) return
  if (isProcessing) {
    if (Date.now() - processingSince > 30000) {
      console.warn(TAG, '⚠ Processing stuck >30s, resetting')
      setProcessing(false)
    } else {
      return
    }
  }
  const count = await getSyncQueueCount()
  if (count > 0) {
    console.log(TAG, `🔄 trySync — ${count} pending`)
    setProcessing(true)
    try {
      await processSyncQueue()
    } finally {
      setProcessing(false)
    }
  }
}

export function initSyncListener() {
  if (typeof window === 'undefined') return

  window.addEventListener('online', async () => {
    console.log(TAG, '⚡ Network back online — checking queue...')
    if (isProcessing) return
    setProcessing(true)
    try {
      await processSyncQueue()
    } finally {
      setProcessing(false)
    }
  })

  window.addEventListener('offline', () => {
    console.log(TAG, '⚡ Network went offline')
  })

  setInterval(async () => {
    if (isOffline()) return
    if (isProcessing) {
      if (Date.now() - processingSince > 30000) {
        console.warn(TAG, '⚠ Poll: stuck >30s, resetting')
        setProcessing(false)
      } else {
        return
      }
    }
    const count = await getSyncQueueCount()
    if (count > 0) {
      console.log(TAG, `⏰ Poll — ${count} pending, starting sync...`)
      setProcessing(true)
      try {
        await processSyncQueue()
      } finally {
        setProcessing(false)
      }
    }
  }, 10000)
}
