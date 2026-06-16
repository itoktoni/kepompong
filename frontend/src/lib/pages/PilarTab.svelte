<script>
  import { pilars, filterPilars } from '../data/pilars.js'
  import { getSkillsByPilar } from '../data/skills.js'
  import { calcAge } from '../utils/age.js'
  import { anakList, addSkill, addActivity } from '../stores/anakStore.js'
  import * as authStore from '../stores/authStore.js'
  import { selectedAnakId, selectedPilar, selectedSkillKey, selectedAge, selectedAgama, selectedPlanId, openPilarSub, closePilarSub, activeTab, switchCounter, switchTab } from '../stores/appStore.js'
  import { userRole, userPlan, plans as planList } from '../stores/authStore.js'
  import AnakDropdown from '../components/AnakDropdown.svelte'

  let anakListVal = $state([])
  let selectedAnakIdVal = $state(null)
  let selectedPilarVal = $state(null)
  let userPlanVal = $state(null)
  let planListVal = $state([])
  let selectedAgeVal = $state(null)
  let selectedAgamaVal = $state(null)
  let selectedPlanIdVal = $state(null)
  let searchQuery = $state('')
  let userRoleVal = $state('')

  $effect(() => {
    const u1 = anakList.subscribe(v => anakListVal = v)
    const u2 = selectedAnakId.subscribe(v => selectedAnakIdVal = v)
    const u3 = selectedPilar.subscribe(v => selectedPilarVal = v)
    const u4 = userPlan.subscribe(v => userPlanVal = v)
    const u5 = selectedAge.subscribe(v => selectedAgeVal = v)
    const u6 = selectedAgama.subscribe(v => selectedAgamaVal = v)
    const u7 = selectedPlanId.subscribe(v => selectedPlanIdVal = v)
    const u8 = planList.subscribe(v => planListVal = v)
    const u9 = userRole.subscribe(v => userRoleVal = v)
    return () => { u1(); u2(); u3(); u4(); u5(); u6(); u7(); u8(); u9() }
  })

  $effect(() => {
    if (anakListVal.length && !selectedChild) {
      selectedAnakId.set(anakListVal[0].id)
    }
  })

  const selectedChild = $derived(anakListVal.find(a => a.id === selectedAnakIdVal))
  const childAge = $derived(selectedChild ? calcAge(selectedChild.tahun, selectedChild.bulan, selectedChild.tanggal) : null)
  const childAgama = $derived(selectedChild?.agama || null)
  const planId = $derived(userPlanVal?.plan_id || null)
  const planName = $derived(() => {
    if (!selectedPlanIdVal) return null
    const found = planListVal.find(p => p.id === selectedPlanIdVal)
    return found?.name || null
  })
  const filteredPilars = $derived(filterPilars(childAge, childAgama, planId).filter(p => {
    if (!searchQuery) return true
    const q = searchQuery.toLowerCase()
    return p.title.toLowerCase().includes(q) || (p.subtitle && p.subtitle.toLowerCase().includes(q))
  }))

  function getSubData(key) {
    const pilar = pilars.find(p => p.key === key)
    if (!pilar) return null
    const items = getSkillsByPilar(key, childAge, childAgama, planId)
    return { title: pilar.title, desc: 'Pilih fokus karakter untuk aktivitas bersama si kecil.', color: pilar.color, bg: pilar.bg, items }
  }

  function togglePilar(key) {
    if (selectedPilarVal === key) {
      closePilarSub()
    } else {
      openPilarSub(key)
    }
  }

  async function openAktivitas(item, pilarKey) {
    const skillKey = item.key || item.title.toLowerCase().replace(/\s+/g, '_')
    if (selectedAnakIdVal) {
      addSkill(selectedAnakIdVal, {
        key: skillKey, emoji: item.emoji, title: item.title, pilar: pilarKey,
        color: getSubData(pilarKey).color
      })
    }

    selectedSkillKey.set(skillKey)
    activeTab.set('activity')
    switchCounter.update(n => n + 1)
    if (typeof window !== 'undefined') window.scrollTo(0, 0)
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  <section class="mb-4">
    <h2 class="font-headline-lg-mobile text-headline-lg-mobile text-text-main leading-tight mb-2">
      Mau Belajar Apa Hari Ini?
    </h2>
    <p class="font-body-md text-body-md text-on-surface-variant mb-3">Pilih area yang ingin dikembangkan bersama si kecil.</p>
    {#if anakListVal.length}
      <AnakDropdown anakList={anakListVal} value={selectedAnakIdVal} onselect={(id) => selectedAnakId.set(id)} />
    {/if}
    <div class="relative mt-3">
      <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">search</span>
      <input
        type="text"
        placeholder="Cari pilar..."
        bind:value={searchQuery}
        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-sm"
      />
    </div>
    {#if selectedAnakIdVal && (selectedAgeVal != null || selectedAgamaVal || selectedPlanIdVal)}
      <div class="mt-3">
        <div class="flex items-center justify-between mb-2">
          <p class="text-xs font-bold text-primary uppercase tracking-wider">Filter Aktif</p>
          {#if userRoleVal === 'developer'}
            <button onclick={() => { selectedAge.set(null); selectedAgama.set(null); selectedPlanId.set(null) }}
              class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold text-error hover:bg-error/10 transition-colors">
              <span class="material-symbols-outlined text-sm">close</span>
              Hapus Semua
            </button>
          {/if}
        </div>
        <div class="bg-white rounded-2xl p-3 border-2 border-[#B7D9BC] flex flex-wrap gap-2">
          {#if selectedAgeVal != null}
            <button onclick={() => userRoleVal === 'developer' && selectedAge.set(null)}
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 {userRoleVal === 'developer' ? 'hover:bg-primary/10 cursor-pointer' : 'cursor-default'}">
              <span class="material-symbols-outlined text-sm">cake</span>
              Umur {selectedAgeVal} th
              {#if userRoleVal === 'developer'}
                <span class="material-symbols-outlined text-sm text-primary/60">close</span>
              {/if}
            </button>
          {/if}
          {#if selectedAgamaVal}
            <button onclick={() => userRoleVal === 'developer' && selectedAgama.set(null)}
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 {userRoleVal === 'developer' ? 'hover:bg-primary/10 cursor-pointer' : 'cursor-default'}">
              <span class="material-symbols-outlined text-sm">diversity_3</span>
              {selectedAgamaVal}
              {#if userRoleVal === 'developer'}
                <span class="material-symbols-outlined text-sm text-primary/60">close</span>
              {/if}
            </button>
          {/if}
          {#if selectedPlanIdVal}
            <button onclick={() => userRoleVal === 'developer' && selectedPlanId.set(null)}
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-success-soft text-primary text-xs font-bold border border-[#B7D9BC]/50 {userRoleVal === 'developer' ? 'hover:bg-primary/10 cursor-pointer' : 'cursor-default'}">
              <span class="material-symbols-outlined text-sm">workspace_premium</span>
              {planName() || 'Plan'}
              {#if userRoleVal === 'developer'}
                <span class="material-symbols-outlined text-sm text-primary/60">close</span>
              {/if}
            </button>
          {/if}
        </div>
      </div>
    {/if}
  </section>

  {#if !anakListVal.length}
    <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
      <div class="text-5xl mb-4">👶</div>
      <h3 class="font-headline-sm text-text-main mb-2">Belum Ada Data Anak</h3>
      <p class="text-sm text-on-surface-variant mb-5">Tambahkan data anak terlebih dahulu sebelum memilih aktivitas.</p>
      <button onclick={() => switchTab('profile')}
        class="px-6 py-3 rounded-2xl text-white text-sm font-bold btn-pop-green">
        + Tambah Anak
      </button>
    </div>
  {:else if !selectedAnakIdVal}
    <div class="bg-canvas-cream rounded-[32px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
      <div class="text-5xl mb-4">👆</div>
      <h3 class="font-headline-sm text-text-main mb-2">Pilih Anak Terlebih Dahulu</h3>
      <p class="text-sm text-on-surface-variant">Gunakan dropdown di atas untuk memilih anak yang ingin dikembangkan.</p>
    </div>
  {:else}
    {#if !selectedChild?.tahun}
      <div class="bg-canvas-cream rounded-[24px] p-4 mb-3 border-2 border-[#B7D9BC] flex items-center gap-3">
        <span class="text-2xl">📅</span>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-bold text-text-main">Tanggal lahir belum diisi</p>
          <p class="text-xs text-on-surface-variant">Aktivitas akan difilter berdasarkan usia setelah tanggal lahir diisi.</p>
        </div>
        <button onclick={() => switchTab('profile')}
          class="px-3 py-1.5 rounded-xl text-xs font-bold text-white btn-pop-green-sm shrink-0">
          Isi
        </button>
      </div>
    {/if}
    {#each filteredPilars as pilar (pilar.key)}
      <button
        class="bento-card group relative bg-canvas-cream rounded-[24px] overflow-hidden cursor-pointer transition-all hover:shadow-lg border-4 mb-3 w-full text-left"
        style="border-color: {selectedPilarVal === pilar.key ? pilar.color : '#B7D9BC'}; box-shadow: {selectedPilarVal === pilar.key ? `0 6px 24px ${pilar.color}30` : `0 2px 12px ${pilar.color}10`}"
        onclick={() => togglePilar(pilar.key)}>
        <div class="absolute left-0 top-0 bottom-0 w-1 rounded-l-[24px]" style="background: {pilar.color}"></div>
        <div class="absolute -bottom-6 -right-6 w-24 h-24 rounded-full opacity-[0.07]" style="background: {pilar.color}"></div>
        <div class="flex items-center gap-4 p-4 md:p-5 pl-5">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 border-2 border-white shadow-sm"
            style="background: {pilar.bg}; color: {pilar.color}">
            {pilar.emoji}
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="font-label-lg text-label-lg text-text-main">{pilar.title}</h3>
            <p class="text-xs text-on-surface-variant mt-0.5">{pilar.subtitle}</p>
          </div>
          <span class="material-symbols-outlined text-primary/50 group-hover:text-primary transition-colors text-lg shrink-0"
            class:rotate-180={selectedPilarVal === pilar.key}>expand_more</span>
        </div>
      </button>

      {#if selectedPilarVal === pilar.key && getSubData(pilar.key)}
        {@const sub = getSubData(pilar.key)}
        <div class="mb-3 fade-in-up">
          <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-headline-sm text-text-main">{sub.title}</h3>
              <button onclick={(e) => { e.stopPropagation(); closePilarSub() }}
                class="flex items-center gap-1.5 text-sm font-bold text-on-surface-variant hover:text-primary transition-colors bg-white px-3 py-1.5 rounded-full border-2 border-[#B7D9BC]">
                <span class="material-symbols-outlined text-lg">close</span>
                Tutup
              </button>
            </div>
            <p class="text-sm text-on-surface-variant mb-4">{sub.desc}</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              {#each sub.items as item (item.title)}
                <button
                  class="group bg-white p-4 rounded-[20px] shadow-sm flex items-center gap-3 transition-all hover:shadow-md cursor-pointer border-2 border-[#B7D9BC] w-full text-left"
                  onclick={(e) => { e.stopPropagation(); openAktivitas(item, pilar.key) }}>
                  <div class="w-11 h-11 rounded-xl flex items-center justify-center text-2xl shrink-0"
                    style="background: {sub.bg}">{item.emoji}</div>
                  <div class="flex-1 min-w-0">
                    <h3 class="font-label-lg text-label-lg text-primary leading-snug">{item.title}</h3>
                    <p class="text-xs leading-snug text-on-surface-variant mt-0.5 line-clamp-2">{item.desc}</p>
                  </div>
                  <span class="material-symbols-outlined text-primary/50 group-hover:text-primary transition-colors text-lg shrink-0">chevron_right</span>
                </button>
              {/each}
            </div>
          </div>
        </div>
      {/if}
    {/each}
  {/if}

  <div class="mt-stack-lg bg-success-soft rounded-[32px] p-6 md:p-8 relative overflow-hidden border-4 border-[#B7D9BC] shadow-md">
    <div class="relative z-10 flex gap-4">
      <div class="shrink-0">
        <div class="w-12 h-12 bg-white/80 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
          <span class="w-10 h-10 rounded-full bg-white border-2 border-[#B7D9BC] flex items-center justify-center text-lg">💡</span>
        </div>
      </div>
      <div>
        <h4 class="font-label-lg text-label-lg text-primary mb-1 uppercase tracking-wider">Tips Hari Ini</h4>
        <p class="font-body-md text-body-lg text-on-surface-variant italic leading-relaxed">"Fokus pada satu hal kecil hari ini memberikan dampak besar di masa depan."</p>
      </div>
    </div>
    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary/5 rounded-full blur-3xl"></div>
  </div>
</div>

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
  .fade-in-up {
    animation: fadeInUp 0.3s ease-out;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
