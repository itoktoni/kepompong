import { writable, derived, get } from 'svelte/store'
import * as api from '../services/api.js'
import { getAllActivities, saveActivities } from '../db.js'
import { autoSync } from './syncStore.js'

export const activitiesCache = writable(null)
export const serverCount = writable(0)
export const downloading = writable(false)
export const downloadMessage = writable('')

export const localCount = derived(activitiesCache, ($cache) => {
  if (!$cache) return 0
  return Object.values($cache).reduce((sum, arr) => sum + (Array.isArray(arr) ? arr.length : 0), 0)
})

export const newCount = derived([serverCount, localCount], ([$server, $local]) => Math.max(0, $server - $local))

export async function loadFromCache() {
  // Load from Dexie (IndexedDB) which has larger capacity than localStorage
  const cached = await getAllActivities()
  if (cached && Object.keys(cached).length > 0) activitiesCache.set(cached)
}

export async function checkServer() {
  try {
    const data = await api.getActivitiesGrouped()
    if (data && typeof data === 'object') {
      serverCount.set(Object.values(data).reduce((sum, arr) => sum + (Array.isArray(arr) ? arr.length : 0), 0))
    }
  } catch (e) { /* ignore */ }
}

export async function downloadActivities() {
  downloading.set(true)
  downloadMessage.set('')
  try {
    const data = await api.getActivitiesGrouped()
    if (!data) {
      downloadMessage.set('Gagal terhubung ke server')
      downloading.set(false)
      return
    }
    const count = Object.values(data).reduce((sum, arr) => sum + (Array.isArray(arr) ? arr.length : 0), 0)
    serverCount.set(count)

    // Save to Dexie only (IndexedDB has larger capacity than localStorage)
    await saveActivities(data)

    activitiesCache.set(data)
    downloadMessage.set(`${count} aktivitas berhasil diunduh`)
  } catch (e) {
    downloadMessage.set('Error: ' + e.message)
  }
  downloading.set(false)
}
