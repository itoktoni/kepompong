<script>
  import { onMount } from 'svelte'
  import { slide, fade } from 'svelte/transition'
  import * as api from '../services/api.js'
  import { token, applyServerData } from '../stores/authStore.js'

  let { onsuccess, initialRegister = false, initialReferralCode = '' } = $props()

  const appName = import.meta.env.VITE_APP_NAME || 'Jejak Tumbuh'
  const appTagline = import.meta.env.VITE_APP_TAGLINE || 'Pendamping Anak'

  let isLogin = $state(!initialRegister)
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
  let showForgotPassword = $state(false)
  let forgotEmail = $state('')
  let forgotLoading = $state(false)
  let forgotMessage = $state('')
  let forgotError = $state('')
  let forgotGateway = $state('email')

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

  onMount(async () => {
    const savedToken = localStorage.getItem('lk_pending_token')
    if (savedToken) {
      pendingToken = savedToken
      needsVerify = true
      verifyCodeSent = localStorage.getItem('lk_verify_code_sent') === 'true'
      try {
        const config = await api.getConfig()
        verifyGateway = config.verification_gateway || 'email'
        localStorage.setItem('lk_verify_gateway', verifyGateway)
      } catch {
        verifyGateway = localStorage.getItem('lk_verify_gateway') || 'email'
      }
    }
  })

  function handleTabChange(login) {
    if (isLogin === login) return
    isLogin = login
    error = ''
    validationErrors = null
  }

  async function handleSubmit(e) {
    if (e) e.preventDefault()
    loading = true
    error = ''
    validationErrors = null

    try {
      let data
      if (isLogin) {
        data = await api.login(email, password)
      } else {
        const ref = referralCode || localStorage.getItem('lk_ref_code') || ''
        data = await api.register(name, email, phone, password, passwordConfirmation, ref, null)
      }

      if (data.needs_verification) {
        pendingToken = data.access_token
        verifyGateway = data.verification_gateway || 'email'
        needsVerify = true
        verifyCodeSent = false
        loading = false
        api.clearAuthToken()
        localStorage.setItem('lk_pending_token', pendingToken)
        localStorage.setItem('lk_verify_gateway', verifyGateway)
        localStorage.removeItem('lk_verify_code_sent')
        return
      }

      if (data.access_token) {
        token.set(data.access_token)
        // Also save to localStorage immediately to ensure persistence
        if (typeof localStorage !== 'undefined') localStorage.setItem('lk_auth_token', data.access_token)
      }
      applyServerData(data)
      if (onsuccess) onsuccess(data)
    } catch (err) {
      error = err.message || 'Terjadi kesalahan'
      validationErrors = err.errors || null
    } finally {
      loading = false
    }
  }

  async function handleSendCode() {
    verifySending = true
    verifySendMessage = ''
    verifyError = ''
    try {
      api.setAuthTokenMemory(pendingToken)
      await api.sendVerification(verifyGateway)
      verifyCodeSent = true
      localStorage.setItem('lk_verify_code_sent', 'true')
      verifySendMessage = 'Kode verifikasi telah dikirim!'
      startTimer()
    } catch (err) {
      verifyCodeSent = true
      localStorage.setItem('lk_verify_code_sent', 'true')
      if (err.status === 429 && err.cooldown) {
        verifyError = err.message
        startTimerWith(err.cooldown)
      } else {
        verifyError = err.message || 'Gagal mengirim kode'
      }
    } finally {
      verifySending = false
    }
  }

  async function handleVerify(e) {
    if (e) e.preventDefault()
    if (verifyCode.length !== 6) return
    verifyLoading = true
    verifyError = ''
    try {
      api.setAuthTokenMemory(pendingToken)
      const data = await api.verifyCode(verifyCode)
      api.setAuthToken(pendingToken)
      token.set(pendingToken)
      // Ensure token is saved to localStorage for persistence
      if (typeof localStorage !== 'undefined') localStorage.setItem('lk_auth_token', pendingToken)
      localStorage.removeItem('lk_pending_token')
      localStorage.removeItem('lk_verify_gateway')
      localStorage.removeItem('lk_verify_code_sent')
      applyServerData(data)
      if (onsuccess) onsuccess(data)
    } catch (err) {
      verifyError = err.message || 'Kode tidak valid'
    } finally {
      verifyLoading = false
    }
  }

  function handleVerifyInput(e) {
    verifyCode = e.target.value.replace(/\D/g, '').slice(0, 6)
  }

  function handleBackToLogin() {
    needsVerify = false
    pendingToken = ''
    verifyCode = ''
    verifyCodeSent = false
    verifyError = ''
    verifySendMessage = ''
    stopTimer()
    api.clearAuthToken()
    localStorage.removeItem('lk_pending_token')
    localStorage.removeItem('lk_verify_gateway')
    localStorage.removeItem('lk_verify_code_sent')
  }

  async function openForgotPassword() {
    showForgotPassword = true
    forgotEmail = email
    forgotMessage = ''
    forgotError = ''
    try {
      const config = await api.getConfig()
      forgotGateway = config.forgot_gateway || 'email'
    } catch {
      forgotGateway = 'email'
    }
  }

  async function handleForgotPassword(e) {
    if (e) e.preventDefault()
    forgotLoading = true
    forgotMessage = ''
    forgotError = ''
    try {
      const body = forgotGateway === 'whatsapp' ? { phone: forgotEmail } : { email: forgotEmail }
      const data = await api.forgotPassword(body)
      forgotMessage = data.message || 'Link reset password telah dikirim.'
    } catch (err) {
      forgotError = err.message || 'Gagal mengirim link reset password'
    } finally {
      forgotLoading = false
    }
  }
