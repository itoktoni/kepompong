<script>
  import { onMount } from 'svelte'
  import MultiSelect from 'svelte-multiselect'
  import { generateIdea, getAiProviders, getActivityTypeOptions, getSkillsList, getActivitiesList } from '../services/api.js'
  import { userRole } from '../stores/authStore.js'

  let userRoleVal = $state('')
  let generating = $state(false)
  let resultMsg = $state('')
  let resultItems = $state([])

  let activityTypes = $state([])
  let providers = $state([{ value: '', label: 'Default' }])
  let skills = $state([])
  let activities = $state({})

  let loading = $state(true)

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })

  onMount(async () => {
    const [typesRes, provRes, skillsRes, actRes] = await Promise.all([
      getActivityTypeOptions().catch(() => []),
      getAiProviders().catch(() => ({})),
      getSkillsList().catch(() => []),
      getActivitiesList().catch(() => ({})),
    ])

    if (Array.isArray(typesRes)) {
      activityTypes = typesRes
    }

    if (provRes && typeof provRes === 'object') {
      providers = [
        { value: '', label: 'Default' },
        ...Object.keys(provRes).map(k => ({ value: k, label: k.charAt(0).toUpperCase() + k.slice(1) })),
      ]
    }

    if (Array.isArray(skillsRes)) {
      skills = skillsRes
    }

    if (skillsRes && typeof skillsRes === 'object' && !Array.isArray(skillsRes)) {
      skills = Object.values(skillsRes).flat()
    }

    if (actRes && typeof actRes === 'object') {
      activities = actRes
    }

    loading = false
  })

  const agamaOptions = [
    { value: '', label: '-' },
    { value: 'islam', label: 'Islam' },
    { value: 'kristen', label: 'Kristen' },
    { value: 'katholik', label: 'Katholik' },
    { value: 'hindu', label: 'Hindu' },
    { value: 'budha', label: 'Budha' },
    { value: 'konghucu', label: 'Konghucu' },
  ]

  const ageOptions = [3, 4, 5, 6, 7, 8, 9]

  let form = $state({
    type: 'storytelling',
    theme: '',
    count: 1,
    provider: '',
    ages: [3, 4, 5, 6, 7, 8, 9],
    selectedSkills: [],
    agama: '',
  })

  function toggleAge(age) {
    form.ages = form.ages.includes(age)
      ? form.ages.filter(a => a !== age)
      : [...form.ages, age].sort()
  }

  async function handleGenerate() {
    if (!form.theme.trim()) {
      resultMsg = 'Isi tema terlebih dahulu'
      return
    }
    generating = true
    resultMsg = ''
    resultItems = []
    try {
      const res = await generateIdea({
        type: form.type,
        theme: form.theme,
        count: form.count,
        provider: form.provider || undefined,
        ages: form.ages,
        skills: form.selectedSkills.map(s => s.value),
        agama: form.agama || undefined,
      })
      if (res && res.items) {
        resultItems = res.items
        resultMsg = `${res.items.length} ide berhasil dibuat!`
      } else {
        resultMsg = res?.message || 'Berhasil!'
      }
    } catch (e) {
      resultMsg = 'Gagal: ' + (e.message || 'Error')
    }
    generating = false
  }
</script>

