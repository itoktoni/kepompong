<script>
  let { show = false, onclose, onsynced } = $props()

  import AppButton from '../components/AppButton.svelte'
  import AppModal from '../components/AppModal.svelte'
  import * as api from '../services/api.js'
  import { syncServerData, getSetting, saveSetting, getAnakList } from '../db.js'

  let loading = $state(false)
  let message = $state('')
  let isLoggedIn = $state(false)

  $effect(() => {
    isLoggedIn = api.isAuthenticated()
  })

  async function handleSyncDown() {
    loading = true
    message = ''
    try {
      const serverList = await api.getAnakList()
      await syncServerData(serverList)
      message = `${serverList.length} data anak berhasil diunduh`
      onsynced?.()
    } catch (e) {
      message = 'Error: ' + e.message
    }
    loading = false
  }

  async function handleSyncUp() {
    loading = true
    message = ''
    try {
      const localList = await getAnakList()
      const cleanList = localList.map(a => {
        const { serverSynced, ...rest } = a
        return rest
      })
      await api.syncToServer(cleanList)
      message = `${cleanList.length} data berhasil diunggah`
      onsynced?.()
    } catch (e) {
      message = 'Error: ' + e.message
    }
    loading = false
  }
</script>

<AppModal {show} title="Sinkronisasi Data" {onclose}>
  {#if !isLoggedIn}
    <div class="text-center py-4">
      <span class="material-symbols-outlined text-4xl text-on-surface-variant mb-2">cloud_off</span>
      <p class="text-on-surface-variant">Masuk untuk sinkronisasi data ke cloud</p>
    </div>
  {:else}
    <p class="text-sm text-on-surface-variant mb-6">
      Backup & restore data anak ke cloud. Data akan disimpan di server untuk akses dari perangkat lain.
    </p>

    <div class="grid grid-cols-2 gap-3 mb-4">
      <AppButton variant="outline" {loading} onclick={handleSyncDown}>
        <span class="material-symbols-outlined text-lg mr-1">cloud_download</span> Download
      </AppButton>
      <AppButton variant="outline" {loading} onclick={handleSyncUp}>
        <span class="material-symbols-outlined text-lg mr-1">cloud_upload</span> Upload
      </AppButton>
    </div>

    {#if message}
      <div class="bg-success-soft text-primary rounded-xl px-4 py-3 text-sm text-center">
        {message}
      </div>
    {/if}
  {/if}
</AppModal>
