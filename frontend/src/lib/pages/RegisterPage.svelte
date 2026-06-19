<script>
  import { onMount } from 'svelte'
  import { slide } from 'svelte/transition'
  import * as api from '../services/api.js'
  import { token, applyServerData } from '../stores/authStore.js'

  let { onsuccess, initialReferralCode = '' } = $props()

  let loading = $state(false)
  let error = $state('')
  let validationErrors = $state(null)

  let name = $state('')
  let email = $state('')
  let phone = $state('')
  let password = $state('')
  let passwordConfirmation = $state('')
  let referralCode = $state(initialReferralCode || (typeof localStorage !== 'undefined' ? localStorage.getItem('lk_ref_code') || '' : ''))
  let showPassword = $state(false)
  let showPasswordConfirm = $state(false)
  let trialParam = $state(null)

  let needsVerify = $state(false)
  let verifyGateway = $state('email')
  let verifyCode = $state('')
  let verifyLoading = $state(false)
  let verifySending = $state(false)
  let verifyError = $state('')
  let verifySendMessage = $state('')
  let verifyCodeSent = $state(false)
  let pendingToken = $state('')
  let timerSeconds = $state(0)
  let timerInterval = $state(null)

  const timerDisplay = $derived(() => {
    const m = Math.floor(timerSeconds / 60)
    const s = timerSeconds % 60
    return `${m}:${s.toString().padStart(2, '0')}`
  })

  function startTimer(seconds = 600) {
    timerSeconds = seconds
    if (timerInterval) clearInterval(timerInterval)
    timerInterval = setInterval(() => {
      timerSeconds--
      if (timerSeconds <= 0) { clearInterval(timerInterval); timerInterval = null }
    }, 1000)
  }

  onMount(async () => {
    const urlParams = new URLSearchParams(window.location.search)
    const t = urlParams.get('trial')
    if (t && Number(t) > 0) trialParam = Number(t)

    const savedToken = localStorage.getItem('lk_pending_token')
    if (savedToken) {
      pendingToken = savedToken
      needsVerify = true
      try {
        const config = await api.getConfig()
        verifyGateway = config.verification_gateway || 'email'
      } catch {
        verifyGateway = localStorage.getItem('lk_verify_gateway') || 'email'
      }
    }
  })

  async function handleRegister(e) {
    if (e) e.preventDefault()
    if (!name.trim()) { error = 'Nama wajib diisi'; return }
    if (!email.trim()) { error = 'Email wajib diisi'; return }
    if (!password) { error = 'Password wajib diisi'; return }
    if (password !== passwordConfirmation) { error = 'Konfirmasi password tidak cocok'; return }

    loading = true; error = ''; validationErrors = null
    try {
      const ref = referralCode || localStorage.getItem('lk_ref_code') || ''
      const data = await api.register(name, email, phone, password, passwordConfirmation, ref, trialParam)

      if (data.needs_verification) {
        pendingToken = data.access_token
        verifyGateway = data.verification_gateway || 'email'
        needsVerify = true
        verifyCodeSent = false
        api.clearAuthToken()
        localStorage.setItem('lk_pending_token', pendingToken)
        localStorage.setItem('lk_verify_gateway', verifyGateway)
        loading = false
        return
      }

      if (data.access_token) {
        token.set(data.access_token)
        localStorage.setItem('lk_auth_token', data.access_token)
      }
      applyServerData(data)
      onsuccess?.()
    } catch (err) {
      error = err.message || 'Terjadi kesalahan'
      validationErrors = err.errors || null
    }
    loading = false
  }

  async function handleSendCode() {
    verifySending = true; verifyError = ''
    try {
      api.setAuthTokenMemory(pendingToken)
      await api.sendVerification(verifyGateway)
      verifyCodeSent = true
      localStorage.setItem('lk_verify_code_sent', 'true')
      verifySendMessage = 'Kode verifikasi telah dikirim!'
      startTimer()
    } catch (err) {
      verifyCodeSent = true
      if (err.status === 429 && err.cooldown) {
        verifyError = err.message
        startTimer(err.cooldown)
      } else {
        verifyError = err.message || 'Gagal mengirim kode'
      }
    }
    verifySending = false
  }

  async function handleVerify(e) {
    if (e) e.preventDefault()
    if (verifyCode.length !== 6) return
    verifyLoading = true; verifyError = ''
    try {
      api.setAuthTokenMemory(pendingToken)
      const data = await api.verifyCode(verifyCode)
      api.setAuthToken(pendingToken)
      token.set(pendingToken)
      localStorage.setItem('lk_auth_token', pendingToken)
      localStorage.removeItem('lk_pending_token')
      localStorage.removeItem('lk_verify_gateway')
      localStorage.removeItem('lk_verify_code_sent')
      applyServerData(data)
      needsVerify = false
      onsuccess?.()
    } catch (err) {
      verifyError = err.message || 'Kode tidak valid'
    }
    verifyLoading = false
  }

  function handleVerifyInput(e) {
    verifyCode = e.target.value.replace(/\D/g, '').slice(0, 6)
  }
