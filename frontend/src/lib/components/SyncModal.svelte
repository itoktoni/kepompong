<script>
  let { show = false, onclose, onsynced } = $props()

  import AppButton from '../components/AppButton.svelte'
  import AppModal from '../components/AppModal.svelte'
  import * as api from '../services/api.js'
  import { syncServerData, getAnakList as dbGetAnakList, saveChallenge, saveChecklist, saveSchedule, saveWorksheet, saveChallengeHistory } from '../db.js'
  import { kategoriChallenge } from '../data/challenge.js'
  import { getServerAnakId, processSyncQueue } from '../services/syncService.js'
  import { loadToolsData } from '../stores/toolsStore.js'

  let loading = $state(false)
  let message = $state('')
  let isLoggedIn = $state(false)
  let step = $state('')

  $effect(() => {
    isLoggedIn = api.isAuthenticated()
  })

  async function handleSyncDown() {
    loading = true
    message = ''
    step = ''
    try {
      step = 'Mengunduh data anak...'
      const serverList = await api.getAnakList()
      await syncServerData(serverList)
      const localList = await dbGetAnakList()

      let totalItems = 0
      for (const anak of localList) {
        const serverId = anak.serverId || anak.id
        step = `Mengunduh data ${anak.nama}...`
        try {
          const [challenges, challengeHistory, checklists, schedules] = await Promise.all([
            api.getChallenges(serverId).catch(() => []),
            api.getChallengeHistory(serverId).catch(() => []),
            api.getChecklists(serverId).catch(() => []),
            api.getSchedules(serverId).catch(() => []),
          ])

          for (const c of challenges) {
            const cat = kategoriChallenge[c.category]
            if (cat) { if (!c.color) c.color = cat.color; if (!c.bg) c.bg = cat.bg; if (!c.emoji) c.emoji = cat.emoji }
            await saveChallenge({ ...c, anakId: anak.id, serverId: c.id })
            totalItems++
          }
          for (const h of challengeHistory) {
            const cat = kategoriChallenge[h.category]
            if (cat) { if (!h.color) h.color = cat.color; if (!h.emoji) h.emoji = cat.emoji }
            await saveChallengeHistory({ ...h, anakId: anak.id })
            totalItems++
          }
          for (const cl of checklists) {
            await saveChecklist({ ...cl, anakId: anak.id, serverId: cl.id })
            totalItems++
          }
          for (const s of schedules) {
            await saveSchedule({ ...s, anakId: anak.id, serverId: s.id })
            totalItems++
          }
        } catch (e) {
          console.warn(`Sync ${anak.nama}:`, e.message)
        }
      }

      await loadToolsData(localList)
      message = `✓ ${serverList.length} anak, ${totalItems} item berhasil diunduh`
      onsynced?.()
    } catch (e) {
      message = '✗ Error: ' + e.message
    }
    step = ''
    loading = false
  }

  async function handleSyncUp() {
    loading = true
    message = ''
    step = ''
    try {
      step = 'Mengunggah data...'
      const result = await processSyncQueue()
      if (result.processed > 0) {
        message = `✓ ${result.processed} data berhasil diunggah`
      } else if (result.failed > 0) {
        message = `✗ ${result.failed} data gagal diunggah`
      } else {
        message = '✓ Semua data sudah tersinkron'
      }
      onsynced?.()
    } catch (e) {
      message = '✗ Error: ' + e.message
    }
    step = ''
    loading = false
  }
</script>

<AppModal {show} title="Sinkronisasi Data" {onclose}>
  {#if !isLoggedIn}
    <div class="text-center py-4">
      <span class="text-4xl text-on-surface-variant mb-2">✈︎</span>
      <p class="text-on-surface-variant">Masuk untuk sinkronisasi data ke cloud</p>
    </div>
  {:else}
    <p class="text-sm text-on-surface-variant mb-2">
      Download semua data dari server untuk bekerja offline. Setelah selesai, upload kembali ke server.
    </p>

    <div class="bg-white rounded-xl p-3 border-2 border-[#B7D9BC] mb-4 text-xs text-on-surface-variant space-y-1">
      <p>⬇️ <strong>Download</strong> — tarik data anak, jadwal, checklist, challenge dari server</p>
      <p>⬆️ <strong>Upload</strong> — kirim semua perubahan lokal ke server</p>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-4">
      <AppButton variant="outline" {loading} onclick={handleSyncDown}>
        <span class="text-lg mr-1">⬇️</span> Download
      </AppButton>
      <AppButton variant="outline" {loading} onclick={handleSyncUp}>
        <span class="text-lg mr-1">⬆️</span> Upload
      </AppButton>
    </div>

    {#if loading && step}
      <div class="bg-white rounded-xl px-4 py-3 text-xs text-center text-on-surface-variant border border-[#B7D9BC]/50 mb-2">
        <span class="animate-spin inline-block mr-1">⏳</span> {step}
      </div>
    {/if}

    {#if message}
      <div class="bg-success-soft text-primary rounded-xl px-4 py-3 text-sm text-center">
        {message}
      </div>
    {/if}
  {/if}
</AppModal>
