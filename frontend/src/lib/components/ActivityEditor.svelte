<script>
  import { updateActivity, getActivitiesGrouped } from '../services/api.js'
  import { activitiesCache } from '../stores/activityStore.js'
  import { buildAktivitasDataFromAPI, setAktivitasData } from '../data/activities.js'
  import { getSkills } from '../data/skills.js'
  import { resolveActivityImage } from '../utils/images.js'
  import MultiSelect from 'svelte-multiselect'

  let { item, type, onsave, ondelete, onclose } = $props()

  let saving = $state(false)
  let saveMsg = $state('')
  let coverFile = $state(null)
  const allAges = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
  const allSkills = getSkills()

  let form = $state({
    title: '', desc: '', moral: '', creator: '', notes: '', status: 'approved',
    ages: [], selectedSkills: [], pages: [], roles: [],
  })

  $effect(() => {
    if (!item) return
    const pages = item.pages || item.data?.pages || []
    const roles = item.roles || item.data?.roles || []
    form.title = item.title || ''
    form.desc = item.desc || ''
    form.moral = item.moral || ''
    form.creator = item.creator || ''
    form.notes = item.notes || ''
    form.status = item.status || 'approved'
    form.ages = Array.isArray(item.ages) ? item.ages.map(Number) : []
    const rawSkills = Array.isArray(item.skills) ? item.skills : []
    form.selectedSkills = rawSkills.map(s => {
      const key = typeof s === 'string' ? s : s?.key || s?.value || ''
      const found = allSkills.find(sk => sk.key === key)
      return { value: key, label: found ? `${found.emoji} ${found.title}` : key }
    })
    form.pages = pages.map(p => ({
      num: p.num, text: p.text || '', narrator: p.narrator || '',
      dialog: Array.isArray(p.dialog) ? p.dialog.map(d => ({ role: d.role || '', text: d.text || '' })) : [],
      question: p.question || '', answer: p.answer || '',
      options: Array.isArray(p.options) ? p.options.join('\n') : '', dialogue: p.dialogue || '',
    }))
    form.roles = roles.map(r => ({ name: r.name || '', emoji: r.emoji || '', desc: r.desc || '' }))
  })

  function addPage() {
    form.pages = [...form.pages, { num: form.pages.length + 1, text: '', narrator: '', dialog: [], question: '', answer: '', options: '', dialogue: '' }]
  }
  function removePage(i) {
    form.pages = form.pages.filter((_, idx) => idx !== i).map((p, idx) => ({ ...p, num: idx + 1 }))
  }
  function addDialog(pi) { form.pages[pi].dialog = [...form.pages[pi].dialog, { role: '', text: '' }]; form.pages = [...form.pages] }
  function removeDialog(pi, di) { form.pages[pi].dialog = form.pages[pi].dialog.filter((_, idx) => idx !== di); form.pages = [...form.pages] }
  function addRole() { form.roles = [...form.roles, { name: '', emoji: '', desc: '' }] }
  function removeRole(i) { form.roles = form.roles.filter((_, idx) => idx !== i) }
  function toggleAge(a) { form.ages = form.ages.includes(a) ? form.ages.filter(x => x !== a) : [...form.ages, a].sort((x, y) => x - y) }

  const isRoleplay = $derived(type === 'bermain_peran')
  const isQuestion = $derived(type === 'puzzle' || type === 'tebak_tebakan')

  async function handleSave() {
    if (saving) return
    saving = true; saveMsg = ''
    try {
      const data = {
        title: form.title, desc: form.desc, moral: form.moral, creator: form.creator,
        notes: form.notes, status: form.status,         ages: form.ages, skills: form.selectedSkills.map(s => s.value),
        type, path: `images/${type}`,
      }
      if (isRoleplay) {
        data.data = { roles: form.roles.filter(r => r.name), pages: form.pages.map(p => ({ num: p.num, narrator: p.narrator, dialog: p.dialog.filter(d => d.text) })) }
      } else if (isQuestion) {
        data.data = { questions: form.pages.map(p => ({ question: p.question, answer: p.answer, options: p.options ? p.options.split('\n').filter(Boolean) : [] })) }
      } else {
        data.data = { pages: form.pages.map(p => ({ num: p.num, text: p.text || p.narrator || '', dialogue: p.dialogue || undefined })) }
      }
      const fd = new FormData()
      for (const [k, v] of Object.entries(data)) {
        fd.append(k, (k === 'data' || k === 'ages' || k === 'skills') ? JSON.stringify(v) : v)
      }
      if (coverFile) fd.append('image', coverFile)
      await updateActivity(item.id, fd)
      item.title = form.title; item.desc = form.desc; item.moral = form.moral
      item.creator = form.creator; item.notes = form.notes; item.status = form.status
      item.ages = data.ages; item.skills = data.skills
      const sd = await getActivitiesGrouped()
      if (sd && typeof sd === 'object') {
        const { saveSetting } = await import('$lib/db.js')
        await saveSetting('activities_cache', sd); activitiesCache.set(sd); setAktivitasData(buildAktivitasDataFromAPI(sd))
      }
      saveMsg = 'Berhasil!'
      onsave?.()
    } catch (e) { saveMsg = 'Gagal: ' + (e.message || 'Error') }
    saving = false
  }
