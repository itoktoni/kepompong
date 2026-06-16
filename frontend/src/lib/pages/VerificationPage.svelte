<script>
  import { slide } from 'svelte/transition'
  import * as api from '../services/api.js'
  import { verificationGateway, user, needsVerification, logout, applyServerData } from '../stores/authStore.js'

  let { onsuccess, inline = false } = $props()

  let code = $state('')
  let loading = $state(false)
  let error = $state('')
  let sending = $state(false)
  let sendMessage = $state('')
  let codeSent = $state(false)
  let timerSeconds = $state(0)
  let timerInterval = $state(null)

  const timerDisplay = $derived(() => {
    const m = Math.floor(timerSeconds / 60)
    const s = timerSeconds % 60
    return `${m}:${s.toString().padStart(2, '0')}`
  })

  function startTimer() {
    startTimerWith(600)
  }

  function startTimerWith(seconds) {
    timerSeconds = seconds
    if (timerInterval) clearInterval(timerInterval)
    timerInterval = setInterval(() => {
      timerSeconds--
      if (timerSeconds <= 0) {
        clearInterval(timerInterval)
        timerInterval = null
        timerSeconds = 0
      }
    }, 1000)
  }

  function stopTimer() {
    if (timerInterval) clearInterval(timerInterval)
    timerInterval = null
    timerSeconds = 0
  }

  $effect(() => {
    return () => stopTimer()
  })

  const gateway = $derived($verificationGateway)
  const userVal = $derived($user)

  async function handleSendCode() {
    sending = true
    sendMessage = ''
    error = ''
    try {
      await api.sendVerification(gateway)
      codeSent = true
      sendMessage = 'Kode verifikasi telah dikirim!'
      startTimer()
    } catch (err) {
      codeSent = true
      if (err.status === 429 && err.cooldown) {
        error = err.message
        startTimerWith(err.cooldown)
      } else {
        error = err.message || 'Gagal mengirim kode'
      }
    } finally {
      sending = false
    }
  }

  async function handleVerify(e) {
    if (e) e.preventDefault()
    if (code.length !== 6) return
    loading = true
    error = ''
    try {
      const data = await api.verifyCode(code)
      authStore.needsVerification.set(false)
      authStore.applyServerData(data)
      if (onsuccess) onsuccess(data)
    } catch (err) {
      error = err.message || 'Kode tidak valid'
    } finally {
      loading = false
    }
  }

  function handleCodeInput(e) {
    const val = e.target.value.replace(/\D/g, '').slice(0, 6)
    code = val
  }

  const gatewayLabel = $derived(
    gateway === 'whatsapp' ? 'WhatsApp' :
    gateway === 'telegram' ? 'Telegram' : 'Email'
  )

  const maskedContact = $derived(() => {
    if (!user) return ''
    if (gateway === 'email') {
      const parts = (user.email || '').split('@')
      if (parts.length !== 2) return user.email
      const local = parts[0]
      const masked = local.slice(0, 2) + '***'
      return masked + '@' + parts[1]
    }
    const phone = user.phone || ''
    if (phone.length > 4) {
      return phone.slice(0, 3) + '***' + phone.slice(-3)
    }
    return phone
  })
</script>

