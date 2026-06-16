<script>
  let { referralCode = '' } = $props()

  const appName = import.meta.env.VITE_APP_NAME || 'Jejak Tumbuh'
  const appTagline = import.meta.env.VITE_APP_TAGLINE || 'Pendamping Anak'

  function handleRegister() {
    if (referralCode) {
      localStorage.setItem('lk_ref_code', referralCode)
    }
    window.location.href = '/?action=register'
  }
</script>

<div class="min-h-screen flex items-center justify-center p-4" style="background: linear-gradient(135deg, #E1F2E5 0%, #FFF9F3 50%, #d4f0d8 100%)">
  <div class="relative w-full max-w-sm bg-white rounded-[32px] shadow-2xl overflow-hidden border-4 border-[#B7D9BC]">

    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full opacity-20" style="background: #176c33"></div>
    <div class="absolute -bottom-8 -left-8 w-24 h-24 rounded-full opacity-15" style="background: #6DBE7B"></div>
    <div class="absolute top-1/2 -right-6 w-16 h-16 rounded-full opacity-10" style="background: #B7D9BC"></div>

    <div class="relative z-10 p-8 text-center">

      <div class="flex items-center justify-center gap-2 mb-6">
        <span class="text-3xl">🌸</span>
        <h1 class="text-xl font-bold text-primary">{appName}</h1>
      </div>

      <div class="text-5xl mb-4">🎉</div>

      <h2 class="text-2xl font-bold text-text-main mb-3 tracking-wide">KAMU DIUNDANG!</h2>

      <p class="text-sm text-on-surface-variant leading-relaxed mb-6">
        Temanmu membagikan progress anaknya dari aplikasi <strong class="text-primary">{appName}</strong>.
        Yuk ikutan bantu perkembangan anakmu juga!
      </p>

      {#if referralCode}
        <div class="bg-primary-container rounded-2xl p-4 mb-6 border-2 border-primary/20">
          <p class="text-[10px] text-on-surface-variant uppercase tracking-wider font-bold mb-1">Kode Referral</p>
          <p class="text-2xl font-bold text-primary tracking-[0.3em]">{referralCode}</p>
        </div>
      {/if}

      <button onclick={handleRegister}
        class="w-full py-4 rounded-2xl text-white font-bold text-base bg-primary hover:bg-primary/90 active:scale-95 transition-all shadow-lg shadow-primary/30">
        Daftar {appName} 🚀
      </button>

      <p class="text-xs text-on-surface-variant/60 mt-4">{appTagline} #1 di Indonesia</p>
    </div>
  </div>
</div>
