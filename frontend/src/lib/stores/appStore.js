import { writable, derived, get } from 'svelte/store'
import { appConfig } from '../config/appConfig.js'

const savedTab = typeof localStorage !== 'undefined' ? localStorage.getItem('lk_active_tab') : null

export const activeTab = writable(savedTab && savedTab !== 'null' ? savedTab : appConfig.defaultTab)
export const selectedPilar = writable(null)
export const selectedAnakId = writable(null)
export const userName = writable('Parent')
export const userGender = writable('')
export const toolsAnakId = writable(null)
export const selectedSkillKey = writable(null)
export const selectedAge = writable(null)
export const selectedAgama = writable(null)
export const selectedPlanId = writable(null)
export const appReady = writable(false)
export const switchCounter = writable(0)

export const pageTitle = derived(activeTab, ($activeTab) => {
  const titles = {
    pilar: 'Selamat Datang',
    progress: 'Statistik',
    activity: 'Aktivitas',
    profile: 'Profile',
    settings: 'Pengaturan',
    billing: 'Billing',
    referral: 'Affiliate',
    challenge: 'Challenge',
    jadwal: 'Jadwal Harian',
    checklist: 'Checklist Harian'
  }
  return titles[$activeTab] || `Welcome to ${appConfig.name}`
})

if (typeof localStorage !== 'undefined') {
  activeTab.subscribe((val) => {
    localStorage.setItem('lk_active_tab', val)
  })
}

export function switchTab(tabId) {
  const current = get(activeTab)
  activeTab.set(tabId)
  selectedPilar.set(null)
  switchCounter.update(n => n + 1)
  if (typeof window !== 'undefined') window.scrollTo(0, 0)
}

export function openPilarSub(key) {
  selectedPilar.set(key)
  if (typeof window !== 'undefined') window.scrollTo(0, 0)
}

export function closePilarSub() {
  selectedPilar.set(null)
}