</script>

<div class="fixed inset-0 z-[100] bg-canvas-cream overflow-y-auto">
  <div class="sticky top-0 z-10 bg-white border-b-4 border-[#B7D9BC] px-4 py-3 flex items-center gap-3">
    <button onclick={onclose} class="w-10 h-10 rounded-full bg-error text-white flex items-center justify-center text-lg shrink-0">✕</button>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-bold text-on-surface truncate">Edit: {item.title}</p>
      <p class="text-[10px] text-on-surface-variant">ID: {item.id} · {type}</p>
    </div>
    <button onclick={() => ondelete?.()} class="w-10 h-10 rounded-full bg-error/80 text-white flex items-center justify-center text-base shrink-0">🗑</button>
    <button onclick={handleSave} disabled={saving} class="px-5 py-2.5 rounded-xl text-white text-sm font-bold disabled:opacity-50 shrink-0" style="background:#176C33;box-shadow:0 4px 0 #0d4a22">
      {saving ? '...' : 'Simpan'}
    </button>
  </div>

  <div class="max-w-lg lg:max-w-none mx-auto p-4 pb-24">
    {#if saveMsg}
      <div class="rounded-xl px-4 py-3 text-sm font-bold text-center mb-4"
        style="background:{saveMsg.includes('Gagal')?'#FFEBEE':'#E1F2E9'};color:{saveMsg.includes('Gagal')?'#C62828':'#176c33'}">{saveMsg}</div>
    {/if}

    <div class="flex flex-col lg:flex-row gap-4">

      <div class="lg:w-[360px] shrink-0 space-y-4">
        <div class="bg-white rounded-2xl border-2 border-[#B7D9BC] p-4 space-y-3">
          <p class="text-xs font-bold text-primary uppercase tracking-wider">Info Dasar</p>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant">Judul</label>
            <input type="text" bind:value={form.title} class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none" />
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant">Deskripsi</label>
            <textarea bind:value={form.desc} rows="2" class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none resize-none"></textarea>
          </div>
          {#if !isRoleplay}
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-on-surface-variant">Moral</label>
              <textarea bind:value={form.moral} rows="2" class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none resize-none"></textarea>
            </div>
          {/if}
          <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-on-surface-variant">Creator</label>
              <input type="text" bind:value={form.creator} class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none" />
            </div>
            <div class="space-y-1">
              <label class="text-[10px] font-bold text-on-surface-variant">Status</label>
              <select bind:value={form.status} class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none bg-white">
                <option value="approved">Approved</option><option value="pending">Pending</option><option value="review">Review</option><option value="rejected">Rejected</option>
              </select>
            </div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant">Ages</label>
            <div class="flex flex-wrap gap-1.5">
              {#each allAges as age}
                <button onclick={() => toggleAge(age)} class="w-8 h-8 rounded-lg text-xs font-bold border-2 transition-all"
                  style="border-color:{form.ages.includes(age)?'#176c33':'#B7D9BC'};background:{form.ages.includes(age)?'#E1F2E5':'white'};color:{form.ages.includes(age)?'#176c33':'#9CA3AF'}">{age}</button>
              {/each}
            </div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant">Skills</label>
            <div class="relative z-20">
              <MultiSelect
                options={allSkills.map(s => ({ value: s.key, label: `${s.emoji} ${s.title}` }))}
                bind:selected={form.selectedSkills}
                placeholder="Pilih skills..."
                maxSelect={10}
                --sms-border="2px solid #B7D9BC"
                --sms-border-radius="12px"
                --sms-focus-border="2px solid #176c33"
                --sms-min-height="42px"
                --sms-padding="8px 12px"
              />
            </div>
          </div>
          <div class="space-y-1">
            <label class="text-[10px] font-bold text-on-surface-variant">Cover Image</label>
            <label class="flex items-center gap-2 px-3 py-2 rounded-xl border-2 border-dashed border-[#B7D9BC] bg-canvas-cream cursor-pointer hover:border-primary transition-colors">
              <span class="text-sm">{coverFile ? '🖼️' : '📤'}</span>
              <span class="text-xs font-medium text-on-surface-variant truncate">{coverFile ? coverFile.name : 'Pilih gambar...'}</span>
              <input type="file" accept="image/*" class="hidden" onchange={(e) => coverFile = e.target.files[0] || null} />
            </label>
          </div>
        </div>

        {#if isRoleplay}
          <div class="bg-white rounded-2xl border-2 border-[#B7D9BC] p-4 space-y-3">
            <div class="flex items-center justify-between">
              <p class="text-xs font-bold text-primary uppercase tracking-wider">Roles</p>
              <button onclick={addRole} class="text-xs font-bold text-primary px-3 py-1 rounded-lg bg-success-soft">+ Tambah</button>
            </div>
            {#each form.roles as role, i}
              <div class="flex gap-2 items-start p-3 rounded-xl bg-canvas-cream border border-[#B7D9BC]/50">
                <input type="text" bind:value={role.emoji} placeholder="🎭" class="w-12 px-2 py-2 rounded-lg border-2 border-[#B7D9BC] text-center text-lg focus:border-primary outline-none" />
                <div class="flex-1 space-y-1">
                  <input type="text" bind:value={role.name} placeholder="Nama Peran" class="w-full px-3 py-1.5 rounded-lg border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none" />
                  <input type="text" bind:value={role.desc} placeholder="Deskripsi" class="w-full px-3 py-1.5 rounded-lg border-2 border-[#B7D9BC] text-xs focus:border-primary outline-none" />
                </div>
                <button onclick={() => removeRole(i)} class="text-error text-lg px-2">✕</button>
              </div>
            {/each}
          </div>
        {/if}
      </div>

      <div class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl border-2 border-[#B7D9BC] p-4 space-y-3">
          <div class="flex items-center justify-between">
            <p class="text-xs font-bold text-primary uppercase tracking-wider">{isQuestion?'Questions':'Pages'} ({form.pages.length})</p>
            <button onclick={addPage} class="text-xs font-bold text-primary px-3 py-1 rounded-lg bg-success-soft">+ Tambah</button>
          </div>
          {#each form.pages as page, pi}
            {@const pageImg = resolveActivityImage(type, item.slug || item.id, page.num + '.png')}
            <div class="p-3 rounded-xl bg-canvas-cream border border-[#B7D9BC]/50 space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-primary">#{page.num}</span>
                <button onclick={() => removePage(pi)} class="text-error text-sm font-bold px-2">✕</button>
              </div>
              <div class="flex flex-col lg:flex-row gap-3">
                {#if pageImg}
                  <div class="lg:w-48 shrink-0 rounded-lg overflow-hidden border-2 border-[#B7D9BC] self-start mx-auto lg:mx-0">
                    <img src={pageImg} alt="Page {page.num}" class="w-full h-auto object-contain max-h-48 lg:max-h-none"
                      onerror={(e) => { e.target.style.display = 'none' }} />
                  </div>
                {/if}
                <div class="flex-1 min-w-0 space-y-2">
              {#if isRoleplay}
                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-on-surface-variant">Narator</label>
                  <textarea bind:value={page.narrator} rows="2" class="w-full px-3 py-2 rounded-lg border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none resize-none"></textarea>
                </div>
                <div class="space-y-1">
                  <div class="flex items-center justify-between">
                    <label class="text-[10px] font-bold text-on-surface-variant">Dialog</label>
                    <button onclick={() => addDialog(pi)} class="text-[10px] font-bold text-primary">+ Dialog</button>
                  </div>
                  {#each page.dialog as d, di}
                    <div class="flex gap-2 items-center">
                      <input type="text" bind:value={d.role} placeholder="Role" class="w-24 px-2 py-1.5 rounded-lg border-2 border-[#B7D9BC] text-xs focus:border-primary outline-none" />
                      <input type="text" bind:value={d.text} placeholder="Dialog" class="flex-1 px-2 py-1.5 rounded-lg border-2 border-[#B7D9BC] text-xs focus:border-primary outline-none" />
                      <button onclick={() => removeDialog(pi, di)} class="text-error text-xs">✕</button>
                    </div>
                  {/each}
                </div>
              {:else if isQuestion}
                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-on-surface-variant">Pertanyaan</label>
                  <textarea bind:value={page.question} rows="2" class="w-full px-3 py-2 rounded-lg border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none resize-none"></textarea>
                </div>
                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-on-surface-variant">Jawaban</label>
                  <input type="text" bind:value={page.answer} class="w-full px-3 py-2 rounded-lg border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none" />
                </div>
              {:else}
                <div class="space-y-1">
                  <label class="text-[10px] font-bold text-on-surface-variant">Teks</label>
                  <textarea bind:value={page.text} rows="3" class="w-full px-3 py-2 rounded-lg border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none resize-none"></textarea>
                </div>
                {#if type === 'komik'}
                  <div class="space-y-1">
                    <label class="text-[10px] font-bold text-on-surface-variant">Dialog</label>
                    <input type="text" bind:value={page.dialogue} class="w-full px-3 py-2 rounded-lg border-2 border-[#B7D9BC] text-sm focus:border-primary outline-none" />
                  </div>
                {/if}
              {/if}
                </div>
              </div>
            </div>
          {/each}
        </div>
      </div>

    </div>
  </div>
</div>

<style>
  :global(.multiselect .options) {
    position: absolute !important;
    z-index: 50 !important;
  }
</style>
