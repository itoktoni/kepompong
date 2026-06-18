<script>
  import { page } from '$app/stores'
  import { slide, fade } from 'svelte/transition'
  import * as api from '../../lib/services/api.js'

  const appName = import.meta.env.VITE_APP_NAME || 'Jejak Tumbuh'

  let token = $state('')
  let email = $state('')
  let password = $state('')
  let passwordConfirmation = $state('')
  let showPassword = $state(false)
  let showPasswordConfirm = $state(false)
  let loading = $state(false)
  let success = $state(false)
  let error = $state('')
  let validationErrors = $state(null)

  $effect(() => {
    let search = $page.url.search
    if (search.includes('&amp;')) {
      search = search.replace(/&amp;/g, '&')
    }
    const params = new URLSearchParams(search)
    token = params.get('token') || ''
    email = params.get('email') || ''
  })

  async function handleSubmit(e) {
    if (e) e.preventDefault()
    loading = true
    error = ''
    validationErrors = null

    try {
      await api.resetPassword(token, email, password, passwordConfirmation)
      success = true
    } catch (err) {
      error = err.message || 'Gagal reset password'
      validationErrors = err.errors || null
    } finally {
      loading = false
    }
  }
</script>

<div class="min-h-screen bg-canvas-cream flex items-center justify-center p-4">
  <div class="w-full max-w-sm">
    <div class="text-center mb-8">
      <div class="w-20 h-20 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
        <span class="text-4xl">👣</span>
      </div>
      <h1 class="text-2xl font-bold text-text-main">{appName}</h1>
      <p class="text-sm text-on-surface-variant mt-1">Reset Password</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
      {#if success}
        <div transition:slide={{ duration: 250 }} class="text-center py-4">
          <div class="w-16 h-16 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
            <span class="text-3xl text-primary">✅</span>
          </div>
          <h3 class="text-lg font-bold text-text-main mb-2">Password Berhasil Diubah</h3>
          <p class="text-sm text-on-surface-variant mb-4">Silakan login dengan password baru Anda.</p>
          <a href="/" class="inline-block w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform text-center">
            Login
          </a>
        </div>
      {:else}
        {#if error}
          <div transition:slide={{ duration: 200 }} class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">
            {error}
          </div>
        {/if}

        {#if !token}
          <div class="bg-error-container text-on-error-container rounded-xl px-4 py-3 mb-4 text-sm">
            Token reset password tidak valid atau sudah kedaluwarsa.
          </div>
        {:else}
          <form onsubmit={handleSubmit} class="space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                type="email"
                value={email}
                disabled
                class="w-full px-4 py-3 rounded-xl border-2 border-[#B7D9BC] bg-gray-50 text-gray-500 outline-none"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M11.83 9L15 12.16C15 12.11 15 12.05 15 12a3 3 0 0 0-3-3c-.55 0-1.05.14-1.5.37L10.7 8.05c-.2-.07-.42-.11-.64-.13V8.05m2.57 2.18L9.81 7.64A3 3 0 0 0 12 7c1.66 0 3 1.34 3 3c0 .55-.14 1.05-.37 1.5L14.4 13.07c.2.07.42.11.64.13M2.04 3L3.46 4.41l1.71 1.71C4.17 7.29 3.38 8.73 3.12 10c-.52 2.5.53 4.87 2.18 6.65l1.71 1.71C9.81 20.39 12.76 21 16 21c1.37 0 2.69-.25 3.92-.71L21.59 22.7L23 21.29l-9-9L2.04 3M12 7c-1.66 0-3 1.34-3 3c0 .29.04.57.11.84l5.73 5.73c.07-.27.11-.55.11-.84c0-1.66-1.34-3-3-3m11.16 9.64L19.72 15.2c.66-1.19 1.05-2.53 1.05-3.94c0-3.6-2.71-6.57-6.22-6.93c-.22-.02-.44-.03-.65-.04C12.16 4.29 10.25 5 8.54 6.16L3.61 1.23L2.04 3l3.46 3.46c1.42 1.42 3.33 2.13 5.22 2.33c-.31.37-.56.78-.74 1.22C9.33 10.6 9 11.28 9 12c0 1.66 1.34 3 3 3c.72 0 1.4-.33 1.99-.86c.44-.18.85-.43 1.22-.74c.2.24.42.47.66.69L19.72 15.2l1.44 1.44z"/></svg>
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
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M11.83 9L15 12.16C15 12.11 15 12.05 15 12a3 3 0 0 0-3-3c-.55 0-1.05.14-1.5.37L10.7 8.05c-.2-.07-.42-.11-.64-.13V8.05m2.57 2.18L9.81 7.64A3 3 0 0 0 12 7c1.66 0 3 1.34 3 3c0 .55-.14 1.05-.37 1.5L14.4 13.07c.2.07.42.11.64.13M2.04 3L3.46 4.41l1.71 1.71C4.17 7.29 3.38 8.73 3.12 10c-.52 2.5.53 4.87 2.18 6.65l1.71 1.71C9.81 20.39 12.76 21 16 21c1.37 0 2.69-.25 3.92-.71L21.59 22.7L23 21.29l-9-9L2.04 3M12 7c-1.66 0-3 1.34-3 3c0 .29.04.57.11.84l5.73 5.73c.07-.27.11-.55.11-.84c0-1.66-1.34-3-3-3m11.16 9.64L19.72 15.2c.66-1.19 1.05-2.53 1.05-3.94c0-3.6-2.71-6.57-6.22-6.93c-.22-.02-.44-.03-.65-.04C12.16 4.29 10.25 5 8.54 6.16L3.61 1.23L2.04 3l3.46 3.46c1.42 1.42 3.33 2.13 5.22 2.33c-.31.37-.56.78-.74 1.22C9.33 10.6 9 11.28 9 12c0 1.66 1.34 3 3 3c.72 0 1.4-.33 1.99-.86c.44-.18.85-.43 1.22-.74c.2.24.42.47.66.69L19.72 15.2l1.44 1.44z"/></svg>
                  {:else}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"><path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/></svg>
                  {/if}
                </button>
              </div>
            </div>

            <button
              type="submit"
              class="w-full py-3 bg-primary text-on-primary rounded-xl font-bold active:scale-95 transition-transform disabled:opacity-50 mt-2"
              disabled={loading || !token}
            >
              {#if loading}
                <span class="inline-flex items-center gap-2">
                  <svg class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                  Memproses...
                </span>
              {:else}
                <span>Reset Password</span>
              {/if}
            </button>
          </form>
        {/if}
      {/if}
    </div>
  </div>
</div>
