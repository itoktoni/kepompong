import { writable, derived, get } from 'svelte/store'
import { appConfig } from '../config/appConfig.js'

const savedTab = typeof localStorage !== 'undefined' ? localStorage.getItem('lk_active_tab') : null
const savedSkillKey = typeof localStorage !== 'undefined' ? localStorage.getItem('lk_selected_skill') : null

export const activeTab = writable(savedTab && savedTab !== 'null' ? savedTab : appConfig.defaultTab)
export const selectedPilar = writable(null)
export const selectedAnakId = writable(null)
export const userName = writable('Parent')
export const userGender = writable('')
export const toolsAnakId = writable(null)
export const selectedSkillKey = writable(savedSkillKey && savedSkillKey !== 'null' ? savedSkillKey : null)
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
  selectedSkillKey.subscribe((val) => {
    localStorage.setItem('lk_selected_skill', val ?? '')
  })
}

export function switchTab(tabId) {
  const current = get(activeTab)
  if (tabId !== current) {
    window.history.pushState({ tab: tabId }, '', '')
  }
  activeTab.set(tabId)
  selectedPilar.set(null)
  switchCounter.update(n => n + 1)
  if (typeof window !== 'undefined') {
    window.scrollTo(0, 0)
    import('../services/syncService.js').then(m => m.trySync()).catch(() => {})
  }
}

export function initBackHandler() {
  if (typeof window === 'undefined') return

  window.history.pushState({ tab: get(activeTab) }, '', '')

  window.addEventListener('popstate', (e) => {
    const current = get(activeTab)
    const pilar = get(selectedPilar)

    if (pilar) {
      selectedPilar.set(null)
      window.history.pushState({ tab: current }, '', '')
      return
    }

    if (current !== 'activity') {
      activeTab.set('activity')
      switchCounter.update(n => n + 1)
      window.history.pushState({ tab: 'activity' }, '', '')
      return
    }

    window.history.pushState({ tab: 'activity' }, '', '')
  })
}

export function openPilarSub(key) {
  selectedPilar.set(key)
  if (typeof window !== 'undefined') window.scrollTo(0, 0)
}

export function closePilarSub() {
  selectedPilar.set(null)
}