{#if inline}
  <div class="fixed inset-0 bg-black/60 z-[200] flex items-center justify-center p-4" transition:slide={{ duration: 200 }}>
    <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-sm" onclick={(e) => e.stopPropagation()}>
      <div class="text-center mb-6">
        <div class="w-16 h-16 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-3">
          <span class="text-3xl">🔐</span>
        </div>
        <h2 class="text-lg font-bold text-text-main">Verifikasi Akun</h2>
        <p class="text-sm text-on-surface-variant mt-1">
          Verifikasi melalui {gatewayLabel}
        </p>
        {#if user}
          <p class="text-xs text-on-surface-variant mt-1 font-medium">
            {maskedContact()}
          </p>
        {/if}
      </div>

      {#if error}
        <div transition:slide={{ duration: 200 }} class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-3 text-sm">
          {error}
        </div>
      {/if}

      {#if sendMessage}
        <div transition:slide={{ duration: 200 }} class="bg-primary-container text-black rounded-xl px-4 py-3 mb-3 text-sm">
          {sendMessage}
        </div>
      {/if}

      {#if !codeSent}
        <button
          type="button"
          class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
          onclick={handleSendCode}
          disabled={sending}
        >
          {#if sending}
            <span class="inline-flex items-center gap-2">
              <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Mengirim kode...
            </span>
          {:else}
            Kirim Kode Verifikasi
          {/if}
        </button>
      {:else}
        <form onsubmit={handleVerify} class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Masukkan Kode</label>
            <input
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="6"
              placeholder="000000"
              value={code}
              oninput={handleCodeInput}
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-center text-xl tracking-[0.5em] font-mono font-bold"
            />
            <p class="text-xs text-on-surface-variant mt-1 text-center">
              {#if timerSeconds > 0}
                Kode berlaku <span class="font-bold text-primary">{timerDisplay()}</span>
              {:else}
                Kode telah kedaluwarsa
              {/if}
            </p>
          </div>

          <button
            type="submit"
            class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
            disabled={loading || code.length !== 6 || timerSeconds <= 0}
          >
            {#if loading}
              <span class="inline-flex items-center gap-2">
                <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memverifikasi...
              </span>
            {:else}
              Verifikasi
            {/if}
          </button>

          <button
            type="button"
            class="w-full py-2 text-sm text-primary font-semibold hover:underline disabled:opacity-50"
            onclick={handleSendCode}
            disabled={sending || timerSeconds > 0}
          >
            {#if sending}
              Mengirim ulang...
            {:else}
              Kirim ulang kode
            {/if}
          </button>
        </form>
      {/if}

      <div class="mt-4 text-center">
        <button
          type="button"
          class="text-xs text-on-surface-variant hover:underline"
          onclick={() => { authStore.logout() }}
        >
          Kembali ke login
        </button>
      </div>
    </div>
  </div>
{:else}
  <div class="min-h-screen bg-canvas-cream flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
      <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">🔐</span>
        </div>
        <h1 class="text-2xl font-bold text-text-main">Verifikasi Akun</h1>
        <p class="text-sm text-on-surface-variant mt-1">
          Verifikasi akun Anda melalui {gatewayLabel}
        </p>
        {#if user}
          <p class="text-xs text-on-surface-variant mt-1 font-medium">
            {maskedContact()}
          </p>
        {/if}
      </div>

      {#if error}
        <div transition:slide={{ duration: 200 }} class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">
          {error}
        </div>
      {/if}

      {#if sendMessage}
        <div transition:slide={{ duration: 200 }} class="bg-primary-container text-black rounded-xl px-4 py-3 mb-4 text-sm">
          {sendMessage}
        </div>
      {/if}

      {#if !codeSent}
        <button
          type="button"
          class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
          onclick={handleSendCode}
          disabled={sending}
        >
          {#if sending}
            <span class="inline-flex items-center gap-2">
              <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Mengirim kode...
            </span>
          {:else}
            Kirim Kode Verifikasi
          {/if}
        </button>
      {:else}
        <form onsubmit={handleVerify} class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Masukkan Kode</label>
            <input
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              maxlength="6"
              placeholder="000000"
              value={code}
              oninput={handleCodeInput}
              class="w-full px-4 py-4 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-center text-2xl tracking-[0.5em] font-mono font-bold"
            />
            <p class="text-xs text-on-surface-variant mt-2 text-center">
              {#if timerSeconds > 0}
                Kode berlaku <span class="font-bold text-primary">{timerDisplay()}</span>
              {:else}
                Kode telah kedaluwarsa
              {/if}
            </p>
          </div>

          <button
            type="submit"
            class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
            disabled={loading || code.length !== 6 || timerSeconds <= 0}
          >
            {#if loading}
              <span class="inline-flex items-center gap-2">
                <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Memverifikasi...
              </span>
            {:else}
              Verifikasi
            {/if}
          </button>

          <button
            type="button"
            class="w-full py-2 text-sm text-primary font-semibold hover:underline disabled:opacity-50"
            onclick={handleSendCode}
            disabled={sending || timerSeconds > 0}
          >
            {#if sending}
              Mengirim ulang...
            {:else}
              Kirim ulang kode
            {/if}
          </button>
        </form>
      {/if}

      <div class="mt-6 text-center">
        <button
          type="button"
          class="text-sm text-on-surface-variant hover:underline"
          onclick={() => { authStore.logout() }}
        >
          Kembali ke login
        </button>
      </div>
    </div>
  </div>
{/if}
