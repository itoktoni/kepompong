<script>
  import { get } from 'svelte/store'
  import { onMount } from 'svelte'
  import { user, isAuthenticated } from '../stores/authStore.js'
  import { applyServerData } from '../stores/authStore.js'
  import * as api from '../services/api.js'

  let userVal = $state(null)
  let isAuth = $state(false)

  let activeTab = $state('affiliate')
  let copied = $state(false)
  let referrals = $state([])
  let earnings = $state({ total: 0, register: 0, upgrade: 0, pending: 0 })
  let rates = $state({ register_bonus: 500, commission_rate: 15 })
  let cashoutConfig = $state({ minimum: 50000, admin_rate: 3 })
  let banks = $state([])
  let cashouts = $state([])

  let editingData = $state(false)
  let editCodeValue = $state('')
  let editDataError = $state('')
  let savingData = $state(false)
  let rekeningForm = $state({ rekening_nama: '', rekening_bank: '', rekening_nomor: '' })

  let showCashout = $state(false)
  let cashoutAmount = $state(null)
  let cashoutError = $state('')
  let cashingOut = $state(false)

  let myDiscounts = $state([])
  let discountConfig = $state({ max_discounts: 3, max_value: 15, max_nominal: 10000 })
  let discountForm = $state({ discount_code: '', discount_nama: '', discount_value: null })
  let discountError = $state('')
  let discountSaving = $state(false)
  let copiedId = $state(null)

  let bankOptions = $state([])
  let loadingBankOptions = $state(false)

  async function loadBankOptions() {
    loadingBankOptions = true
    try {
      const res = await api.getPaymentMethodList()
      // Transform payment_methods to grouped options format
      const methods = res.payment_methods || []
      const grouped = {}

      for (const m of methods) {
        const group = m.group || 'Bank Transfer'
        if (!grouped[group]) grouped[group] = []
        grouped[group].push({ name: m.nama })
      }

      bankOptions = Object.entries(grouped).map(([group, items]) => ({ group, items }))
    } catch (e) {
      console.error('Failed to load bank options:', e)
    }
    loadingBankOptions = false
  }

  $effect(() => {
    const u1 = user.subscribe(v => userVal = v)
    const u2 = isAuthenticated.subscribe(v => isAuth = v)
    return () => { u1(); u2() }
  })

  const appUrl = typeof window !== 'undefined' ? window.location.origin : 'https://jejakTumbuh.itoktoni.com'
  const referralLink = $derived(
    userVal?.affiliate_code ? `${appUrl}?ref=${userVal.affiliate_code}` : ''
  )

  const bonusRegister = $derived(referrals.length * rates.register_bonus)
  const totalKomisi = $derived(userVal?.komisi ?? 0)
  const totalEarned = $derived(earnings.upgrade + bonusRegister)
  const totalCashedOut = $derived(cashouts.filter(c => c.cashout_status === 'completed').reduce((sum, c) => sum + c.cashout_jumlah, 0))
  const totalPendingCashout = $derived(cashouts.filter(c => c.cashout_status === 'pending').reduce((sum, c) => sum + c.cashout_jumlah, 0))
  const saldoTersedia = $derived(userVal?.komisi ?? 0)
  const maxCashout = $derived(() => {
    const saldo = saldoTersedia
    const rate = cashoutConfig.admin_rate / 100
    return Math.floor(saldo / (1 + rate) / 1000) * 1000
  })
  const step = $derived(() => {
    const saldo = saldoTersedia
    if (saldo >= 100000) return 5000
    if (saldo >= 50000) return 2000
    return 1000
  })
  const hasRekening = $derived(userVal?.rekening_nama && userVal?.rekening_bank && userVal?.rekening_nomor)
  const adminFee = $derived(Math.round((cashoutAmount || 0) * cashoutConfig.admin_rate / 100))
  const received = $derived(cashoutAmount || 0)

  function formatDate(iso) {
    if (!iso) return '-'
    return new Date(iso).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
  }

  function formatRp(n) {
    return n ? `Rp${Number(n).toLocaleString('id-ID')}` : 'Rp0'
  }

  async function copyLink() {
    if (!referralLink) return
    try {
      await navigator.clipboard.writeText(referralLink)
      copied = true
      setTimeout(() => copied = false, 2000)
    } catch { /* fallback */ }
  }

  function shareLink() {
    if (navigator.share && referralLink) {
      navigator.share({ title: 'Jejak Tumbuh', text: 'Yuk coba Jejak Tumbuh - Pendamping Anak!', url: referralLink })
    }
  }

  function startEditData() {
    editCodeValue = userVal?.affiliate_code || ''
    rekeningForm = {
      rekening_nama: userVal?.rekening_nama || '',
      rekening_bank: userVal?.rekening_bank || '',
      rekening_nomor: userVal?.rekening_nomor || '',
    }
    editDataError = ''
    editingData = true
  }

  async function saveData() {
    editDataError = ''
    savingData = true
    try {
      const code = editCodeValue.trim().toUpperCase().replace(/[^A-Z0-9]/g, '')
      if (code && code.length >= 4) {
        const codeRes = await api.updateAffiliateCode(code)
        if (codeRes.user) user.set(codeRes.user)
      }
      const rekRes = await api.updateRekening({ ...rekeningForm })
      if (rekRes.user) user.set(rekRes.user)
      const me = await api.getMe()
      applyServerData(me)
      editingData = false
    } catch (e) {
      editDataError = e.message || 'Gagal menyimpan data'
    }
    savingData = false
  }

  function openCashout() {
    cashoutAmount = maxCashout()
    cashoutError = ''
    showCashout = true
  }

  async function submitCashout() {
    cashoutError = ''
    if (!cashoutAmount || cashoutAmount < cashoutConfig.minimum) {
      cashoutError = `Minimal pencairan Rp${cashoutConfig.minimum.toLocaleString('id-ID')}`
      return
    }
    if (cashoutAmount + adminFee > saldoTersedia) {
      cashoutError = 'Saldo komisi tidak mencukupi (termasuk platform fee)'
      return
    }
    cashingOut = true
    try {
      const res = await api.requestCashout(cashoutAmount)
      if (res.komisi !== undefined && userVal) {
        user.update(u => u ? { ...u, komisi: res.komisi } : u)
      }
      if (res.cashout) cashouts.unshift(res.cashout)
      showCashout = false
    } catch (e) {
      cashoutError = e.message || 'Gagal memproses pencairan'
    }
    cashingOut = false
  }

  async function submitDiscount() {
    discountError = ''
    const code = discountForm.discount_code.trim().toUpperCase().replace(/[^A-Z0-9_-]/g, '')
    if (!code || code.length < 4) {
      discountError = 'Kode promo minimal 4 karakter'
      return
    }
    if (!discountForm.discount_nama.trim()) {
      discountError = 'Nama diskon wajib diisi'
      return
    }
    if (!discountForm.discount_value || discountForm.discount_value < 1) {
      discountError = 'Nilai diskon minimal 1'
      return
    }
    if (discountForm.discount_value > discountConfig.max_value) {
      discountError = `Maksimal ${discountConfig.max_value}%`
      return
    }
    discountSaving = true
    try {
      const res = await api.createDiscount({
        discount_code: code,
        discount_nama: discountForm.discount_nama.trim(),
        discount_type: 'percentage',
        discount_value: discountForm.discount_value,
      })
      if (res.discount) myDiscounts.unshift(res.discount)
      discountForm = { discount_code: '', discount_nama: '', discount_value: null }
    } catch (e) {
      discountError = e.errors ? Object.values(e.errors).flat()[0] : (e.message || 'Gagal membuat diskon')
    }
    discountSaving = false
  }

  async function removeDiscount(id) {
    try {
      await api.deleteDiscount(id)
      myDiscounts = myDiscounts.filter(d => d.id !== id)
    } catch (e) { /* ignore */ }
  }

  async function copyCode(d) {
    try {
      await navigator.clipboard.writeText(d.code)
      copiedId = d.id
      setTimeout(() => copiedId = null, 2000)
    } catch { /* fallback */ }
  }

  async function loadReferrals() {
    try {
      const res = await api.getReferrals()
      referrals = res.referrals || []
      if (res.earnings) earnings = res.earnings
      if (res.rates) rates = res.rates
      if (res.cashout) cashoutConfig = res.cashout
      if (res.banks) banks = res.banks
      if (res.komisi !== undefined && userVal) {
        user.update(u => u ? { ...u, komisi: res.komisi } : u)
      }
    } catch (e) { /* ignore */ }
  }

  async function loadPaymentMethods() {
    try {
      const res = await api.getPaymentMethodCategories()
      if (res.grouped && res.grouped.length) {
        bankOptions = res.grouped
      }
    } catch (e) { /* ignore */ }
  }

  async function loadCashouts() {
    try {
      const res = await api.getCashouts()
      cashouts = res.cashouts || []
    } catch (e) { /* ignore */ }
  }

  async function loadDiscounts() {
    try {
      const res = await api.getMyDiscounts()
      myDiscounts = res.discounts || []
      if (res.config) discountConfig = res.config
    } catch (e) { /* ignore */ }
  }

  async function refreshUser() {
    try {
      const me = await api.getMe()
      applyServerData(me)
    } catch (e) { /* ignore */ }
  }

  onMount(() => {
    refreshUser()
    loadReferrals()
    loadCashouts()
    loadDiscounts()
    loadBankOptions()
  })
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">

  <!-- Hero Card -->
  <div class="bg-canvas-cream rounded-[32px] p-6 border-4 border-primary shadow-lg mb-5">
    <div class="flex items-start gap-3 mb-5">
      <div class="w-12 h-12 rounded-full bg-success-soft flex items-center justify-center border-2 border-white shadow-sm shrink-0">
        <span class="text-2xl text-primary">👥</span>
      </div>
      <div class="flex-1 min-w-0">
        <p class="font-bold text-lg text-text-main">Ajak Teman</p>
        <p class="text-xs text-on-surface-variant mt-0.5">Bagikan link referral dan dapatkan manfaat bersama</p>
      </div>
    </div>

    <div class="bg-white rounded-xl p-4 border-2 border-primary mb-3">
      <p class="text-[11px] text-on-surface-variant uppercase tracking-wider font-bold mb-1">Kode Referral Kamu</p>
      <p class="text-2xl font-bold text-primary tracking-widest">{userVal?.affiliate_code || '-'}</p>
    </div>

    <div class="flex items-stretch bg-white rounded-xl border-2 border-primary overflow-hidden mb-3">
      <div class="flex-1 px-3 py-3 min-w-0 flex flex-col justify-center">
        <p class="text-[11px] text-on-surface-variant mb-0.5">Link Referral</p>
        <p class="text-sm font-bold text-primary truncate">{referralLink || '-'}</p>
      </div>
      <button onclick={copyLink}
        class="px-4 bg-primary hover:bg-primary/90 transition-colors text-white font-bold text-sm flex items-center gap-1.5 border-l-2 border-primary">
        <span class="text-base">{copied ? '✓' : '📋'}</span>
        {copied ? 'Tersalin!' : 'Salin'}
      </button>
    </div>

    <div class="flex gap-2">
      <button onclick={startEditData}
        class="flex-1 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 bg-white border-2 border-primary text-primary hover:bg-success-soft transition-colors">
        <span class="text-base">✏️</span> Edit
      </button>
      <button onclick={shareLink}
        class="flex-1 py-2.5 rounded-xl text-sm font-bold flex items-center justify-center gap-2 bg-primary text-white hover:bg-primary/90 transition-colors">
        <span class="text-base">➤</span> Share Link
      </button>
    </div>
  </div>

  <!-- Tabs -->
  <div class="flex border-b-2 border-[#B7D9BC] mb-5">
    <button onclick={() => activeTab = 'affiliate'}
      class="flex-1 py-3 text-xs sm:text-sm font-bold text-center transition-colors relative
        {activeTab === 'affiliate' ? 'text-primary' : 'text-on-surface-variant'}">
      Affiliate
      {#if activeTab === 'affiliate'}<div class="absolute bottom-0 left-0 right-0 h-[3px] bg-primary rounded-t-full"></div>{/if}
    </button>
    <button onclick={() => activeTab = 'diskon'}
      class="flex-1 py-3 text-xs sm:text-sm font-bold text-center transition-colors relative
        {activeTab === 'diskon' ? 'text-primary' : 'text-on-surface-variant'}">
      Diskon
      {#if activeTab === 'diskon'}<div class="absolute bottom-0 left-0 right-0 h-[3px] bg-primary rounded-t-full"></div>{/if}
    </button>
    <button onclick={() => activeTab = 'pencairan'}
      class="flex-1 py-3 text-xs sm:text-sm font-bold text-center transition-colors relative
        {activeTab === 'pencairan' ? 'text-primary' : 'text-on-surface-variant'}">
      Pencairan
      {#if activeTab === 'pencairan'}<div class="absolute bottom-0 left-0 right-0 h-[3px] bg-primary rounded-t-full"></div>{/if}
    </button>
  </div>

  <!-- Tab: Affiliate -->
  {#if activeTab === 'affiliate'}
    <div class="space-y-3 mb-5">
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md text-center">
          <p class="text-xs text-on-surface-variant mb-1">Total Referral</p>
          <p class="font-bold text-2xl text-text-main">{referrals.length}</p>
          <p class="text-xs text-on-surface-variant">Orang bergabung</p>
        </div>
        <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md text-center">
          <p class="text-xs text-on-surface-variant mb-1">Bonus Register</p>
          <p class="font-bold text-2xl text-text-main">{formatRp(bonusRegister)}</p>
          <p class="text-xs text-on-surface-variant">@{rates.register_bonus?.toLocaleString('id-ID')} per referral</p>
        </div>
      </div>
      <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md">
        <p class="text-xs text-on-surface-variant mb-1">Total Diperoleh</p>
        <p class="font-bold text-2xl text-text-main">{formatRp(totalEarned)}</p>
        <p class="text-xs text-on-surface-variant">Akumulasi semua komisi + bonus register</p>
      </div>
    </div>

    {#if referrals.length}
      <div class="space-y-3">
        {#each referrals as r (r.id)}
          <div class="bg-canvas-cream rounded-2xl p-5 border-2 border-[#B7D9BC] flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-success-soft flex items-center justify-center border-2 border-white shadow-sm text-xl shrink-0">
              👤
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-bold text-base text-text-main truncate">{r.name}</p>
              <p class="text-sm text-on-surface-variant truncate">{r.email}</p>
            </div>
            <div class="text-right shrink-0">
              <span class="text-xs font-bold px-2.5 py-1 rounded-lg
                {r.role === 'trial' ? 'bg-amber-50 text-amber-600' : 'bg-success-soft text-primary'}">
                {r.role === 'trial' ? 'Trial' : r.role === 'premium' ? 'Premium' : 'Aktif'}
              </span>
              <p class="text-xs text-on-surface-variant mt-1.5">{formatDate(r.joined_at)}</p>
            </div>
          </div>
        {/each}
      </div>
    {:else}
      <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
        <p class="text-4xl mb-2">🤝</p>
        <p class="text-sm text-on-surface-variant font-medium">Belum ada yang bergabung</p>
        <p class="text-xs text-on-surface-variant/60 mt-1">Bagikan link referral kamu untuk mengundang teman</p>
      </div>
    {/if}
  {/if}

  <!-- Tab: Diskon -->
  {#if activeTab === 'diskon'}
    <div class="mb-5">
      <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md">
        <div class="flex items-center gap-2 mb-3">
          <span class="text-primary">🏷</span>
          <p class="font-bold text-sm text-text-main">Buat Kode Diskon</p>
        </div>
        <p class="text-xs text-on-surface-variant mb-3">Maksimal {discountConfig.max_discounts} kode diskon. Nilai maks {discountConfig.max_value}%.</p>

        <div class="space-y-3">
          <div>
            <label class="text-xs text-on-surface-variant font-bold mb-1 block">Kode Promo</label>
            <input bind:value={discountForm.discount_code}
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white font-bold uppercase tracking-wider"
              placeholder="Contoh: PROMO123" maxlength="20" />
            <p class="text-[10px] text-on-surface-variant mt-1">Huruf, angka, dan dash. 4-20 karakter. Harus unik.</p>
          </div>
          <div>
            <label class="text-xs text-on-surface-variant font-bold mb-1 block">Nama Diskon</label>
            <input bind:value={discountForm.discount_nama}
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
              placeholder="Contoh: Promo Spesial" maxlength="100" />
          </div>
          <div>
            <label class="text-xs text-on-surface-variant font-bold mb-1 block">Diskon (%)</label>
            <input bind:value={discountForm.discount_value} type="number" min="1"
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white font-bold"
              placeholder="Maks {discountConfig.max_value}" />
          </div>
        </div>

        {#if discountError}
          <p class="text-xs text-error font-medium mt-2">{discountError}</p>
        {/if}

        <button onclick={submitDiscount}
          disabled={discountSaving || myDiscounts.length >= discountConfig.max_discounts}
          class="w-full mt-4 py-3 rounded-xl text-sm font-bold text-white bg-primary hover:bg-primary/90 transition-colors disabled:opacity-50">
          {discountSaving ? 'Menyimpan...' : 'Buat Diskon'}
        </button>
        {#if myDiscounts.length >= discountConfig.max_discounts}
          <p class="text-[11px] text-on-surface-variant text-center mt-2">
            Batas maksimal {discountConfig.max_discounts} kode diskon tercapai
          </p>
        {/if}
      </div>
    </div>

    {#if myDiscounts.length}
      <div class="space-y-3">
        {#each myDiscounts as d (d.id)}
          <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md relative overflow-hidden">
            <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full bg-primary/5"></div>
            <div class="flex items-start justify-between mb-3">
              <div>
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wider font-bold mb-0.5">Kode Promo</p>
                <p class="text-lg font-bold text-primary tracking-widest">{d.code}</p>
              </div>
              <div class="flex items-center gap-1.5">
                <button onclick={() => copyCode(d)}
                  class="w-9 h-9 rounded-full flex items-center justify-center border-2 transition-colors shrink-0
                    {copiedId === d.id ? 'border-primary bg-success-soft text-primary' : 'border-[#B7D9BC] text-on-surface-variant hover:bg-success-soft'}">
                  <span class="text-base">{copiedId === d.id ? '✓' : '📋'}</span>
                </button>
                <button onclick={() => removeDiscount(d.id)}
                  class="w-9 h-9 rounded-full flex items-center justify-center border-2 border-error/30 text-error hover:bg-error/10 transition-colors shrink-0">
                  <span class="text-base">❌</span>
                </button>
              </div>
            </div>
            <div class="flex items-center gap-3">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-text-main truncate">{d.name}</p>
                <p class="text-xs text-on-surface-variant">Diskon</p>
              </div>
              <div class="bg-primary/10 rounded-xl px-3 py-2 text-center shrink-0">
                <p class="text-lg font-bold text-primary leading-tight">{d.value}%</p>
              </div>
            </div>
          </div>
        {/each}
      </div>
    {:else}
      <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
        <p class="text-4xl mb-2">🏷️</p>
        <p class="text-sm text-on-surface-variant font-medium">Belum ada kode diskon</p>
        <p class="text-xs text-on-surface-variant/60 mt-1">Buat kode diskon untuk dibagikan ke customer</p>
      </div>
    {/if}
  {/if}

  <!-- Tab: Pencairan -->
  {#if activeTab === 'pencairan'}
    <div class="space-y-3 mb-5">
      <div class="grid grid-cols-2 gap-3">
        <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-[#B7D9BC] shadow-md text-center">
          <p class="text-xs text-on-surface-variant mb-1">Sudah Dicairkan</p>
          <p class="font-bold text-xl text-text-main">{formatRp(totalCashedOut)}</p>
          <p class="text-xs text-on-surface-variant">{cashouts.filter(c => c.cashout_status === 'completed').length}x pencairan</p>
        </div>
        <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-primary shadow-md text-center">
          <p class="text-xs text-on-surface-variant mb-1">Menunggu Proses</p>
          <p class="font-bold text-xl text-amber-600">{formatRp(totalPendingCashout)}</p>
          <p class="text-xs text-on-surface-variant">{cashouts.filter(c => c.cashout_status === 'pending').length}x pending</p>
        </div>
      </div>
      <div class="bg-canvas-cream rounded-[24px] p-5 border-4 border-primary shadow-md">
        <p class="text-xs text-on-surface-variant mb-1">Saldo Tersedia</p>
        <p class="font-bold text-3xl text-primary">{formatRp(saldoTersedia)}</p>
        <p class="text-xs text-on-surface-variant">Minimum pencairan {formatRp(cashoutConfig.minimum)} · Diproses maksimal {cashoutConfig.processing_time || '1 hari kerja'}</p>
      </div>
      <p class="text-[11px] text-on-surface-variant text-center px-4">
        Saldo outstanding baru bisa dicairkan jika terdapat komisi dari upgrade referral.
        Bonus register akan masuk ke saldo setelah referral kamu melakukan upgrade.
      </p>
      <button onclick={openCashout}
        disabled={maxCashout() < cashoutConfig.minimum}
        class="w-full py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 bg-primary text-white hover:bg-primary/90 transition-colors disabled:opacity-50">
        <span class="text-base">💳</span>
        Cairkan Komisi {formatRp(saldoTersedia)}
      </button>
    </div>

    {#if cashouts.length}
      <p class="text-sm font-bold text-text-main mb-3">Riwayat Pencairan</p>
      <div class="space-y-2">
        {#each cashouts as c (c.cashout_id)}
          <div class="bg-canvas-cream rounded-2xl p-4 border-2 border-[#B7D9BC]">
            <div class="flex items-center justify-between mb-1">
              <p class="font-bold text-sm text-text-main">{formatRp(c.cashout_jumlah)}</p>
              <span class="text-xs font-bold px-2.5 py-1 rounded-lg
                {c.cashout_status === 'pending' ? 'bg-amber-50 text-amber-600' :
                  c.cashout_status === 'processing' ? 'bg-blue-50 text-blue-600' :
                  c.cashout_status === 'completed' ? 'bg-success-soft text-primary' :
                  'bg-red-50 text-red-600'}">
                {c.cashout_status === 'pending' ? 'Menunggu' :
                  c.cashout_status === 'processing' ? 'Diproses' :
                  c.cashout_status === 'completed' ? 'Selesai' : 'Ditolak'}
              </span>
            </div>
            <div class="flex justify-between text-xs text-on-surface-variant">
              <span>Fee: {formatRp(c.cashout_admin_fee)} · Diterima: {formatRp(c.cashout_diterima)}</span>
              <span>{formatDate(c.cashout_created_at)}</span>
            </div>
            <p class="text-[11px] text-on-surface-variant mt-1">{c.cashout_rekening_bank} - {c.cashout_rekening_nomor} a/n {c.cashout_rekening_nama}</p>
          </div>
        {/each}
      </div>
    {:else}
      <div class="bg-canvas-cream rounded-[24px] p-8 text-center border-4 border-dashed border-[#B7D9BC]">
        <p class="text-4xl mb-2">💸</p>
        <p class="text-sm text-on-surface-variant font-medium">Belum ada riwayat pencairan</p>
      </div>
    {/if}
  {/if}
</div>

<!-- Edit Data Modal -->
{#if editingData}
  <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick={() => editingData = false}>
    <div class="bg-canvas-cream rounded-[32px] p-6 border-4 border-[#B7D9BC] shadow-xl max-w-sm w-full max-h-[90vh] overflow-y-auto" onclick={(e) => e.stopPropagation()}>
      <h3 class="font-bold text-lg text-text-main mb-4">Edit Rekening</h3>

      <div class="mb-3">
        <label class="text-xs text-on-surface-variant font-bold mb-1 block">Kode Referral</label>
        <input bind:value={editCodeValue}
          class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white uppercase tracking-wider font-bold"
          placeholder="KODEUNIK" maxlength="20" />
        <p class="text-[10px] text-on-surface-variant mt-1">Huruf dan angka saja, 4-20 karakter</p>
      </div>

      <div class="mb-3">
        <label class="text-xs text-on-surface-variant font-bold mb-1 block">Nama Pemilik Rekening</label>
        <input bind:value={rekeningForm.rekening_nama}
          class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
          placeholder="Nama sesuai rekening" />
      </div>

      <div class="mb-3">
        <label class="text-xs text-on-surface-variant font-bold mb-1 block">Metode Pembayaran</label>
        <div class="relative">
          <select bind:value={rekeningForm.rekening_bank}
            class="w-full pl-4 pr-10 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white appearance-none"
            disabled={loadingBankOptions}>
            <option value="" disabled>{loadingBankOptions ? 'Memuat...' : 'Pilih bank atau e-wallet'}</option>
            {#each bankOptions as group}
              <optgroup label={group.group}>
                {#each group.items as b}
                  <option value={b.name}>{b.name}</option>
                {/each}
              </optgroup>
            {/each}
          </select>
          <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg pointer-events-none">▾</span>
        </div>
      </div>

      <div class="mb-3">
        <label class="text-xs text-on-surface-variant font-bold mb-1 block">Nomor Rekening / E-Wallet</label>
        <input bind:value={rekeningForm.rekening_nomor}
          class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white"
          placeholder="Nomor rekening atau e-wallet" />
      </div>

      {#if editDataError}
        <p class="text-xs text-error font-medium mb-3">{editDataError}</p>
      {/if}

      <div class="flex gap-3">
        <button onclick={() => editingData = false}
          class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray">Batal</button>
        <button onclick={saveData} disabled={savingData}
          class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green disabled:opacity-50">
          {savingData ? 'Menyimpan...' : 'Simpan'}
        </button>
      </div>
    </div>
  </div>
{/if}

<!-- Cashout Modal -->
{#if showCashout}
  <div class="fixed inset-0 bg-black/50 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4" onclick={() => showCashout = false}>
    <div class="bg-canvas-cream rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 border-4 border-primary shadow-xl w-full sm:max-w-sm max-h-[90vh] overflow-y-auto" onclick={(e) => e.stopPropagation()}>
      <div class="w-10 h-1 bg-outline-variant rounded-full mx-auto mb-4 sm:hidden"></div>
      <h3 class="font-bold text-lg text-text-main mb-2">Cairkan Komisi</h3>
      <p class="text-xs text-on-surface-variant mb-4">Saldo tersedia: <span class="font-bold text-primary">{formatRp(saldoTersedia)}</span></p>

      <div class="mb-3">
        <label class="text-xs text-on-surface-variant font-bold mb-1 block">Nominal Pencairan</label>
        <div class="bg-white rounded-xl p-4 border-2 border-primary">
          <p class="text-2xl font-bold text-primary text-center mb-3">{formatRp(cashoutAmount || 0)}</p>
          <input bind:value={cashoutAmount} type="range" min={cashoutConfig.minimum} max={maxCashout()} step={step()}
            class="w-full accent-primary h-2 rounded-full appearance-none cursor-pointer" />
          <div class="flex justify-between text-[10px] text-on-surface-variant mt-1.5">
            <span>{formatRp(cashoutConfig.minimum)}</span>
            <span>{formatRp(maxCashout())}</span>
          </div>
        </div>
        <div class="flex gap-2 mt-2">
          <button onclick={() => cashoutAmount = cashoutConfig.minimum} class="flex-1 py-1.5 text-[10px] font-bold rounded-lg bg-success-soft text-primary">Min</button>
          <button onclick={() => cashoutAmount = Math.floor(saldoTersedia * 0.25 / 1000) * 1000} class="flex-1 py-1.5 text-[10px] font-bold rounded-lg bg-success-soft text-primary">25%</button>
          <button onclick={() => cashoutAmount = Math.floor(saldoTersedia * 0.5 / 1000) * 1000} class="flex-1 py-1.5 text-[10px] font-bold rounded-lg bg-success-soft text-primary">50%</button>
          <button onclick={() => cashoutAmount = maxCashout()} class="flex-1 py-1.5 text-[10px] font-bold rounded-lg bg-success-soft text-primary">Max</button>
        </div>
      </div>

      {#if cashoutAmount >= cashoutConfig.minimum}
        <div class="bg-white rounded-xl p-3 border-2 border-[#B7D9BC] mb-3">
          <div class="flex justify-between text-xs text-on-surface-variant mb-1">
            <span>Platform Fee ({cashoutConfig.admin_rate}%)</span>
            <span class="font-bold text-error">-{formatRp(adminFee)}</span>
          </div>
          <div class="flex justify-between text-xs text-on-surface-variant mb-1">
            <span>Total Potongan</span>
            <span class="font-bold">{formatRp((cashoutAmount || 0) + adminFee)}</span>
          </div>
          <div class="border-t my-1.5"></div>
          <div class="flex justify-between text-sm font-bold text-text-main">
            <span>Diterima</span>
            <span class="text-primary">{formatRp(cashoutAmount || 0)}</span>
          </div>
        </div>
      {/if}

      {#if !hasRekening}
        <div class="bg-amber-50 border-2 border-amber-300 rounded-xl p-3 mb-3">
          <p class="text-xs text-amber-700 font-medium">Lengkapi data rekening terlebih dahulu melalui tombol Edit.</p>
        </div>
      {/if}

      <p class="text-[11px] text-on-surface-variant mb-4">Pencairan diproses maksimal <span class="font-bold">{cashoutConfig.processing_time || '1 hari kerja'}</span>.</p>

      {#if cashoutError}
        <p class="text-xs text-error font-medium mb-3">{cashoutError}</p>
      {/if}

      <div class="flex gap-3">
        <button onclick={() => showCashout = false}
          class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray">Batal</button>
        <button onclick={submitCashout} disabled={cashingOut || !hasRekening}
          class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green disabled:opacity-50">
          {cashingOut ? 'Memproses...' : 'Cairkan'}
        </button>
      </div>
    </div>
  </div>
{/if}

<style>
  .btn-pop-green {
    background-color: #6DBE7B;
    box-shadow: 0 3px 0 #176c33;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #176c33;
  }
  .btn-pop-gray {
    background-color: #e5e5e5;
    box-shadow: 0 3px 0 #999;
    transition: all 0.1s ease;
  }
  .btn-pop-gray:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #999;
  }
  input[type="range"] {
    -webkit-appearance: none;
    background: transparent;
  }
  input[type="range"]::-webkit-slider-runnable-track {
    height: 8px;
    border-radius: 4px;
    background: #B7D9BC;
  }
  input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #176C33;
    margin-top: -8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }
  input[type="range"]::-moz-range-track {
    height: 8px;
    border-radius: 4px;
    background: #B7D9BC;
  }
  input[type="range"]::-moz-range-thumb {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #176C33;
    border: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }
</style>
