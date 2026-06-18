<script>
  import { get } from 'svelte/store'
  import { onMount, onDestroy } from 'svelte'
  import QRCode from 'qrcode'
  import { user, plans, userPlan, userRole, trialDays, serverDate, isAuthenticated } from '../stores/authStore.js'
  import { applyServerData } from '../stores/authStore.js'
  import * as api from '../services/api.js'
  import AppButton from '../components/AppButton.svelte'
  import AppModal from '../components/AppModal.svelte'

  let userVal = $state(null)
  let plansVal = $state([])
  let userPlanVal = $state(null)
  let userRoleVal = $state('')
  let trialDaysVal = $state(10)
  let serverDateVal = $state(null)
  let isAuth = $state(false)

  let selectedPlan = $state(null)
  let showCheckout = $state(false)
  let discountCode = $state('')
  let discountResult = $state(null)
  let validatingDiscount = $state(false)
  let paying = $state(false)
  let paymentData = $state(null)
  let paymentError = $state('')

  let activePayment = $state(null)
  let showQrModal = $state(false)
  let paymentChecking = $state(false)
  let qrCanvas = $state(null)
  let pollTimer = null
  let countdownTimer = null
  let countdown = $state('')
  let payments = $state([])

  $effect(() => {
    const u1 = user.subscribe(v => userVal = v)
    const u2 = plans.subscribe(v => {
      plansVal = v
      const recommended = v.find(p => p.recommended)
      if (recommended && !selectedPlan) {
        selectedPlan = recommended
      }
    })
    const u3 = userPlan.subscribe(v => userPlanVal = v)
    const u4 = userRole.subscribe(v => userRoleVal = v)
    const u5 = trialDays.subscribe(v => trialDaysVal = v)
    const u6 = serverDate.subscribe(v => serverDateVal = v)
    const u7 = isAuthenticated.subscribe(v => isAuth = v)
    return () => { u1(); u2(); u3(); u4(); u5(); u6(); u7() }
  })

  onDestroy(() => { stopPolling(); stopCountdown() })

  function startCountdown(expiredAt) {
    stopCountdown()
    function update() {
      if (!expiredAt) { countdown = ''; return }
      const now = new Date()
      const exp = new Date(expiredAt)
      const diff = exp - now
      if (diff <= 0) { countdown = 'Kedaluwarsa'; stopCountdown(); return }
      const m = Math.floor(diff / 60000)
      const s = Math.floor((diff % 60000) / 1000)
      countdown = `${m}:${String(s).padStart(2, '0')}`
    }
    update()
    countdownTimer = setInterval(update, 1000)
  }

  function stopCountdown() {
    if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null }
  }

  function planTheme(plan) {
    const c = plan?.color || 'rgb(23, 108, 51)'
    const m = c.match(/rgb\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/)
    const r = m ? +m[1] : 23, g = m ? +m[2] : 108, b = m ? +m[3] : 51
    const hex = '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('')
    const bg = `rgba(${r}, ${g}, ${b}, 0.2)`
    return { color: hex, bg }
  }

  function isCurrentPlan(plan) {
    return userPlanVal?.plan_id === plan?.id
  }

  function isExpired() {
    return !!userPlanVal?.expired
  }

  function isTrial() {
    return userRoleVal === 'trial'
  }

  function trialRemaining() {
    if (isExpired()) return 0
    if (!userPlanVal?.subscribe_end_at) return 0
    const end = new Date(userPlanVal.subscribe_end_at)
    const now = serverDateVal ? new Date(serverDateVal) : new Date()
    return Math.max(0, Math.ceil((end - now) / (1000 * 60 * 60 * 24)))
  }

  function formatDate(d) {
    if (!d) return '-'
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  }

  function formatTime(d) {
    if (!d) return '-'
    const date = new Date(d)
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) + ', ' + date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', '.')
  }

  function selectPlan(plan) {
    selectedPlan = selectedPlan?.id === plan.id ? null : plan
    paymentData = null
    paymentError = ''
    discountCode = ''
    discountResult = null
  }

  async function startPayment(plan) {
    showCheckout = true
    selectedPlan = plan
    paymentData = null
    paymentError = ''
    discountCode = ''
    discountResult = null
  }

  async function confirmPayment() {
    if (!selectedPlan) return
    paying = true
    paymentError = ''
    try {
      stopPolling()
      stopCountdown()
      const data = await api.createPayment(selectedPlan.id, discountCode?.trim().toUpperCase() || null)
      activePayment = data.payment || data
      showCheckout = false
      showQrModal = true
      if (activePayment?.status === 'pending') {
        startPolling()
        startCountdown(activePayment.expired_at)
      }
      await loadHistory()
    } catch (e) {
      paymentError = e.message
    }
    paying = false
  }

  async function validateDiscount() {
    if (!discountCode || !selectedPlan) return
    validatingDiscount = true
    discountResult = null
    try {
      discountResult = await api.validateDiscount(discountCode, selectedPlan.id)
    } catch (e) {
      discountResult = { valid: false, error: e.message }
    }
    validatingDiscount = false
  }

  function startPolling() {
    stopPolling()
    paymentChecking = true
    pollTimer = setInterval(async () => {
      if (!activePayment) { stopPolling(); return }
      try {
        const res = await api.getPaymentStatus(activePayment.id)
        const updated = res.payment || res
        activePayment = updated
        if (updated.status !== 'pending') {
          stopPolling()
          stopCountdown()
          if (updated.status === 'paid') {
            if (typeof localStorage !== 'undefined') localStorage.setItem('lk_just_paid', String(Date.now()))
            try {
              const me = await api.getMe()
              applyServerData(me)
            } catch (e) { /* ignore */ }
            setTimeout(async () => {
              try {
                const me2 = await api.getMe()
                applyServerData(me2)
              } catch (e) {}
              activePayment = null
              showQrModal = false
            }, 5000)
          }
        }
      } catch (e) {
      }
    }, 3000)
  }

  function stopPolling() {
    paymentChecking = false
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null }
  }

  async function closeQrModal() {
    stopPolling()
    stopCountdown()
    showQrModal = false
    activePayment = null
  }

  $effect(() => {
    if (showQrModal && activePayment?.status === 'pending' && activePayment?.qris_string && qrCanvas) {
      renderQr()
      startCountdown(activePayment.expired_at)
    }
  })

  onMount(async () => {
    await loadHistory()
  })

  async function loadHistory() {
    try {
      const res = await api.getPaymentHistory()
      payments = res.payments || []
      if (!activePayment || activePayment.status !== 'pending') {
        const pending = payments.find(p => p.status === 'pending')
        if (pending) activePayment = pending
      }
    } catch (e) {
    }
  }

  async function cancelActivePayment() {
    if (!activePayment) return
    try {
      await api.cancelPayment(activePayment.id)
      stopPolling()
      stopCountdown()
      activePayment = null
      await loadHistory()
    } catch (e) {
      activePayment = null
    }
  }

  async function renderQr() {
    if (!qrCanvas || !activePayment?.qris_string) return
    try {
      await QRCode.toCanvas(qrCanvas, activePayment.qris_string, {
        width: 200,
        margin: 1,
        color: { dark: '#1a1a1a', light: '#ffffff' }
      })
    } catch (e) {
    }
  }