{#if userRoleVal !== 'developer'}
  <div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
    <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
      <div class="text-5xl mb-3">🔒</div>
      <p class="font-label-lg text-text-main mb-1">Akses Terbatas</p>
      <p class="text-sm text-on-surface-variant">Halaman ini hanya untuk developer.</p>
    </div>
  </div>
{:else}
  <div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
    <section class="mb-stack-lg">
      <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main leading-tight mb-2 flex items-center gap-2">
        <span class="w-10 h-10 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-xl">✨</span> Generate Idea
      </h2>
      <p class="font-body-md text-body-md text-on-surface-variant mb-4">
        Buat ide aktivitas dengan AI dan simpan ke database.
      </p>
    </section>

    {#if loading}
      <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-[#B7D9BC]">
        <div class="text-3xl mb-2 animate-spin">⏳</div>
        <p class="text-sm text-on-surface-variant">Memuat data...</p>
      </div>
    {:else}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <div class="space-y-4">
          <div class="bg-white rounded-[24px] border-4 border-[#B7D9BC] p-5 shadow-md space-y-4">
            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Tema</label>
              <textarea bind:value={form.theme}
                rows="4"
                placeholder="contoh: tema laut, ikan, kehidupan bawah laut, palung mariana"
                class="w-full px-4 py-3 rounded-2xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-sm resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Type</label>
                <select bind:value={form.type}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
                  {#each activityTypes as t}
                    <option value={t.value}>{t.emoji} {t.label}</option>
                  {/each}
                </select>
              </div>
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Jumlah</label>
                <input type="number" bind:value={form.count} min="1" max="50"
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">AI Provider</label>
                <select bind:value={form.provider}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
                  {#each providers as p}
                    <option value={p.value}>{p.label}</option>
                  {/each}
                </select>
              </div>
              <div>
                <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Agama</label>
                <select bind:value={form.agama}
                  class="w-full px-3 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
                  {#each agamaOptions as a}
                    <option value={a.value}>{a.label}</option>
                  {/each}
                </select>
              </div>
            </div>

            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Ages</label>
              <div class="flex flex-wrap gap-1.5">
                {#each ageOptions as age}
                  <button onclick={() => toggleAge(age)}
                    class="px-3 py-1.5 rounded-xl text-xs font-bold border-2 transition-all"
                    style="background: {form.ages.includes(age) ? '#E1F2E5' : 'white'};
                           border-color: {form.ages.includes(age) ? '#176c33' : '#B7D9BC'};
                           color: {form.ages.includes(age) ? '#176c33' : '#666'}">
                    {age}
                  </button>
                {/each}
              </div>
            </div>

            <div>
              <label class="text-xs font-bold text-on-surface-variant mb-1.5 block">Skills</label>
              <MultiSelect
                options={skills.map(s => ({ value: s.key, label: `${s.label} (${s.pilar})` }))}
                bind:selected={form.selectedSkills}
                placeholder="Pilih skills..."
                maxSelect={10}
                --sms-border="2px solid #B7D9BC"
                --sms-border-radius="12px"
                --sms-focus-border="2px solid #176c33"
                --sms-min-height="42px"
                --sms-text-sm="0.875rem"
                --sms-padding="8px 12px"
                --sms-selected-li-padding="4px 8px"
                --sms-gap="4px"
              />
              {#if form.selectedSkills.length > 0}
                <p class="text-[10px] text-on-surface-variant/60 mt-1.5">{form.selectedSkills.length} skill dipilih</p>
              {/if}
            </div>

            <button onclick={handleGenerate} disabled={generating}
              class="w-full py-3 rounded-2xl text-white text-sm font-bold disabled:opacity-50 flex items-center justify-center gap-2"
              style="background: #176C33; box-shadow: 0 6px 0 #0d4a22;">
              {#if generating}
                <span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                Generating...
              {:else}
                <span class="material-symbols-outlined text-lg">auto_awesome</span>
                Generate Idea
              {/if}
            </button>

            {#if resultMsg}
              <div class="rounded-xl p-3 text-xs font-bold"
                class:bg-success-soft={resultMsg.includes('berhasil')}
                class:text-primary={resultMsg.includes('berhasil')}
                class:bg-error-soft={resultMsg.includes('Gagal')}
                class:text-error={resultMsg.includes('Gagal')}>
                {resultMsg}
              </div>
            {/if}
          </div>
        </div>

        <div class="space-y-3">
          {#if resultItems.length > 0}
            <div class="bg-white rounded-[24px] border-4 border-[#B7D9BC] p-5 shadow-md">
              <div class="flex items-center gap-2 mb-3">
                <span class="w-8 h-8 rounded-full bg-success-soft border-2 border-[#B7D9BC] flex items-center justify-center text-base">📋</span>
                <p class="text-sm font-bold text-primary">{resultItems.length} Ide Aktivitas</p>
              </div>
              <div class="space-y-2.5 max-h-[60vh] overflow-y-auto">
                {#each resultItems as item, i}
                  <div class="bg-canvas-cream rounded-2xl p-3.5 border-2 border-[#B7D9BC]/50">
                    <div class="flex items-start gap-2.5">
                      <span class="text-xs font-bold text-white bg-primary w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5">{i + 1}</span>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-text-main">{item.name}</p>
                        <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">{item.desc}</p>
                        {#if item.moral}
                          <p class="text-[11px] text-primary mt-1.5 italic">💬 {item.moral}</p>
                        {/if}
                      </div>
                    </div>
                  </div>
                {/each}
              </div>
            </div>
          {:else}
            <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
              <div class="text-5xl mb-3">💡</div>
              <p class="font-label-lg text-text-main mb-1">Belum Ada Ide</p>
              <p class="text-sm text-on-surface-variant">Isi tema dan klik Generate untuk membuat ide aktivitas.</p>
            </div>
          {/if}
        </div>

      </div>
    {/if}
  </div>
{/if}
