import { writable } from 'svelte/store'
import { onMount } from 'svelte'

const deferredPrompt = writable(null)
export const canInstall = writable(false)

export function initInstall() {
  if (typeof window === 'undefined') return

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault()
    deferredPrompt.set(e)
    canInstall.set(true)
  })

  window.addEventListener('appinstalled', () => {
    deferredPrompt.set(null)
    canInstall.set(false)
  })
}

export async function installApp() {
  let prompt
  const unsub = deferredPrompt.subscribe(v => prompt = v)
  unsub()
  if (!prompt) return
  prompt.prompt()
  const { outcome } = await prompt.userChoice
  if (outcome === 'accepted') canInstall.set(false)
  deferredPrompt.set(null)
}
