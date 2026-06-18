import { writable } from 'svelte/store'
import { onMount } from 'svelte'
import { appConfig } from '../config/appConfig.js'

const deferredPrompt = writable(null)
export const canInstall = writable(false)

export function initInstall() {
  if (typeof window === 'undefined') return

  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true
  const isNgrok = location.host.includes('ngrok')
  console.log('[PWA] initInstall', {
    isStandalone,
    isIOS: isIOS(),
    isNgrok,
    protocol: location.protocol,
    host: location.host,
    href: location.href
  })

  if (isStandalone) {
    console.log('[PWA] Already standalone, skip')
    return
  }

  if (isNgrok) {
    console.log('[PWA] Ngrok detected! Checking ngrok interstitial...')
    const ngrokCookie = document.cookie.includes('ngrok-skip-browser-warning')
    console.log('[PWA] ngrok-skip-browser-warning cookie:', ngrokCookie)
    if (!ngrokCookie) {
      console.log('[PWA] Setting ngrok bypass cookie')
      document.cookie = 'ngrok-skip-browser-warning=true; path=/; max-age=86400'
    }
  }

  window.addEventListener('beforeinstallprompt', (e) => {
    console.log('[PWA] beforeinstallprompt fired!', e)
    e.preventDefault()
    deferredPrompt.set(e)
    canInstall.set(true)
  })

  window.addEventListener('appinstalled', () => {
    console.log('[PWA] appinstalled event')
    deferredPrompt.set(null)
    canInstall.set(false)
  })

  if (!isStandalone && !isIOS()) {
    console.log('[PWA] Setting canInstall=true (not standalone, not iOS)')
    canInstall.set(true)
  }

  setTimeout(() => {
    let hasPrompt
    const unsub = deferredPrompt.subscribe(v => hasPrompt = v)
    unsub()
    console.log('[PWA] After 3s, hasPrompt:', !!hasPrompt, 'isStandalone:', isStandalone)
    if (!hasPrompt && !isStandalone) {
      console.log('[PWA] No beforeinstallprompt after 3s. Possible reasons:')
      console.log('[PWA] - Not served over HTTPS (current:', location.protocol + ')')
      console.log('[PWA] - Already installed')
      console.log('[PWA] - Browser doesn\'t support install (iOS Safari, Firefox, etc)')
      console.log('[PWA] - Manifest missing or invalid')
      console.log('[PWA] - No service worker registered')
      console.log('[PWA] - App not served from root (/)')
    }
  }, 3000)
}

function isIOS() {
  return /iphone|ipad|ipod/i.test(window.navigator.userAgent)
}

export async function installApp() {
  let prompt
  const unsub = deferredPrompt.subscribe(v => prompt = v)
  unsub()
  console.log('[PWA] installApp called, hasPrompt:', !!prompt)
  if (!prompt) {
    console.log('[PWA] No deferred prompt, showing manual instructions')
    alert('Untuk install:\n\nChrome: Menu (⋮) → "Cast, save, and share" → "Install app"\n\nSafari: Share (⬆️) → "Add to Home Screen"')
    return
  }
  prompt.prompt()
  const { outcome } = await prompt.userChoice
  console.log('[PWA] userChoice outcome:', outcome)
  if (outcome === 'accepted') canInstall.set(false)
  deferredPrompt.set(null)
}
