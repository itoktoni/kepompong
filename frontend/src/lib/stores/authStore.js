import { writable, derived } from 'svelte/store'
import * as api from '../services/api.js'

export const user = writable(null)
export const token = writable(null)
export const loading = writable(false)
export const error = writable('')
export const validationErrors = writable(null)
export const serverAnakList = writable([])
export const serverDate = writable(null)
export const trialDays = writable(10)
export const needsVerification = writable(false)
export const verificationGateway = writable('whatsapp')
const cachedPilars = (() => {
  if (typeof localStorage === 'undefined') return []
  try { const raw = localStorage.getItem('lk_cache_pilars'); return raw ? JSON.parse(raw) : [] } catch { return [] }
})()
const cachedSkills = (() => {
  if (typeof localStorage === 'undefined') return []
  try { const raw = localStorage.getItem('lk_cache_skills'); return raw ? JSON.parse(raw) : [] } catch { return [] }
})()
const cachedWorksheets = (() => {
  if (typeof localStorage === 'undefined') return []
  try { const raw = localStorage.getItem('lk_cache_worksheets'); return raw ? JSON.parse(raw) : [] } catch { return [] }
})()

export const plans = writable([])
export const discounts = writable([])
export const affiliateConfig = writable({})
export const pilars = writable(cachedPilars)
export const skills = writable(cachedSkills)
export const worksheets = writable(cachedWorksheets)

export const isAuthenticated = derived(token, ($token) => !!$token)
export const userPlan = derived(user, ($user) => $user?.subscribe || null)
export const userRole = derived(user, ($user) => $user?.role || '')

export function init() {
  const storedToken = api.getAuthToken()
  if (storedToken) token.set(storedToken)

  api.setVerificationCallback((gateway) => {
    needsVerification.set(true)
    verificationGateway.set(gateway)
  })
}

export function applyServerData(data) {
  if (data.needs_verification) {
    needsVerification.set(true)
    verificationGateway.set(data.verification_gateway || 'whatsapp')
  } else {
    needsVerification.set(false)
  }
  if (data.user) user.set(data.user)
  if (data.user?.subscribe && typeof localStorage !== 'undefined') localStorage.removeItem('lk_just_paid')
  if (data.anak_list) serverAnakList.set(data.anak_list)
  if (data.server_date) serverDate.set(data.server_date)
  if (data.trial_days) trialDays.set(data.trial_days)
  if (data.plans) plans.set(data.plans)
  if (data.discounts) discounts.set(data.discounts)
  if (data.pilars) {
    pilars.set(data.pilars)
    if (typeof localStorage !== 'undefined') localStorage.setItem('lk_cache_pilars', JSON.stringify(data.pilars))
  }
  if (data.skills) {
    skills.set(data.skills)
    if (typeof localStorage !== 'undefined') localStorage.setItem('lk_cache_skills', JSON.stringify(data.skills))
  }
  if (data.worksheets) {
    worksheets.set(data.worksheets)
    if (typeof localStorage !== 'undefined') localStorage.setItem('lk_cache_worksheets', JSON.stringify(data.worksheets))
  }
  if (data.affiliate_config) affiliateConfig.set(data.affiliate_config)
}

export async function login(email, password) {
  loading.set(true)
  error.set('')
  validationErrors.set(null)
  try {
    const data = await api.login(email, password)
    token.set(data.access_token)
    applyServerData(data)
    return data
  } catch (err) {
    error.set(err.message || 'Login gagal')
    validationErrors.set(err.errors || null)
    throw err
  } finally {
    loading.set(false)
  }
}

export async function register(name, email, phone, password, passwordConfirmation, referralCode) {
  loading.set(true)
  error.set('')
  validationErrors.set(null)
  try {
    const data = await api.register(name, email, phone, password, passwordConfirmation, referralCode)
    token.set(data.access_token)
    applyServerData(data)
    return data
  } catch (err) {
    error.set(err.message || 'Registrasi gagal')
    validationErrors.set(err.errors || null)
    throw err
  } finally {
    loading.set(false)
  }
}

export function logout() {
  api.logout()
  token.set(null)
  user.set(null)
  serverAnakList.set([])
  serverDate.set(null)
  needsVerification.set(false)
  verificationGateway.set('whatsapp')
  if (typeof localStorage !== 'undefined') localStorage.removeItem('lk_cache_user_id')
}

init()
