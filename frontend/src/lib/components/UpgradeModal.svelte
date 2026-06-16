<script>
  import * as api from '../services/api.js'

  let { show = false, onclose, onswitch } = $props()

  let selectedPlan = $state(null)
  let discountCode = $state('')
  let discountResult = $state(null)
  let validatingDiscount = $state(false)
  let paying = $state(false)
  let paymentData = $state(null)
  let paymentError = $state('')

  $effect(() => {
    if (show) {
      paymentData = null
      paymentError = ''
      discountCode = ''
      discountResult = null
      if (!selectedPlan) loadPlans()
    }
  })

  async function loadPlans() {
    try {
      const data = await api.getPlans()
      const plans = data.plans || data
      selectedPlan = plans.find(p => p.recommended) || plans.find(p => p.price > 0) || plans[0]
    } catch (e) {
    }
  }

  async function confirmPayment() {
    if (!selectedPlan) return
    paying = true
    paymentError = ''
    try {
      const data = await api.createPayment(selectedPlan.id, discountCode || null)
      paymentData = data
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
      discountResult = { error: e.message }
    }
    validatingDiscount = false
  }

  function handleClose() {
    paymentData = null
    paymentError = ''
    discountCode = ''
    discountResult = null
    onclose?.()
  }
</script>

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
</style>

{#if show}
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" onclick={handleClose}>
    <div class="bg-canvas-cream rounded-t-[32px] sm:rounded-[32px] p-5 sm:p-6 border-4 border-primary shadow-xl w-full sm:max-w-sm max-h-[90vh] overflow-y-auto" onclick={(e) => e.stopPropagation()}>
      <div class="w-10 h-1 bg-outline-variant rounded-full mx-auto mb-4 sm:hidden"></div>

      {#if paymentData}
        <div class="text-center py-4">
          <span class="material-symbols-outlined text-5xl text-primary mb-3">qr_code_2</span>
          <p class="text-sm font-bold text-text-main mb-1">Scan QRIS untuk membayar</p>
          <p class="text-xs text-on-surface-variant mb-4">Order: {paymentData.order_code}</p>
          {#if paymentData.qris_string}
            <div class="bg-white p-4 rounded-xl border-2 border-[#B7D9BC] mb-4">
              <p class="text-xs text-on-surface-variant break-all">{paymentData.qris_string}</p>
            </div>
          {/if}
          <p class="font-bold text-lg text-primary">Rp{paymentData.total?.toLocaleString('id-ID')}</p>
          <button class="mt-4 w-full py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={handleClose}>
            Tutup
          </button>
        </div>
      {:else}
        <h3 class="font-bold text-lg text-text-main mb-1">Checkout</h3>
        <p class="text-sm text-on-surface-variant mb-4">{selectedPlan?.name || 'Paket'} · {selectedPlan?.period_label || ''}</p>

        {#if paymentError}
          <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-3 text-sm">{paymentError}</div>
        {/if}

        <div class="bg-white rounded-xl p-4 border-2 border-[#B7D9BC] mb-3">
          <p class="text-xs text-on-surface-variant mb-1">Harga</p>
          <p class="font-bold text-lg text-text-main">
            {selectedPlan?.price === 0 ? 'Gratis' : `Rp${selectedPlan?.price?.toLocaleString('id-ID')}`}
          </p>
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
          {#if discountResult?.message && !discountResult?.valid}
            <p class="text-xs text-error mt-1">{discountResult.message}</p>
          {/if}
        </div>

        {#if discountResult?.valid}
          {@const discountAmount = discountResult.amount || 0}
          {@const totalBayar = (selectedPlan?.price || 0) - discountAmount}
          <div class="rounded-xl p-4 border-2 mb-3" style="background: rgba(33, 150, 243, 0.08);">
            <div class="flex items-center gap-2 mb-2">
              <span class="material-symbols-outlined text-lg" style="color: rgb(33, 150, 243);">sell</span>
              <span class="text-sm font-bold" style="color: rgb(33, 150, 243);">Diskon {discountResult.name || 'Diskon'}{discountResult.rate ? ` (${discountResult.rate}%)` : ''}</span>
            </div>
            <div class="flex items-center justify-between mb-1">
              <span class="text-xs text-on-surface-variant">Harga asli</span>
              <span class="text-xs text-on-surface-variant line-through">Rp{(selectedPlan?.price || 0).toLocaleString('id-ID')}</span>
            </div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-xs font-bold" style="color: rgb(33, 150, 243);">Diskon</span>
              <span class="text-xs font-bold" style="color: rgb(33, 150, 243);">-Rp{discountAmount.toLocaleString('id-ID')}</span>
            </div>
            <div class="border-t pt-2 flex items-center justify-between">
              <span class="text-sm font-bold text-text-main">Total Bayar</span>
              <span class="font-bold text-xl" style="color: rgb(33, 150, 243);">Rp{totalBayar.toLocaleString('id-ID')}</span>
            </div>
          </div>
        {/if}

        <div class="flex gap-3">
          <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-on-surface-variant btn-pop-gray" onclick={handleClose}>
            Batal
          </button>
          <button class="flex-1 py-3 rounded-2xl text-sm font-bold text-white btn-pop-green disabled:opacity-50"
            onclick={confirmPayment} disabled={paying || !selectedPlan}>
            {paying ? 'Memproses...' : 'Bayar'}
          </button>
        </div>
      {/if}
    </div>
  </div>
{/if}
