import { writable } from 'svelte/store'
import { onMount } from 'svelte'
import { appConfig } from '../config/appConfig.js'

const deferredPrompt = writable(null)
export const canInstall = writable(false)

export function initInstall() {
  if (typeof window === 'undefined') return

  const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true
  if (isStandalone) return

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt.set(e)
    canInstall.set(true)
  })

  window.addEventListener('appinstalled', () => {
    deferredPrompt.set(null)
    canInstall.set(false)
  })

  if (!isStandalone && !isIOS()) {
    canInstall.set(true)
  }
}

function isIOS() {
  return /iphone|ipad|ipod/i.test(window.navigator.userAgent)
}

export async function installApp() {
  let prompt
  const unsub = deferredPrompt.subscribe(v => prompt = v)
  unsub()
  if (!prompt) {
    alert('Untuk install:\n\nChrome: Menu (⋮)  "\n→ Cast, save, and share\n→ Install app"\n\nSafari: Share (⬆)  "\n→ Add to Home Screen"')
    return
  }
  prompt.prompt()
  const { outcome } = await prompt.userChoice
  if (outcome === 'accepted') canInstall.set(false)
  deferredPrompt.set(null)
}
