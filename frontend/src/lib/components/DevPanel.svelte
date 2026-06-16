<script>
  import { updateActivity, getActivitiesGrouped } from '../services/api.js'
  import { activitiesCache } from '../stores/activityStore.js'
  import { buildAktivitasDataFromAPI, setAktivitasData } from '../data/activities.js'

  let { item } = $props()

  let devStatus = $state('')
  let devCoverFile = $state(null)
  let devSaving = $state(false)
  let devSaveMsg = $state('')
  let devOpen = $state(false)
  let copied = $state(false)

  const statusColors = {
    approved: { bg: '#E1F2E5', text: '#176c33', label: 'Approved' },
    pending: { bg: '#FFF3E0', text: '#E65100', label: 'Pending' },
    review: { bg: '#E3F2FD', text: '#0D47A1', label: 'Review' },
    rejected: { bg: '#FFEBEE', text: '#C62828', label: 'Rejected' },
  }

  export function initStatus() {
    devStatus = item.status || 'approved'
    devCoverFile = null
    devSaveMsg = ''
    copied = false
  }

  export function isOpen() {
    return devOpen
  }

  export function close() {
    devOpen = false
  }

  async function saveDevChanges() {
    if (!item.id) return
    devSaving = true
    devSaveMsg = ''
    try {
      const formData = new FormData()
      formData.append('status', devStatus)
      if (devCoverFile) formData.append('image', devCoverFile)
      await updateActivity(item.id, formData)
      item.status = devStatus
      devSaveMsg = 'Berhasil!'
      devCoverFile = null
      const { saveSetting } = await import('$lib/db.js')
      const serverData = await getActivitiesGrouped()
      if (serverData && typeof serverData === 'object') {
        await saveSetting('activities_cache', serverData)
        activitiesCache.set(serverData)
        setAktivitasData(buildAktivitasDataFromAPI(serverData))
      }
    } catch (e) {
      devSaveMsg = 'Gagal: ' + (e.message || 'Error')
    }
    devSaving = false
  }
</script>

<button onclick={(e) => { e.stopPropagation(); if (!devOpen) initStatus(); devOpen = !devOpen }}
  class="w-11 h-11 border-4 border-white rounded-full flex items-center justify-center text-xl shadow-md hover:scale-105 active:scale-95 transition-all shrink-0"
  style="background: {statusColors[item.status]?.bg || '#FFF3E0'}; color: {statusColors[item.status]?.text || '#E65100'}">
  <span class="material-symbols-outlined">edit</span>
</button>

{#if devOpen}
  <div class="absolute top-full right-0 mt-2 w-[calc(100%-2rem)] mx-4 p-3 rounded-2xl border-2 border-[#B7D9BC] bg-white shadow-xl z-20 space-y-2">
    <div class="flex items-center gap-2">
      <label class="text-xs font-bold text-on-surface-variant shrink-0">Status</label>
      <select bind:value={devStatus}
        class="flex-1 px-3 py-1.5 rounded-xl border-2 border-[#B7D9BC] text-sm font-bold bg-white focus:border-primary outline-none">
        <option value="pending">Pending</option>
        <option value="review">Review</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
      <label class="py-1.5 px-3 rounded-xl border-2 border-dashed border-[#B7D9BC] text-xs text-on-surface-variant bg-white cursor-pointer hover:border-primary transition-colors truncate max-w-[140px]">
        {devCoverFile ? devCoverFile.name : 'Upload Cover'}
        <input type="file" accept="image/*" class="hidden" onchange={(e) => devCoverFile = e.target.files[0] || null} />
      </label>
    </div>
    <div class="flex items-center gap-2">
      <button onclick={() => { if (item.prompt) { navigator.clipboard.writeText(item.prompt); copied = true; setTimeout(() => copied = false, 2000) } }}
        class="py-1.5 px-3 rounded-xl text-xs font-bold border-2 bg-white text-on-surface-variant hover:border-primary transition-all shrink-0 disabled:opacity-40 flex items-center gap-1"
        style="border-color: {copied ? '#176c33' : '#B7D9BC'}; {copied ? 'background: #E1F2E5; color: #176c33' : ''}"
        disabled={!item.prompt}
        title={item.prompt || 'No prompt'}>
        <span class="material-symbols-outlined text-sm">{copied ? 'check' : 'content_copy'}</span>
        {copied ? 'Copied!' : 'Copy Prompt'}
      </button>
      <button onclick={saveDevChanges} disabled={devSaving}
        class="flex-1 py-1.5 rounded-xl text-white text-sm font-bold disabled:opacity-50"
        style="background: #176C33; box-shadow: 0 3px 0 #0d4a22;">
        {devSaving ? 'Menyimpan...' : 'Simpan'}
      </button>
      {#if devSaveMsg}
        <p class="text-xs font-bold shrink-0" class:text-primary={devSaveMsg.includes('!')} class:text-error={devSaveMsg.includes('Gagal')}>{devSaveMsg}</p>
      {/if}
    </div>
  </div>
{/if}
