<script>
  import '../app.css'
  import { onMount } from 'svelte'

  onMount(async () => {
    const isNgrok = location.host.includes('ngrok')
    if (isNgrok) {
      document.cookie = 'ngrok-skip-browser-warning=true; path=/; max-age=86400'
    }

    if ('serviceWorker' in navigator) {
      try {
        const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' })
        console.log('[SW] Registered:', reg.scope)
      } catch (e) {
        console.warn('[SW] Registration failed:', e.message)
      }
    }
  })
</script>

<slot />
