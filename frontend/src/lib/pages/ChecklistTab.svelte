<script>
  import { toolsData, toolsAnakId, addChecklist, removeChecklist, addChecklistItem, removeChecklistItem, refreshChecklists } from '../stores/toolsStore.js'
  import { anakList } from '../stores/anakStore.js'
  import AppModal from '../components/AppModal.svelte'
  import AnakDropdown from '../components/AnakDropdown.svelte'
  import { shareChecklistImage } from '../utils/share.js'

  let checklistsData = $state([])
  let anakListVal = $state([])
  let toolsAnakIdVal = $state(null)
  let showForm = $state(false)
  let newTitle = $state('')
  let titleError = $state('')
  let showItemForm = $state(false)
  let newItemLabel = $state('')
  let itemError = $state('')
  let activeChecklistId = $state(null)

  const selectedAnakName = $derived.by(() => {
    const a = anakListVal.find(a => a.id === toolsAnakIdVal)
    return a ? a.nama : 'Anak'
  })

  $effect(() => {
    const u1 = toolsData.subscribe(v => {
      checklistsData = v.checklists || []
    })
    const u2 = anakList.subscribe(v => { anakListVal = v })
    const u3 = toolsAnakId.subscribe(v => { toolsAnakIdVal = v })
    return () => { u1(); u2(); u3() }
  })

  $effect(() => {
    if (toolsAnakIdVal) refreshChecklists(toolsAnakIdVal)
  })

  function checked(cl) {
    return cl.items.filter(i => i.done).length
  }

  function percent(cl) {
    if (!cl.items.length) return 0
    return Math.round((checked(cl) / cl.items.length) * 100)
  }

  function toggleItem(cl, index) {
    cl.items[index].done = !cl.items[index].done
    addChecklist({ ...cl, items: cl.items.map(i => ({ ...i })) })
    checklistsData = [...checklistsData]
  }

  function closeForm() {
    showForm = false
    newTitle = ''
    titleError = ''
  }

  function handleAddChecklist() {
    titleError = ''
    if (!newTitle.trim()) {
      titleError = 'Nama checklist wajib diisi'
      return
    }
    addChecklist({ id: Date.now(), title: newTitle.trim(), items: [] })
    closeForm()
  }

  function openAddItem(cl) {
    activeChecklistId = cl.id
    showItemForm = true
  }

  function closeItemForm() {
    showItemForm = false
    newItemLabel = ''
    itemError = ''
    activeChecklistId = null
  }

  function handleAddItem() {
    itemError = ''
    if (!newItemLabel.trim()) {
      itemError = 'Nama aktivitas wajib diisi'
      return
    }
    if (!activeChecklistId) return
    addChecklistItem({
      checklistId: activeChecklistId,
      item: { label: newItemLabel.trim(), done: false }
    })
    closeItemForm()
  }

  function handleRemoveChecklist(index) {
    removeChecklist(index)
  }

  function handleRemoveItem(checklistId, itemIndex) {
    removeChecklistItem({ checklistId, itemIndex })
  }

  function shareChecklist(cl) {
    shareChecklistImage(cl.title, cl.items, checked(cl), percent(cl), {
      childName: selectedAnakName
    })
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  <AnakDropdown anakList={anakListVal} value={toolsAnakIdVal} onselect={(id) => toolsAnakId.set(id)} />
<div class="space-y-4 mt-4">
  {#each checklistsData as cl, idx (idx)}
    <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md">
      <div class="flex items-center justify-between mb-3">
        <h4 class="font-label-lg text-text-main flex items-center gap-2">
          <span class="w-7 h-7 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-xs">✅</span>
          {cl.title}
        </h4>
        <div class="flex items-center gap-2">
          <span class="text-xs font-bold text-primary bg-success-soft px-2 py-1 rounded-full">{checked(cl)}/{cl.items.length}</span>
          <button onclick={() => handleRemoveChecklist(idx)}
            class="w-7 h-7 rounded-full flex items-center justify-center text-on-surface-variant hover:text-red-500 hover:bg-red-50 transition-colors">
            <span class="text-base">❌</span>
          </button>
        </div>
      </div>

      <div class="space-y-2 mb-4">
        {#each cl.items as item, i (i)}
          <label class="flex items-start gap-3 cursor-pointer select-none group">
            <div class="relative mt-0.5 shrink-0">
              <input type="checkbox" checked={item.done} onchange={() => toggleItem(cl, i)} class="peer sr-only" />
              <div class="w-5 h-5 rounded-md border-2 border-outline-variant bg-white transition-all peer-checked:bg-blue-500 peer-checked:border-blue-500 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500/40">
                {#if item.done}
                  <svg class="w-full h-full text-white p-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                  </svg>
                {/if}
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <span class="font-body-md text-sm text-text-main block">{item.label}</span>
            </div>
            <button onclick={() => handleRemoveItem(cl.id, i)}
              class="w-6 h-6 rounded-full flex items-center justify-center text-on-surface-variant/50 hover:text-red-500 hover:bg-red-50 opacity-0 group-hover:opacity-100 transition-all shrink-0">
              <span class="text-sm">✕</span>
            </button>
          </label>
        {/each}
        {#if !cl.items.length}
          <div class="text-center text-xs text-on-surface-variant py-2">
            <p class="text-2xl mb-1">📋</p>
            Belum ada item
          </div>
        {/if}
      </div>

      <div class="w-full h-2 bg-white rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-700"
          style="width: {percent(cl)}%; background: #176c33"></div>
      </div>

      <div class="flex items-center gap-2 mt-3">
        <button onclick={() => openAddItem(cl)}
          class="h-9 flex-1 rounded-xl border-2 border-dashed text-xs font-bold transition-colors flex items-center justify-center gap-1.5 border-[#B7D9BC] text-primary">
          <span class="text-base">+</span>
          Tambah Item
        </button>
        <button onclick={() => shareChecklist(cl)}
          class="h-9 w-9 rounded-xl border-2 flex items-center justify-center transition-all active:scale-95 border-[#B7D9BC] text-primary">
          <span class="text-base">➤</span>
        </button>
      </div>
    </div>
  {/each}

  {#if !checklistsData.length}
    <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
      <p class="text-3xl mb-2">✅</p>
      <p class="text-sm text-on-surface-variant font-medium">Belum ada checklist</p>
    </div>
  {/if}

  <button onclick={() => { showForm = true }}
    class="w-full py-3 mt-4 rounded-2xl text-white text-sm font-bold btn-pop-green flex items-center justify-center gap-2">
    <span class="text-lg">+</span>
    Buat Checklist
  </button>
</div>
</div>

<AppModal show={showForm} onclose={closeForm} title="Tambah Checklist">
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-bold text-text-main mb-1">Nama Checklist</label>
      <input type="text" bind:value={newTitle} placeholder="Contoh: Pagi Hari"
        class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] bg-white text-sm text-text-main focus:outline-none focus:border-primary transition-colors" />
      {#if titleError}
        <p class="text-xs text-red-500 mt-1">{titleError}</p>
      {/if}
    </div>
  </div>
  <div class="flex gap-3 mt-6">
    <button onclick={closeForm}
      class="flex-1 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm font-bold text-on-surface-variant transition-colors">
      Batal
    </button>
    <button onclick={handleAddChecklist}
      class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-bold btn-pop-green">
      Simpan
    </button>
  </div>
</AppModal>

<AppModal show={showItemForm} onclose={closeItemForm} title="Tambah Item">
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-bold text-text-main mb-1">Nama Aktivitas</label>
      <input type="text" bind:value={newItemLabel} placeholder="Contoh: Sikat gigi"
        class="w-full px-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] bg-white text-sm text-text-main focus:outline-none focus:border-primary transition-colors" />
      {#if itemError}
        <p class="text-xs text-red-500 mt-1">{itemError}</p>
      {/if}
    </div>
  </div>
  <div class="flex gap-3 mt-6">
    <button onclick={closeItemForm}
      class="flex-1 py-2.5 rounded-xl border-2 border-[#B7D9BC] text-sm font-bold text-on-surface-variant transition-colors">
      Batal
    </button>
    <button onclick={handleAddItem}
      class="flex-1 py-2.5 rounded-xl bg-primary text-white text-sm font-bold btn-pop-green">
      Simpan
    </button>
  </div>
</AppModal>

<style>
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 4px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(4px);
    box-shadow: 0 0px 0 #176c33;
  }
</style>
