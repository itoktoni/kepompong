import { writable, derived } from 'svelte/store'

export const syncStatus = writable({
  syncing: false,
  pending: 0,
  processed: 0,
  failed: 0,
  lastSyncAt: null,
  currentAction: '',
})

export const isSyncing = derived(syncStatus, ($s) => $s.syncing)
export const pendingCount = derived(syncStatus, ($s) => $s.pending)
export const hasPending = derived(syncStatus, ($s) => $s.pending > 0)

export function setSyncing(val) {
  syncStatus.update(s => ({ ...s, syncing: val }))
}

export function setPending(count) {
  syncStatus.update(s => ({ ...s, pending: count }))
}

export function setCurrentAction(action) {
  syncStatus.update(s => ({ ...s, currentAction: action }))
}

export function recordSyncResult(processed, failed) {
  syncStatus.update(s => ({
    ...s,
    syncing: false,
    processed: s.processed + processed,
    failed: s.failed + failed,
    lastSyncAt: new Date().toLocaleTimeString('id-ID'),
    currentAction: '',
  }))
}

export function resetSyncStatus() {
  syncStatus.set({
    syncing: false, pending: 0, processed: 0, failed: 0,
    lastSyncAt: null, currentAction: '',
  })
}
