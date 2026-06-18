<script>
  import { generateIdea, getAiProviders } from '../services/api.js'
  import { userRole } from '../stores/authStore.js'

  let userRoleVal = $state('')
  let open = $state(false)
  let generating = $state(false)
  let resultMsg = $state('')
  let resultItems = $state([])

  $effect(() => {
    const unsub = userRole.subscribe(v => userRoleVal = v)
    return unsub
  })

  const activityTypes = [
    { value: 'storytelling', emoji: '📖', label: 'Story Telling' },
    { value: 'bermain_peran', emoji: '🎭', label: 'Bermain Peran' },
    { value: 'permainan', emoji: '🎲', label: 'Permainan' },
    { value: 'monolog', emoji: '🎤', label: 'Monolog' },
    { value: 'proyek_kreatif', emoji: '🎨', label: 'Proyek Kreatif' },
    { value: 'musik_gerak', emoji: '🎵', label: 'Musik & Gerak' },
    { value: 'puzzle', emoji: '🧩', label: 'Puzzle' },
    { value: 'mindfulness', emoji: '🧘', label: 'Mindfulness' },
    { value: 'outdoor', emoji: '🌿', label: 'Outdoor' },
    { value: 'ilmu_pengetahuan', emoji: '🔬', label: 'Ilmu Pengetahuan' },
    { value: 'tebak_teakan', emoji: '🤔', label: 'Tebak-tebakan' },
    { value: 'permainan_tangan', emoji: '🤲', label: 'Permainan Tangan' },
    { value: 'latihan_otak', emoji: '🧠', label: 'Latihan Otak' },
    { value: 'komik', emoji: '💬', label: 'Komik' },
    { value: 'worksheet', emoji: '📝', label: 'Worksheet' },
    { value: 'coloring', emoji: '🖍️', label: 'Coloring' },
  ]

  const providers = [
    { value: '', label: 'Default' },
    { value: 'openai', label: 'OpenAI' },
    { value: 'minimax', label: 'MiniMax' },
    { value: 'deepseek', label: 'DeepSeek' },
    { value: 'groq', label: 'Groq' },
    { value: 'ollama', label: 'Ollama' },
  ]

  const agamaOptions = [
    { value: '', label: '-' },
    { value: 'islam', label: 'Islam' },
    { value: 'kristen', label: 'Kristen' },
    { value: 'katholik', label: 'Katholik' },
    { value: 'hindu', label: 'Hindu' },
    { value: 'budha', label: 'Budha' },
  ]

  const ageOptions = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11]

  const skillOptions = [
    { key: 'bersyukur', label: 'Bersyukur' },
    { key: 'jujur', label: 'Jujur' },
    { key: 'peduli_sesama', label: 'Peduli Sesama' },
    { key: 'berani_bicara', label: 'Berani Bicara' },
    { key: 'berani_mencoba', label: 'Berani Mencoba' },
    { key: 'fokus', label: 'Fokus' },
    { key: 'kerja_sama', label: 'Kerja Sama' },
    { key: 'berpikir_kreatif', label: 'Berpikir Kreatif' },
    { key: 'memecahkan_masalah', label: 'Memecahkan Masalah' },
    { key: 'mengelola_marah', label: 'Mengelola Marah' },
    { key: 'empati', label: 'Empati' },
    { key: 'kemandirian', label: 'Kemandirian' },
  ]

  let form = $state({
    type: 'storytelling',
    theme: '',
    count: 20,
    provider: '',
    ages: [3, 4, 5, 6, 7, 8],
    skills: [],
    agama: '',
  })

  function toggleAge(age) {
    if (form.ages.includes(age)) {
      form.ages = form.ages.filter(a => a !== age)
    } else {
      form.ages = [...form.ages, age].sort()
    }
  }

  function toggleSkill(key) {
    if (form.skills.includes(key)) {
      form.skills = form.skills.filter(s => s !== key)
    } else {
      form.skills = [...form.skills, key]
    }
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
        skills: form.skills,
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

{#if userRoleVal === 'developer'}
  <div class="bg-white rounded-[24px] border-4 border-[#B7D9BC] shadow-md overflow-hidden">
    <button onclick={() => open = !open}
      class="w-full p-4 flex items-center gap-3 text-left hover:bg-success-soft/30 transition-colors">
      <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
        <span class="text-primary">✨</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-bold text-sm text-text-main">Generate Idea (AI)</p>
        <p class="text-xs text-on-surface-variant">Buat ide aktivitas dengan AI</p>
      </div>
      <span class="text-on-surface-variant transition-transform" class:rotate-180={open}>
        ▼
      </span>
    </button>

    {#if open}
      <div class="px-4 pb-4 space-y-3 border-t-2 border-[#B7D9BC]/50">

        <div class="pt-3">
          <label class="text-xs font-bold text-on-surface-variant mb-1 block">Tema</label>
          <textarea bind:value={form.theme}
            rows="3"
            placeholder="contoh: tema laut, ikan, kehidupan bawah laut, palung mariana"
            class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-sm resize-none"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1 block">Type</label>
            <select bind:value={form.type}
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
              {#each activityTypes as t}
                <option value={t.value}>{t.emoji} {t.label}</option>
              {/each}
            </select>
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1 block">Jumlah</label>
            <input type="number" bind:value={form.count} min="1" max="50"
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1 block">AI Provider</label>
            <select bind:value={form.provider}
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
              {#each providers as p}
                <option value={p.value}>{p.label}</option>
              {/each}
            </select>
          </div>
          <div>
            <label class="text-xs font-bold text-on-surface-variant mb-1 block">Agama</label>
            <select bind:value={form.agama}
              class="w-full px-3 py-2 rounded-xl border-2 border-[#B7D9BC] focus:border-primary outline-none text-sm bg-white">
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
                class="px-2.5 py-1 rounded-lg text-xs font-bold border-2 transition-all"
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
          <div class="flex flex-wrap gap-1.5">
            {#each skillOptions as skill}
              <button onclick={() => toggleSkill(skill.key)}
                class="px-2.5 py-1 rounded-lg text-xs font-bold border-2 transition-all"
                style="background: {form.skills.includes(skill.key) ? '#E3F2FD' : 'white'};
                       border-color: {form.skills.includes(skill.key) ? '#0D47A1' : '#B7D9BC'};
                       color: {form.skills.includes(skill.key) ? '#0D47A1' : '#666'}">
                {skill.label}
              </button>
            {/each}
          </div>
        </div>

        <button onclick={handleGenerate} disabled={generating}
          class="w-full py-2.5 rounded-xl text-white text-sm font-bold disabled:opacity-50 flex items-center justify-center gap-2"
          style="background: #176C33; box-shadow: 0 4px 0 #0d4a22;">
          {#if generating}
            <span class="text-lg animate-spin">⏳</span>
            Generating...
          {:else}
            <span class="text-lg">✨</span>
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

        {#if resultItems.length > 0}
          <div class="space-y-2 max-h-60 overflow-y-auto">
            {#each resultItems as item, i}
              <div class="bg-canvas-cream rounded-xl p-3 border-2 border-[#B7D9BC]/50">
                <div class="flex items-start gap-2">
                  <span class="text-xs font-bold text-primary shrink-0">{i + 1}.</span>
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-text-main">{item.name}</p>
                    <p class="text-[11px] text-on-surface-variant mt-0.5">{item.desc}</p>
                    {#if item.moral}
                      <p class="text-[10px] text-primary mt-1 italic">💬 {item.moral}</p>
                    {/if}
                  </div>
                </div>
              </div>
            {/each}
          </div>
        {/if}

      </div>
    {/if}
  </div>
{/if}
