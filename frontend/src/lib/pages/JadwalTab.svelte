<script>
  import { toolsData, toolsAnakId, addSchedule, removeSchedule, updateSchedule, refreshSchedules } from '../stores/toolsStore.js'
  import { anakList } from '../stores/anakStore.js'
  import AppModal from '../components/AppModal.svelte'
  import AppInput from '../components/AppInput.svelte'
  import AppButton from '../components/AppButton.svelte'
  import AnakDropdown from '../components/AnakDropdown.svelte'
  import { shareJadwalImage } from '../utils/share.js'

  let schedules = $state([])
  let currentAnakId = $state(null)
  let anakListVal = $state([])

  let showForm = $state(false)
  let showHistory = $state(false)
  let newLabel = $state('')
  let newTime = $state('')
  let labelError = $state('')
  let timeError = $state('')

  const undoneSchedules = $derived(schedules.filter(s => !s.done))
  const doneSchedules = $derived(schedules.filter(s => s.done))

  // Group done schedules by date
  const doneSchedulesByDate = $derived.by(() => {
    const grouped = {}
    for (const s of doneSchedules) {
      const date = s.date || 'unknown'
      if (!grouped[date]) grouped[date] = []
      grouped[date].push(s)
    }
    // Sort by date descending
    return Object.entries(grouped)
      .sort((a, b) => b[0].localeCompare(a[0]))
      .map(([date, items]) => ({ date, items }))
  })

  const selectedAnakName = $derived.by(() => {
    const a = anakListVal.find(a => a.id === currentAnakId)
    return a ? a.nama : 'Anak'
  })

  $effect(() => {
    const u1 = toolsData.subscribe(v => {
      schedules = v?.schedules || []
    })
    const u2 = toolsAnakId.subscribe(v => {
      currentAnakId = v
    })
    const u3 = anakList.subscribe(v => {
      anakListVal = v
    })
    return () => { u1(); u2(); u3() }
  })

  $effect(() => {
    const id = currentAnakId
    if (id) refreshSchedules(id)
  })

  function getToday() {
    return new Date().toISOString().slice(0, 10)
  }

  function formatDate(dateStr) {
    if (!dateStr) return 'Tanggal tidak diketahui'
    const date = new Date(dateStr + 'T00:00:00')
    if (isNaN(date.getTime())) return dateStr || 'Tanggal tidak diketahui'

    const today = new Date()
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)

    const dateOnly = date.toISOString().slice(0, 10)
    const todayStr = today.toISOString().slice(0, 10)
    const yesterdayStr = yesterday.toISOString().slice(0, 10)

    if (dateOnly === todayStr) return 'Hari Ini'
    if (dateOnly === yesterdayStr) return 'Kemarin'

    return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' })
  }

  async function toggleDone(item) {
    const newDone = !item.done
    await updateSchedule(item, { done: newDone })
  }

  function closeForm() {
    showForm = false
    newLabel = ''
    newTime = ''
    labelError = ''
    timeError = ''
  }

  async function handleAdd() {
    labelError = ''
    timeError = ''
    let valid = true
    if (!newLabel.trim()) {
      labelError = 'Nama aktivitas wajib diisi'
      valid = false
    }
    if (!newTime) {
      timeError = 'Waktu wajib diisi'
      valid = false
    }
    if (!valid) return
    await addSchedule({ time: newTime, label: newLabel.trim(), done: false })
    closeForm()
  }

  async function handleRemove(item) {
    await removeSchedule(item)
  }

  function handleShareJadwal() {
    shareJadwalImage(schedules, { childName: selectedAnakName })
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  <AnakDropdown anakList={anakListVal} value={currentAnakId} onselect={(id) => toolsAnakId.set(id)} />

  <div class="flex items-center justify-between mb-4 mt-5">
    <h3 class="font-headline-md text-text-main flex items-center gap-2">
      <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">📅</span> Jadwal Harian
    </h3>
    <div class="flex items-center gap-2">
      {#if schedules.length > 0}
        <button onclick={handleShareJadwal}
          class="flex items-center gap-1.5 text-sm font-bold text-primary transition-colors bg-success-soft px-3 py-1.5 rounded-full">
          <span class="text-lg">➤</span>
          Share
        </button>
      {/if}
      <button onclick={() => showHistory = !showHistory}
        class="flex items-center gap-1.5 text-sm font-bold text-primary transition-colors bg-success-soft px-3 py-1.5 rounded-full">
        <span class="text-lg">{showHistory ? '✕' : '📖'}</span>
        {showHistory ? 'Tutup' : 'History'}
      </button>
    </div>
  </div>

<div class="space-y-4">
  {#if !showHistory}
    {#each undoneSchedules as s, i (s.id || i)}
      <div class="jadwal-card jadwal-undone"
        onclick={() => toggleDone(s)}
        role="button"
        tabindex="0"
        onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') toggleDone(s) }}>
        <div class="jadwal-icon">
          <span class="text-lg">⏰</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-label-lg text-main">{s.label}</p>
          <p class="text-xs text-on-surface-variant">{s.date} {s.time}</p>
        </div>
        <button class="jadwal-remove" onclick={(e) => { e.stopPropagation(); handleRemove(s) }}>
          <span class="text-base">✕</span>
        </button>
      </div>
    {/each}

    {#if undoneSchedules.length === 0 && doneSchedules.length === 0}
      <div class="jadwal-empty">
        <p class="text-3xl mb-2">📅</p>
        <p class="text-sm text-on-surface-variant font-medium">Belum ada jadwal</p>
      </div>
    {:else if undoneSchedules.length === 0}
      <div class="bg-canvas-cream rounded-[24px] p-6 text-center border-4 border-[#B7D9BC]">
        <p class="text-3xl mb-2">🎉</p>
        <p class="text-sm text-text-main font-bold">Semua jadwal selesai!</p>
        <p class="text-xs text-on-surface-variant mt-1">Hebat! Semua aktivitas hari ini sudah terlaksana.</p>
      </div>
    {/if}

    <button class="btn-pop-green" onclick={() => { showForm = true }}>
      <span class="text-lg">+</span>
      Tambah Jadwal
    </button>
  {:else}
    <div class="space-y-6">
      {#if doneSchedulesByDate.length > 0}
        {#each doneSchedulesByDate as group (group.date)}
          <div>
            <div class="flex items-center gap-2 mb-3">
              <h4 class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">{formatDate(group.date)}</h4>
              <span class="text-xs font-bold text-primary bg-success-soft px-2 py-0.5 rounded-full">{group.items.length}</span>
            </div>
            <div class="space-y-2">
              {#each group.items as s (s.id)}
                <div class="jadwal-card jadwal-done"
                  onclick={() => toggleDone(s)}
                  role="button"
                  tabindex="0"
                  onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') toggleDone(s) }}>
                  <div class="jadwal-icon jadwal-icon-done">
                    <span class="text-lg">✓</span>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-label-lg text-variant line-through">{s.label}</p>
                    <p class="text-xs text-on-surface-variant">{s.date} {s.time}</p>
                  </div>
                  <button class="jadwal-remove" onclick={(e) => { e.stopPropagation(); handleRemove(s) }}>
                    <span class="text-base">✕</span>
                  </button>
                </div>
              {/each}
            </div>
          </div>
        {/each}
      {:else}
        <div class="jadwal-empty">
          <p class="text-3xl mb-2">📋</p>
          <p class="text-sm text-on-surface-variant font-medium">Belum ada jadwal yang selesai</p>
        </div>
      {/if}
    </div>
  {/if}
</div>
</div>

<AppModal show={showForm} title="Tambah Jadwal" onclose={closeForm}>
  <div class="space-y-4">
    <AppInput bind:value={newLabel} label="Nama Aktivitas" placeholder="Contoh: Belajar Membaca" error={labelError} />
    <AppInput bind:value={newTime} label="Waktu" type="time" placeholder="08:00" error={timeError} />
  </div>
  <div class="flex gap-3 mt-6">
    <AppButton variant="outline" onclick={closeForm}>Batal</AppButton>
    <AppButton onclick={handleAdd}>Simpan</AppButton>
  </div>
</AppModal>

<style>
  .jadwal-card {
    background-color: #FFF9F3;
    border-radius: 24px;
    padding: 20px;
    border: 4px solid #B7D9BC;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    display: flex;
    align-items: center;
    gap: 16px;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .jadwal-card:hover {
    box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
  }
  .jadwal-icon {
    width: 40px;
    height: 40px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.15s ease;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
  }
  .jadwal-undone .jadwal-icon {
    background-color: #E1F2E5;
    color: #176c33;
  }
  .jadwal-done .jadwal-icon {
    background-color: #176c33;
    color: white;
  }
  .text-main {
    color: #1C1B1F;
  }
  .text-variant {
    color: #79747E;
  }
  .line-through {
    text-decoration: line-through;
  }
  .jadwal-remove {
    width: 32px;
    height: 32px;
    border-radius: 9999px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgb(186 26 26 / 0.5);
    transition: all 0.15s ease;
    background: transparent;
    border: none;
    cursor: pointer;
  }
  .jadwal-remove:hover {
    background-color: rgb(186 26 26 / 0.1);
    color: #BA1A1A;
  }
  .jadwal-empty {
    background-color: #FFF9F3;
    border-radius: 24px;
    padding: 32px;
    text-align: center;
    border: 4px dashed #B7D9BC;
  }
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 4px 0 #176c33;
    transition: all 0.1s ease;
    width: 100%;
    padding: 12px 0;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 700;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
    cursor: pointer;
  }
  .btn-pop-green:active {
    transform: translateY(4px);
    box-shadow: 0 0px 0 #176c33;
  }
</style>