</script>

<div class="min-h-screen bg-canvas-cream flex items-center justify-center p-4">
  <div class="w-full max-w-sm">

    {#if needsVerify}
      <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">🔐</span>
        </div>
        <h1 class="text-2xl font-bold text-text-main">Verifikasi Akun</h1>
        <p class="text-sm text-on-surface-variant mt-1">
          Verifikasi melalui {verifyGateway === 'whatsapp' ? 'WhatsApp' : verifyGateway === 'telegram' ? 'Telegram' : 'Email'}
        </p>
      </div>

      {#if verifyError}
        <div transition:slide={{ duration: 200 }} class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">
          {verifyError}
        </div>
      {/if}

      {#if verifySendMessage}
        <div transition:slide={{ duration: 200 }} class="bg-primary-container text-black rounded-xl px-4 py-3 mb-4 text-sm">
          {verifySendMessage}
        </div>
      {/if}

      {#if !verifyCodeSent}
        <button
          type="button"
          class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50"
          onclick={handleSendCode}
          disabled={verifySending}
        >
          {#if verifySending}
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
              value={verifyCode}
              oninput={handleVerifyInput}
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
            disabled={verifyLoading || verifyCode.length !== 6 || timerSeconds <= 0}
          >
            {#if verifyLoading}
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
            disabled={verifySending || timerSeconds > 0}
          >
            {#if verifySending}
              Mengirim ulang...
            {:else}
              Kirim ulang kode
            {/if}
          </button>
        </form>
      {/if}

      <div class="mt-6 text-center">
        <button type="button" class="text-sm text-on-surface-variant hover:underline" onclick={handleBackToLogin}>
          Kembali ke login
        </button>
      </div>

    {:else}
      <div class="text-center mb-8">
        <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
          <span class="text-4xl">👣</span>
        </div>
        <h1 class="text-2xl font-bold text-text-main">{appName}</h1>
        <p class="text-sm text-on-surface-variant mt-1">{appTagline}</p>
      </div>

      <div class="flex bg-white rounded-xl p-1 mb-6 border-2 border-[#B7D9BC]">
        <button
          class="flex-1 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 {isLogin ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-gray-100'}"
          onclick={() => handleTabChange(true)}
        >
          Masuk
        </button>
        <button
          class="flex-1 py-2.5 rounded-lg text-sm font-bold transition-all duration-200 {!isLogin ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-gray-100'}"
          onclick={() => handleTabChange(false)}
        >
          Daftar
        </button>
      </div>

      {#if error}
        <div transition:slide={{ duration: 200 }} class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">
          {error}
        </div>
      {/if}

      <form onsubmit={(e) => { e.preventDefault(); handleSubmit() }} class="space-y-3">
        {#if !isLogin}
          <div transition:slide={{ duration: 250 }}>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input
              type="text"
              placeholder="Masukkan nama"
              bind:value={name}
              required
              class="w-full px-4 py-3 rounded-xl border-2 {validationErrors?.name ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
            />
            {#if validationErrors?.name?.[0]}
              <p class="text-xs text-error mt-1">{validationErrors.name[0]}</p>
            {/if}
          </div>
        {/if}

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            type="email"
            placeholder="email@contoh.com"
            bind:value={email}
            required
            class="w-full px-4 py-3 rounded-xl border-2 {validationErrors?.email ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
          />
          {#if validationErrors?.email?.[0]}
            <p class="text-xs text-error mt-1">{validationErrors.email[0]}</p>
          {/if}
        </div>

        {#if !isLogin}
          <div transition:slide={{ duration: 250 }}>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
            <input
              type="tel"
              placeholder="08xxxxxxxxxx"
              bind:value={phone}
              class="w-full px-4 py-3 rounded-xl border-2 {validationErrors?.phone ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
            />
            {#if validationErrors?.phone?.[0]}
              <p class="text-xs text-error mt-1">{validationErrors.phone[0]}</p>
            {/if}
          </div>
        {/if}

        {#if isLogin}
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <input
                type={showPassword ? 'text' : 'password'}
                placeholder="••••••••"
                bind:value={password}
                required
                class="w-full px-4 py-3 pr-12 rounded-xl border-2 {validationErrors?.password ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
              />
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" onclick={() => { showPassword = !showPassword }}>
                {#if showPassword}
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M11.83 9L15 12.16V12a3 3 0 0 0-3-3c-.55 0-1.05.14-1.5.37L10.7 8.05c-.2-.07-.42-.11-.64-.13V8.05m2.57 2.18L9.81 7.64A3 3 0 0 0 12 7c1.66 0 3 1.34 3 3c0 .55-.14 1.05-.37 1.5L14.4 13.07c.2.07.42.11.64.13M2.04 3L3.46 4.41l1.71 1.71C4.17 7.29 3.38 8.73 3.12 10c-.52 2.5.53 4.87 2.18 6.65l1.71 1.71C9.81 20.39 12.76 21 16 21c1.37 0 2.69-.25 3.92-.71L21.59 22.7L23 21.29l-9-9L2.04 3M12 7c-1.66 0-3 1.34-3 3c0 .29.04.57.11.84l5.73 5.73c.07-.27.11-.55.11-.84c0-1.66-1.34-3-3-3m11.16 9.64L19.72 15.2c.66-1.19 1.05-2.53 1.05-3.94c0-3.6-2.71-6.57-6.22-6.93c-.22-.02-.44-.03-.65-.04C12.16 4.29 10.25 5 8.54 6.16L3.61 1.23L2.04 3l3.46 3.46c1.42 1.42 3.33 2.13 5.22 2.33c-.31.37-.56.78-.74 1.22C9.33 10.6 9 11.28 9 12c0 1.66 1.34 3 3 3c.72 0 1.4-.33 1.99-.86c.44-.18.85-.43 1.22-.74c.2.24.42.47.66.69L19.72 15.2l1.44 1.44z"/></svg>
                {:else}
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
                {/if}
              </button>
            </div>
            {#if validationErrors?.password?.[0]}
              <p class="text-xs text-error mt-1">{validationErrors.password[0]}</p>
            {/if}
          </div>
        {/if}

        {#if isLogin}
          <div transition:slide={{ duration: 200 }} class="text-right -mt-1">
            <button type="button" class="text-sm text-primary font-semibold hover:underline" onclick={openForgotPassword}>
              Lupa password?
            </button>
          </div>
        {/if}

        {#if !isLogin}
          <div class="grid grid-cols-2 gap-3" transition:slide={{ duration: 250 }}>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <div class="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  placeholder="••••••••"
                  bind:value={password}
                  required
                  class="w-full px-4 py-3 pr-12 rounded-xl border-2 {validationErrors?.password ? 'border-error' : 'border-[#B7D9BC]'} focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
                />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" onclick={() => { showPassword = !showPassword }}>
                  {#if showPassword}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M11.83 9L15 12.16V12a3 3 0 0 0-3-3c-.55 0-1.05.14-1.5.37L10.7 8.05c-.2-.07-.42-.11-.64-.13V8.05m2.57 2.18L9.81 7.64A3 3 0 0 0 12 7c1.66 0 3 1.34 3 3c0 .55-.14 1.05-.37 1.5L14.4 13.07c.2.07.42.11.64.13M2.04 3L3.46 4.41l1.71 1.71C4.17 7.29 3.38 8.73 3.12 10c-.52 2.5.53 4.87 2.18 6.65l1.71 1.71C9.81 20.39 12.76 21 16 21c1.37 0 2.69-.25 3.92-.71L21.59 22.7L23 21.29l-9-9L2.04 3M12 7c-1.66 0-3 1.34-3 3c0 .29.04.57.11.84l5.73 5.73c.07-.27.11-.55.11-.84c0-1.66-1.34-3-3-3m11.16 9.64L19.72 15.2c.66-1.19 1.05-2.53 1.05-3.94c0-3.6-2.71-6.57-6.22-6.93c-.22-.02-.44-.03-.65-.04C12.16 4.29 10.25 5 8.54 6.16L3.61 1.23L2.04 3l3.46 3.46c1.42 1.42 3.33 2.13 5.22 2.33c-.31.37-.56.78-.74 1.22C9.33 10.6 9 11.28 9 12c0 1.66 1.34 3 3 3c.72 0 1.4-.33 1.99-.86c.44-.18.85-.43 1.22-.74c.2.24.42.47.66.69L19.72 15.2l1.44 1.44z"/></svg>
                  {:else}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
                  {/if}
                </button>
              </div>
              {#if validationErrors?.password?.[0]}
                <p class="text-xs text-error mt-1">{validationErrors.password[0]}</p>
              {/if}
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi</label>
              <div class="relative">
                <input
                  type={showPasswordConfirm ? 'text' : 'password'}
                  placeholder="••••••••"
                  bind:value={passwordConfirmation}
                  required
                  class="w-full px-4 py-3 pr-12 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
                />
                <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition" onclick={() => { showPasswordConfirm = !showPasswordConfirm }}>
                  {#if showPasswordConfirm}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M11.83 9L15 12.16V12a3 3 0 0 0-3-3c-.55 0-1.05.14-1.5.37L10.7 8.05c-.2-.07-.42-.11-.64-.13V8.05m2.57 2.18L9.81 7.64A3 3 0 0 0 12 7c1.66 0 3 1.34 3 3c0 .55-.14 1.05-.37 1.5L14.4 13.07c.2.07.42.11.64.13M2.04 3L3.46 4.41l1.71 1.71C4.17 7.29 3.38 8.73 3.12 10c-.52 2.5.53 4.87 2.18 6.65l1.71 1.71C9.81 20.39 12.76 21 16 21c1.37 0 2.69-.25 3.92-.71L21.59 22.7L23 21.29l-9-9L2.04 3M12 7c-1.66 0-3 1.34-3 3c0 .29.04.57.11.84l5.73 5.73c.07-.27.11-.55.11-.84c0-1.66-1.34-3-3-3m11.16 9.64L19.72 15.2c.66-1.19 1.05-2.53 1.05-3.94c0-3.6-2.71-6.57-6.22-6.93c-.22-.02-.44-.03-.65-.04C12.16 4.29 10.25 5 8.54 6.16L3.61 1.23L2.04 3l3.46 3.46c1.42 1.42 3.33 2.13 5.22 2.33c-.31.37-.56.78-.74 1.22C9.33 10.6 9 11.28 9 12c0 1.66 1.34 3 3 3c.72 0 1.4-.33 1.99-.86c.44-.18.85-.43 1.22-.74c.2.24.42.47.66.69L19.72 15.2l1.44 1.44z"/></svg>
                  {:else}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
                  {/if}
                </button>
              </div>
            </div>
          </div>
        {/if}

        {#if !isLogin}
          <div transition:slide={{ duration: 250 }}>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Referral (opsional)</label>
            <input
              type="text"
              placeholder="Masukkan kode"
              bind:value={referralCode}
              oninput={() => referralCode = referralCode.toUpperCase()}
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white uppercase"
            />
          </div>
        {/if}

        <button
          type="submit"
          class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50 mt-2"
          disabled={loading}
        >
          {#if loading}
            <span class="inline-flex items-center gap-2">
              <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              {isLogin ? 'Masuk...' : 'Mendaftar...'}
            </span>
          {:else}
            <span>{isLogin ? 'Masuk' : 'Daftar'}</span>
          {/if}
        </button>
      </form>
    {/if}
  </div>
</div>

{#if showForgotPassword}
  <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" transition:fade={{ duration: 200 }} onclick={() => { showForgotPassword = false }}>
    <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-sm" transition:slide={{ duration: 250, y: 20 }} onclick={(e) => e.stopPropagation()}>
      <h3 class="text-lg font-bold text-text-main mb-2">Reset Password</h3>
      <p class="text-sm text-on-surface-variant mb-4">
        {#if forgotGateway === 'whatsapp'}
          Masukkan nomor HP Anda untuk menerima link reset via WhatsApp.
        {:else}
          Masukkan email Anda untuk menerima link reset password.
        {/if}
      </p>

      {#if forgotMessage}
        <div transition:slide={{ duration: 200 }} class="bg-primary-container text-black rounded-xl px-4 py-3 mb-4 text-xs">{forgotMessage}</div>
      {/if}
      {#if forgotError}
        <div transition:slide={{ duration: 200 }} class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">{forgotError}</div>
      {/if}

      <form onsubmit={handleForgotPassword} class="space-y-3">
        <div>
          {#if forgotGateway === 'whatsapp'}
            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
            <input
              type="tel"
              placeholder="08xxxxxxxxxx"
              bind:value={forgotEmail}
              required
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
            />
          {:else}
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              type="email"
              placeholder="email@contoh.com"
              bind:value={forgotEmail}
              required
              class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition bg-white"
            />
          {/if}
        </div>
        <div class="flex gap-3">
          <button type="button" class="flex-1 px-4 py-3 rounded-xl border-2 border-[#B7D9BC] text-text-main font-semibold hover:bg-surface-variant transition-colors" onclick={() => { showForgotPassword = false }}>
            Batal
          </button>
          <button type="submit" class="flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50" disabled={forgotLoading}>
            {#if forgotLoading}
              <span class="inline-flex items-center gap-2">
                <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Mengirim...
              </span>
            {:else}
              Kirim Link
            {/if}
          </button>
        </div>
      </form>
    </div>
  </div>
{/if}