</script>

<div class="px-margin-mobile md:px-margin-desktop pt-5 max-w-6xl mx-auto pb-8">
  {#if userPlanVal}
    <div class="mb-4 bg-canvas-cream rounded-[24px] p-4 border-4 border-[#B7D9BC] shadow-md">
      {#if isTrial()}
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 border-white shadow-sm shrink-0 {trialRemaining() > 0 ? 'bg-success-soft' : 'bg-error/10'}">
            <span class="{trialRemaining() > 0 ? 'text-primary' : 'text-error'}">
              {trialRemaining() > 0 ? '⏰' : '⏰'}
            </span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-label-lg text-text-main">{trialRemaining() > 0 ? 'Masa Trial' : 'Trial Berakhir'}</p>
            <p class="text-sm text-on-surface-variant">
              {#if trialRemaining() > 0}
                Sisa <span class="font-bold text-primary">{trialRemaining()} hari</span> dari {trialDaysVal} hari
              {:else}
                Masa trial {trialDaysVal} hari telah berakhir.
              {/if}
            </p>
          </div>
        </div>
        <div class="border-t-2 border-[#B7D9BC]/40 my-3"></div>
      {/if}
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 border-white shadow-sm shrink-0 {isExpired() ? 'bg-error/10' : 'bg-success-soft'}">
          <span class="{isExpired() ? 'text-error' : 'text-primary'}">🏆</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-label-lg text-text-main truncate">{userPlanVal.plan_nama || 'Plan Aktif'}</p>
          <p class="text-sm text-on-surface-variant">
            {#if isExpired()}
              Kedaluwarsa sejak {formatDate(userPlanVal.subscribe_end_at)}
            {:else}
              Aktif hingga {formatDate(userPlanVal.subscribe_end_at)}
            {/if}
          </p>
        </div>
        {#if isExpired()}
          <span class="text-xs text-error font-bold bg-error/10 px-2 py-1 rounded-lg shrink-0">Kedaluwarsa</span>
        {:else}
          <span class="text-xs text-primary font-bold bg-success-soft px-2 py-1 rounded-lg shrink-0">Aktif</span>
        {/if}
      </div>
    </div>
  {/if}

  <!-- Pending Payment Banner -->
  {#if activePayment && activePayment.status === 'pending'}
    <div class="mb-4 bg-canvas-cream rounded-[24px] p-4 border-4 border-blue-300 shadow-md">
      <div class="flex items-center gap-3">
        <button onclick={cancelActivePayment}
          class="w-9 h-9 rounded-full bg-error/10 flex items-center justify-center text-error hover:bg-error/20 transition-colors shrink-0">
          <span class="text-lg">✕</span>
        </button>
        <button class="flex-1 min-w-0 text-left" onclick={() => showQrModal = true}>
          <p class="font-label-lg text-text-main">Menunggu Pembayaran</p>
          <p class="text-sm text-on-surface-variant truncate">{activePayment.order_code} &middot; Rp{(activePayment.actual_amount ?? activePayment.total)?.toLocaleString('id-ID')}</p>
        </button>
        <span class="text-xs font-bold px-2 py-1 rounded-lg bg-blue-50 text-blue-600 shrink-0">Nanti Saja</span>
      </div>
    </div>
  {/if}

  <h3 class="font-headline-md text-headline-md mb-4 mt-10 flex items-center gap-2">
    <span class="w-8 h-8 rounded-full flex items-center justify-center text-base" style="background: #176C33; color: white">💳</span>
    Pilih Paket
  </h3>

  <div class="space-y-3">
    {#each plansVal as plan (plan.id)}
      {@const theme = planTheme(plan)}
      <div role="button" tabindex="0" class="w-full text-left rounded-[24px] overflow-hidden transition-all border-4 cursor-pointer"
        style="background: {selectedPlan?.id === plan.id ? theme.bg : '#FFFBF5'}; border-color: {theme.color}; box-shadow: {selectedPlan?.id === plan.id ? `0 6px 24px ${theme.color}30` : `0 2px 12px ${theme.color}10`}"
        onclick={() => selectPlan(plan)}
        onkeydown={(e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); selectPlan(plan); } }}>
        <div class="p-4 sm:p-5">
          <div class="flex items-start gap-3">
            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0 border-2 border-white shadow-sm"
              style="background: {selectedPlan?.id === plan.id ? 'white' : theme.bg}; color: {theme.color}">
              <span class="text-xl sm:text-2xl">🏆</span>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-sm sm:text-base text-text-main">{plan.name}</h3>
                {#if isCurrentPlan(plan)}
                  <span class="text-[10px] sm:text-[11px] font-bold px-1.5 sm:px-2 py-0.5 rounded" style="background: white; color: {theme.color}">Saat Ini</span>
                {/if}
              </div>
              <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5 line-clamp-2">{plan.description}</p>
              <div class="flex items-center gap-2 sm:gap-3 mt-2">
                {#if plan.price_strikethrough}
                  <p class="text-xs sm:text-sm text-on-surface-variant line-through">Rp{plan.price_strikethrough?.toLocaleString('id-ID')}</p>
                {/if}
                <p class="font-bold text-sm sm:text-base text-text-main">
                  {plan.price === 0 ? 'Gratis' : `Rp${plan.price?.toLocaleString('id-ID')}`}
                </p>
                <span class="text-[10px] sm:text-xs text-on-surface-variant">{plan.value} Anak · {plan.period_label}</span>
              </div>
            </div>
            <span class="text-lg sm:text-xl shrink-0 transition-transform mt-1"
                style="color: {theme.color}; opacity: 0.5"
                class:rotate-180={selectedPlan?.id === plan.id}>▾</span>
          </div>
        </div>

        {#if selectedPlan?.id === plan.id}
          <div class="px-4 sm:px-5 pb-4 sm:pb-5 pt-0 fade-in-up">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-3 rounded-xl border-2 border-dashed mb-3"
              style="border-color: {theme.color}40; background: white">
              <span class="text-xs sm:text-sm text-on-surface-variant flex items-center gap-1">
                <span class="text-sm sm:text-base" style="color: {theme.color}">✅</span>
                {plan.description}
              </span>
              <span class="text-xs sm:text-sm text-on-surface-variant flex items-center gap-1">
                <span class="text-sm sm:text-base" style="color: {theme.color}">✅</span>
                {plan.period_label}
              </span>
            </div>

            {#if !isCurrentPlan(plan)}
              <button onclick={(e) => { e.stopPropagation(); startPayment(plan) }}
                class="w-full py-2.5 sm:py-3 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md transition-all"
                style="background: {theme.color}">
                Pilih Paket Ini
              </button>
            {:else}
              <div class="py-2.5 text-center text-xs sm:text-sm font-bold text-on-surface-variant/40 bg-surface-container rounded-xl">Paket aktif saat ini</div>
            {/if}
          </div>
        {/if}
      </div>
    {/each}
  </div>

  <!-- Payment History -->
  {#if payments.length}
    <div class="mt-10">
      <h3 class="font-bold text-sm sm:text-base text-text-main mb-3">Riwayat Pembayaran</h3>
      <div class="space-y-2">
        {#each payments as p (p.id)}
          <div class="bg-canvas-cream rounded-xl p-3 border-2 border-[#B7D9BC] flex items-center gap-3">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full flex items-center justify-center shrink-0
              {p.status === 'paid' ? 'bg-success-soft' : p.status === 'pending' ? 'bg-amber-50' : 'bg-error/10'}">
              <span class="text-sm sm:text-base
                {p.status === 'paid' ? 'text-primary' : p.status === 'pending' ? 'text-amber-500' : 'text-error'}">
                {p.status === 'paid' ? '✅' : p.status === 'pending' ? '⏳' : '✖'}
              </span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs sm:text-sm font-bold text-text-main truncate">{p.plan_name}</p>
              <p class="text-[10px] sm:text-xs text-on-surface-variant truncate">{p.order_code}</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-xs sm:text-sm font-bold text-text-main">Rp{(p.actual_amount ?? p.total)?.toLocaleString('id-ID')}</p>
              <p class="text-[9px] sm:text-[10px] font-bold
                {p.status === 'paid' ? 'text-primary' : p.status === 'pending' ? 'text-amber-500' : 'text-error'}">
                {p.status === 'paid' ? 'Lunas' : p.status === 'pending' ? 'Pending' : 'Kedaluwarsa'}
              </p>
            </div>
          </div>
        {/each}
      </div>
    </div>
  {/if}
</div>

<AppModal show={showCheckout} title="" onclose={() => showCheckout = false}>
  <h3 class="font-bold text-lg text-text-main mb-1">Checkout</h3>
  <p class="text-sm text-on-surface-variant mb-4">{selectedPlan?.name || 'Paket'} · {selectedPlan?.period_label || ''}</p>

  {#if paymentError}
    <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-3 text-sm">{paymentError}</div>
  {/if}

  <div class="bg-white rounded-xl p-4 border-2 border-[#B7D9BC] mb-3">
    <p class="text-xs text-on-surface-variant mb-1">Harga</p>
    <div class="flex items-center gap-2">
      {#if selectedPlan?.price_strikethrough}
        <p class="text-sm text-on-surface-variant line-through">Rp{selectedPlan?.price_strikethrough?.toLocaleString('id-ID')}</p>
      {/if}
      <p class="font-bold text-lg text-text-main">
        {selectedPlan?.price === 0 ? 'Gratis' : `Rp${selectedPlan?.price?.toLocaleString('id-ID')}`}
      </p>
    </div>
  </div>

  <div class="mb-3">
    <label class="text-xs text-on-surface-variant font-bold mb-1 block">Kode Diskon</label>
    <div class="flex gap-2">
      <input bind:value={discountCode}
        class="flex-1 min-w-0 px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-sm focus:outline-none focus:border-primary bg-white font-bold uppercase"
        placeholder="Masukkan kode (opsional)" />
      <button onclick={validateDiscount}
        class="px-4 py-3 rounded-xl text-sm font-bold text-white btn-pop-green shrink-0 disabled:opacity-50"
        disabled={!discountCode || !selectedPlan || validatingDiscount}>
        {validatingDiscount ? 'Cek...' : 'Pakai'}
      </button>
    </div>
    {#if discountResult && !discountResult.valid && discountResult.message}
      <p class="text-xs text-error mt-1">{discountResult.message}</p>
    {:else if discountResult && !discountResult.valid && discountResult.error}
      <p class="text-xs text-error mt-1">{discountResult.error}</p>
    {/if}
  </div>

  {#if discountResult?.valid}
    {@const theme = planTheme(selectedPlan)}
    {@const discountAmount = discountResult.amount || 0}
    {@const totalBayar = (selectedPlan?.price || 0) - discountAmount}
    <div class="rounded-xl p-4 border-2 mb-3" style="background: {theme.bg}; border-color: {theme.color}60">
      <div class="flex items-center gap-2 mb-2">
        <span class="text-lg" style="color: {theme.color}">🏷</span>
        <span class="text-sm font-bold" style="color: {theme.color}">
          {discountResult.name || 'Diskon'}{discountResult.rate ? ` (${discountResult.rate}%)` : ''}
        </span>
      </div>
      <div class="flex items-center justify-between mb-1">
        <span class="text-xs text-on-surface-variant">Harga asli</span>
        <span class="text-xs text-on-surface-variant line-through">Rp{(selectedPlan?.price || 0).toLocaleString('id-ID')}</span>
      </div>
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold" style="color: {theme.color}">Diskon</span>
        <span class="text-xs font-bold" style="color: {theme.color}">-Rp{discountAmount.toLocaleString('id-ID')}</span>
      </div>
      <div class="border-t pt-2 flex items-center justify-between" style="border-color: {theme.color}30">
        <span class="text-sm font-bold text-text-main">Total Bayar</span>
        <span class="font-bold text-xl" style="color: {theme.color}">Rp{totalBayar.toLocaleString('id-ID')}</span>
      </div>
    </div>
  {/if}

  <div class="flex gap-3">
    <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={() => showCheckout = false}>
      Batal
    </button>
    <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green disabled:opacity-50"
      onclick={confirmPayment} disabled={paying || !selectedPlan}>
      {paying ? 'Memproses...' : 'Bayar'}
    </button>
  </div>
</AppModal>

<!-- QR Payment Modal -->
{#if showQrModal && activePayment}
  <div class="fixed inset-0 bg-black/50 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4" onclick={closeQrModal}>
    <div class="bg-canvas-cream rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 border-4 border-[#B7D9BC] shadow-xl w-full sm:max-w-sm max-h-[90vh] overflow-y-auto" onclick={(e) => e.stopPropagation()}>
      <div class="w-10 h-1 bg-outline-variant rounded-full mx-auto mb-4 sm:hidden"></div>
      <div class="text-center">
        <h3 class="font-bold text-lg text-text-main mb-1">{activePayment.plan_name || selectedPlan?.name}</h3>

        {#if activePayment.discount > 0}
          <p class="text-xs text-on-surface-variant mb-1">
            Harga: <span class="line-through">Rp{activePayment.amount?.toLocaleString('id-ID')}</span>
            &middot; Diskon: <span class="font-bold text-blue-600">-Rp{activePayment.discount?.toLocaleString('id-ID')}</span>
          </p>
        {/if}

        <p class="text-2xl font-bold text-blue-600 mb-1">Rp{(activePayment.actual_amount ?? activePayment.total)?.toLocaleString('id-ID')}</p>
        {#if activePayment.unic > 0}
          <p class="text-[10px] text-on-surface-variant mb-3">Termasuk kode unik +Rp{activePayment.unic?.toLocaleString('id-ID')}</p>
        {:else}
          <p class="mb-4"></p>
        {/if}

        {#if activePayment.status === 'pending'}
          <div class="mb-4">
            <div class="bg-white rounded-2xl p-4 border-2 border-[#B7D9BC] inline-block">
              <canvas bind:this={qrCanvas}></canvas>
            </div>
            <p class="text-xs text-on-surface-variant mt-3">Scan QRIS di atas menggunakan aplikasi bank/e-wallet</p>
            {#if paymentChecking}
              <div class="flex items-center justify-center gap-2 mt-3 text-sm text-primary">
                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Menunggu pembayaran...
              </div>
            {/if}
            {#if countdown}
              <p class="text-sm font-bold text-primary mt-2">{countdown}</p>
            {/if}
            {#if activePayment.expired_at}
              <p class="text-xs text-on-surface-variant mt-1">Kedaluwarsa: {formatTime(activePayment.expired_at)}</p>
            {/if}
          </div>
        {:else if activePayment.status === 'paid'}
          <div class="mb-4">
            <div class="w-16 h-16 rounded-full bg-success-soft flex items-center justify-center mx-auto mb-3">
              <span class="text-3xl text-primary">✅</span>
            </div>
            <p class="font-bold text-primary text-lg">Pembayaran Berhasil!</p>
            <p class="text-sm text-on-surface-variant mt-1">Paket telah diaktifkan</p>
          </div>
        {:else}
          <div class="mb-4">
            <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center mx-auto mb-3">
              <span class="text-3xl text-error">✖</span>
            </div>
            <p class="font-bold text-error text-lg">{activePayment.status === 'expired' ? 'Kedaluwarsa' : 'Dibatalkan'}</p>
          </div>
        {/if}

        <button onclick={closeQrModal}
          class="w-full py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray">
          {activePayment.status === 'paid' ? 'Tutup' : 'Nanti Saja'}
        </button>
      </div>
    </div>
  </div>
{/if}

<style>
  .fade-in-up { animation: fadeInUp 0.3s ease-out; }
  @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
  .btn-pop-green {
    background-color: #176C33;
    box-shadow: 0 3px 0 #0d4a22;
    transition: all 0.1s ease;
  }
  .btn-pop-green:active {
    transform: translateY(3px);
    box-shadow: 0 0px 0 #0d4a22;
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
</style>
