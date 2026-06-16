import { writable } from 'svelte/store'

const STORAGE_KEY = 'lk_auto_sync'

function createAutoSyncStore() {
  // Default: true (sync langsung ke API)
  const initial = typeof localStorage !== 'undefined'
    ? localStorage.getItem(STORAGE_KEY) !== 'false'
    : true

  const { subscribe, set, update } = writable(initial)

  return {
    subscribe,
    enable: () => {
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem(STORAGE_KEY, 'true')
      }
      set(true)
    },
    disable: () => {
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem(STORAGE_KEY, 'false')
      }
      set(false)
    },
    toggle: () => {
      update(current => {
        const newValue = !current
        if (typeof localStorage !== 'undefined') {
          localStorage.setItem(STORAGE_KEY, String(newValue))
        }
        return newValue
      })
    }
  }
}

export const autoSync = createAutoSyncStore()