</script>

<div class="min-h-screen bg-canvas-cream flex items-center justify-center p-4">
  <div class="w-full max-w-md">

    {#if needsVerify}
      <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-primary"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" /></svg>
        </div>
        <h1 class="text-2xl font-bold text-text-main">Verifikasi Akun</h1>
        <p class="text-sm text-on-surface-variant mt-1">
          Verifikasi melalui {verifyGateway === 'whatsapp' ? 'WhatsApp' : verifyGateway === 'telegram' ? 'Telegram' : 'Email'}
        </p>
      </div>

      {#if verifyError}
        <div transition:slide class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{verifyError}</div>
      {/if}
      {#if verifySendMessage}
        <div transition:slide class="bg-primary-container text-black rounded-xl px-4 py-3 mb-4 text-sm">{verifySendMessage}</div>
      {/if}

      {#if !verifyCodeSent}
        <button class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
          onclick={handleSendCode} disabled={verifySending}>
          {verifySending ? 'Mengirim kode...' : 'Kirim Kode Verifikasi'}
        </button>
      {:else}
        <form onsubmit={handleVerify} class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Masukkan Kode</label>
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="000000"
              value={verifyCode} oninput={handleVerifyInput}
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white text-center text-xl tracking-[0.5em] font-mono font-bold" />
            <p class="text-xs text-on-surface-variant mt-1 text-center">
              {#if timerSeconds > 0}Kode berlaku <span class="font-bold text-primary">{timerDisplay()}</span>
              {:else}Kode telah kedaluwarsa{/if}
            </p>
          </div>
          <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
            disabled={verifyLoading || verifyCode.length !== 6 || timerSeconds <= 0}>
            {verifyLoading ? 'Memverifikasi...' : 'Verifikasi'}
          </button>
          <button type="button" class="w-full py-2 text-sm text-primary font-semibold hover:underline disabled:opacity-50"
            onclick={handleSendCode} disabled={verifySending || timerSeconds > 0}>
            {verifySending ? 'Mengirim ulang...' : 'Kirim ulang kode'}
          </button>
        </form>
      {/if}

    {:else}
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-text-main">Buat Akun</h1>
        <p class="text-sm text-on-surface-variant mt-1">Daftar dan mulai trial gratis</p>
      </div>

      {#if error}
        <div transition:slide class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{error}</div>
      {/if}

      <form onsubmit={handleRegister} class="space-y-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
          <input type="text" placeholder="Masukkan nama" bind:value={name} required
            class="w-full px-4 py-3 rounded-xl border-2 {validationErrors?.name ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white" />
          {#if validationErrors?.name?.[0]}<p class="text-xs text-error mt-1">{validationErrors.name[0]}</p>{/if}
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input type="email" placeholder="email@contoh.com" bind:value={email} required
            class="w-full px-4 py-3 rounded-xl border-2 {validationErrors?.email ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white" />
          {#if validationErrors?.email?.[0]}<p class="text-xs text-error mt-1">{validationErrors.email[0]}</p>{/if}
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
          <input type="tel" placeholder="08xxxxxxxxxx" bind:value={phone}
            class="w-full px-4 py-3 rounded-xl border-2 {validationErrors?.phone ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white" />
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <input type={showPassword ? 'text' : 'password'} placeholder="••••••••" bind:value={password} required
                class="w-full px-4 py-3 pr-10 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white" />
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" onclick={() => showPassword = !showPassword}>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d={showPassword ? 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88' : 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z'} /><path stroke-linecap="round" stroke-linejoin="round" d={showPassword ? '' : 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'} /></svg>
              </button>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi</label>
            <div class="relative">
              <input type={showPasswordConfirm ? 'text' : 'password'} placeholder="••••••••" bind:value={passwordConfirmation} required
                class="w-full px-4 py-3 pr-10 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white" />
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" onclick={() => showPasswordConfirm = !showPasswordConfirm}>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d={showPasswordConfirm ? 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88' : 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z'} /><path stroke-linecap="round" stroke-linejoin="round" d={showPasswordConfirm ? '' : 'M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z'} /></svg>
              </button>
            </div>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kode Referral (opsional)</label>
          <input type="text" placeholder="Masukkan kode" bind:value={referralCode}
            class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white" />
        </div>
        <button type="submit" class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50 mt-2"
          disabled={loading}>
          {loading ? 'Mendaftar...' : 'Daftar Gratis'}
        </button>
      </form>

      <p class="text-center text-sm text-on-surface-variant mt-4">
        Sudah punya akun? <a href="/" class="text-primary font-semibold hover:underline">Masuk</a>
      </p>
    {/if}
  </div>
</div>
