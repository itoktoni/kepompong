<script>
  import { updateActivity, getActivitiesGrouped, generateActivityPrompt } from '../services/api.js'
  import { activitiesCache } from '../stores/activityStore.js'
  import { buildAktivitasDataFromAPI, setAktivitasData } from '../data/activities.js'

  let { item } = $props()

  let devStatus = $state('')
  let devCoverFile = $state(null)
  let devSaving = $state(false)
  let devSaveMsg = $state('')
  let devOpen = $state(false)
  let copied = $state(false)
  let btnRef = $state(null)
  let generating = $state(false)
  let showPrompt = $state(false)

  const dropdownPos = $derived.by(() => {
    if (!btnRef) return { top: 60, right: 16 }
    const rect = btnRef.getBoundingClientRect()
    return { top: rect.bottom + 8, right: window.innerWidth - rect.right }
  })

  const statusColors = {
    approved: { bg: '#E1F2E5', text: '#176c33', label: 'Approved' },
    pending: { bg: '#FFF3E0', text: '#E65100', label: 'Pending' },
    review: { bg: '#E3F2FD', text: '#0D47A1', label: 'Review' },
    rejected: { bg: '#FFEBEE', text: '#C62828', label: 'Rejected' },
  }

  const promptLines = $derived(item.prompt ? item.prompt.split('\n').length : 0)
  const promptChars = $derived(item.prompt ? item.prompt.length : 0)

  export function initStatus() {
    devStatus = item.status || 'approved'
    devCoverFile = null
    devSaveMsg = ''
    copied = false
    showPrompt = false
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
      formData.append('type', item.key || item.type || '')
      formData.append('path', `images/${item.key || item.type || 'unknown'}`)
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

  async function generatePrompt() {
    if (!item.id || generating) return
    generating = true
    devSaveMsg = ''
    try {
      const res = await generateActivityPrompt(item.id)
      if (res.prompt) {
        item.prompt = res.prompt
        devSaveMsg = 'Prompt generated!'
      } else {
        devSaveMsg = 'Gagal generate prompt'
      }
    } catch (e) {
      devSaveMsg = 'Gagal: ' + (e.message || 'Error')
    }
    generating = false
  }

  function copyPrompt() {
    if (!item.prompt) return
    navigator.clipboard.writeText(item.prompt)
    copied = true
    setTimeout(() => copied = false, 2000)
  }
</script>

<button onclick={(e) => { e.stopPropagation(); if (!devOpen) initStatus(); devOpen = !devOpen }}
  bind:this={btnRef}
  class="w-11 h-11 border-4 border-white rounded-full flex items-center justify-center text-xl shadow-md hover:scale-105 active:scale-95 transition-all shrink-0"
  style="background: {statusColors[item.status]?.bg || '#FFF3E0'}; color: {statusColors[item.status]?.text || '#E65100'}">
  <span>✏️</span>
</button>

{#if devOpen}
  <!-- svelte-ignore a11y_click_events_have_key_events a11y_no_static_element_interactions -->
  <div class="fixed inset-0 z-[105]" onclick={() => devOpen = false}></div>
  <div class="fixed w-80 rounded-3xl border-2 border-[#B7D9BC] bg-white shadow-2xl z-[110] overflow-hidden"
    style="top: {dropdownPos.top}px; right: {dropdownPos.right}px">

    <div class="px-4 py-3 border-b-2 border-[#B7D9BC]/50" style="background: linear-gradient(135deg, #E8F5E9, #F1F8E9)">
      <p class="text-xs font-bold text-primary truncate">{item.title}</p>
      <p class="text-[10px] text-on-surface-variant mt-0.5">ID: {item.id} · {item.type}</p>
    </div>

    <div class="p-4 space-y-3">
      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Status</label>
        <div class="flex gap-1.5">
          {#each Object.entries(statusColors) as [key, sc]}
            <button onclick={() => devStatus = key}
              class="flex-1 py-1.5 rounded-xl text-[10px] font-bold border-2 transition-all"
              style="border-color: {devStatus === key ? sc.text : '#E5E7EB'}; background: {devStatus === key ? sc.bg : 'white'}; color: {devStatus === key ? sc.text : '#9CA3AF'}">
              {sc.label}
            </button>
          {/each}
        </div>
      </div>

      <div class="space-y-1.5">
        <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Cover Image</label>
        <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-dashed border-[#B7D9BC] bg-canvas-cream cursor-pointer hover:border-primary transition-colors">
          <span class="text-sm">{devCoverFile ? '🖼️' : '📤'}</span>
          <span class="text-xs font-medium text-on-surface-variant truncate">{devCoverFile ? devCoverFile.name : 'Pilih gambar...'}</span>
          <input type="file" accept="image/*" class="hidden" onchange={(e) => devCoverFile = e.target.files[0] || null} />
        </label>
      </div>

      <div class="space-y-1.5">
        <div class="flex items-center justify-between">
          <label class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Prompt</label>
          {#if item.prompt}
            <span class="text-[9px] text-on-surface-variant">{promptChars} chars · {promptLines} lines</span>
          {/if}
        </div>
        <div class="flex gap-1.5">
          <button onclick={generatePrompt} disabled={generating}
            class="flex-1 py-2 rounded-xl text-xs font-bold border-2 transition-all flex items-center justify-center gap-1.5 disabled:opacity-40"
            style="border-color: {generating ? '#176c33' : '#B7D9BC'}; background: {generating ? '#E1F2E5' : 'white'}; color: {generating ? '#176c33' : '#6B7280'}">
            {#if generating}
              <span class="w-3 h-3 border-2 border-primary border-t-transparent rounded-full animate-spin"></span>
              Generating...
            {:else}
              generate
            {/if}
          </button>
          <button onclick={copyPrompt} disabled={!item.prompt}
            class="flex-1 py-2 rounded-xl text-xs font-bold border-2 transition-all flex items-center justify-center gap-1.5 disabled:opacity-40"
            style="border-color: {copied ? '#176c33' : '#B7D9BC'}; background: {copied ? '#E1F2E5' : 'white'}; color: {copied ? '#176c33' : '#6B7280'}">
            {copied ? '✓ copied' : 'copy'}
          </button>
          {#if item.prompt}
            <button onclick={() => showPrompt = !showPrompt}
              class="py-2 px-3 rounded-xl text-xs font-bold border-2 transition-all"
              style="border-color: {showPrompt ? '#176c33' : '#B7D9BC'}; background: {showPrompt ? '#E1F2E5' : 'white'}; color: {showPrompt ? '#176c33' : '#6B7280'}">
              {showPrompt ? '▾' : '▸'}
            </button>
          {/if}
        </div>
        {#if showPrompt && item.prompt}
          <div class="rounded-xl border-2 border-[#B7D9BC]/50 bg-canvas-cream p-3 max-h-48 overflow-y-auto">
            <pre class="text-[10px] text-on-surface-variant whitespace-pre-wrap leading-relaxed font-mono">{item.prompt}</pre>
          </div>
        {/if}
      </div>

      {#if devSaveMsg}
        <div class="rounded-xl px-3 py-2 text-xs font-bold text-center"
          style="background: {devSaveMsg.includes('Gagal') ? '#FFEBEE' : '#E1F2E9'}; color: {devSaveMsg.includes('Gagal') ? '#C62828' : '#176c33'}">
          {devSaveMsg}
        </div>
      {/if}

      <button onclick={saveDevChanges} disabled={devSaving}
        class="w-full py-2.5 rounded-xl text-white text-sm font-bold disabled:opacity-50 transition-all active:scale-[0.98]"
        style="background: #176C33; box-shadow: 0 4px 0 #0d4a22;">
        {devSaving ? 'Menyimpan...' : 'Simpan'}
      </button>
    </div>
  </div>
{/if}
