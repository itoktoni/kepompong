const API_BASE = import.meta.env.VITE_API_URL || '/api'

let authToken = null
let onVerificationRequired = null

export function setVerificationCallback(cb) {
  onVerificationRequired = cb
}

export function setAuthToken(token) {
  authToken = token
  if (typeof localStorage !== 'undefined') localStorage.setItem('lk_auth_token', token)
}

export function setAuthTokenMemory(token) {
  authToken = token
}

export function getAuthToken() {
  if (!authToken && typeof localStorage !== 'undefined') {
    authToken = localStorage.getItem('lk_auth_token')
  }
  return authToken
}

export function clearAuthToken() {
  authToken = null
  if (typeof localStorage !== 'undefined') localStorage.removeItem('lk_auth_token')
}

export function isAuthenticated() {
  return !!getAuthToken()
}

async function apiFetch(endpoint, options = {}) {
  const token = getAuthToken()
  const url = `${API_BASE}${endpoint}`
  const timeout = options.timeout || 120000

  const headers = {
    'Accept': 'application/json',
    ...options.headers,
  }

  if (!(options.body instanceof FormData)) {
    headers['Content-Type'] = 'application/json'
  }

  if (token) {
    headers['Authorization'] = `Bearer ${token}`
  }

  const controller = new AbortController()
  const timer = setTimeout(() => controller.abort(), timeout)

  try {
    const response = await fetch(url, { ...options, headers, signal: controller.signal })
    clearTimeout(timer)

    if (response.status === 401) {
      clearAuthToken()
      throw new Error('Unauthorized - please login again')
    }

    if (response.status === 403) {
    const body = await response.json().catch(() => ({}))
    if (body.needs_verification) {
      if (onVerificationRequired) {
        onVerificationRequired(body.verification_gateway || 'whatsapp')
      }
      const err = new Error(body.message || 'Akun belum terverifikasi')
      err.needs_verification = true
      err.verification_gateway = body.verification_gateway
      throw err
    }
  }

  if (!response.ok) {
    const error = await response.json().catch(() => ({ message: 'Request failed' }))
    const err = new Error(error.message || `HTTP ${response.status}`)
    err.errors = error.errors || error.data || null
    err.status = response.status
    err.cooldown = error.cooldown || null
    throw err
  }

  return response.json()
  } catch (e) {
    clearTimeout(timer)
    if (e.name === 'AbortError') {
      throw new Error('Request timeout - server took too long to respond')
    }
    throw e
  }
}

export async function getMe() { return apiFetch('/me') }
export async function updateProfile(data) { return apiFetch('/profile', { method: 'PUT', body: JSON.stringify(data) }) }
export async function changePassword(currentPassword, newPassword, newPasswordConfirmation) {
  return apiFetch('/password', { method: 'PUT', body: JSON.stringify({ current_password: currentPassword, password: newPassword, password_confirmation: newPasswordConfirmation }) })
}
export async function updateAffiliateCode(code) { return apiFetch('/affiliate-code', { method: 'PUT', body: JSON.stringify({ affiliate_code: code }) }) }
export async function getReferrals() { return apiFetch('/referrals') }
export async function getMyDiscounts() { return apiFetch('/discounts') }
export async function createDiscount(data) { return apiFetch('/discounts', { method: 'POST', body: JSON.stringify(data) }) }
export async function deleteDiscount(id) { return apiFetch(`/discounts/${id}`, { method: 'DELETE' }) }
export async function updateRekening(data) { return apiFetch('/rekening', { method: 'POST', body: JSON.stringify(data) }) }
export async function requestCashout(amount) { return apiFetch('/cashout', { method: 'POST', body: JSON.stringify({ amount }) }) }
export async function getCashouts() { return apiFetch('/cashouts') }
export async function purchasePlan(planId, discountCode = null) {
  const body = { plan_id: planId }
  if (discountCode) body.discount_code = discountCode
  return apiFetch('/purchase-plan', { method: 'POST', body: JSON.stringify(body) })
}
export async function createPayment(planId, discountCode = null, methodId = null) {
  return apiFetch('/payments', {
    method: 'POST',
    body: JSON.stringify({ plan_id: planId, discount_code: discountCode, payment_method_id: methodId })
  })
}
export async function validateDiscount(code, planId) {
  return apiFetch('/payments/validate-discount', { method: 'POST', body: JSON.stringify({ code, plan_id: planId }) })
}
export async function getPaymentStatus(paymentId) { return apiFetch(`/payments/${paymentId}`) }
export async function settlePayment(paymentId) { return apiFetch(`/payments/${paymentId}/settle`, { method: 'POST' }) }
export async function cancelPayment(paymentId) { return apiFetch(`/payments/${paymentId}/cancel`, { method: 'POST' }) }
export async function getPaymentHistory() { return apiFetch('/payments') }
export async function getPaymentMethods() { return apiFetch('/payment-methods') }
export async function getPaymentMethodCategories() { return apiFetch('/payment-methods/categories') }
export async function getPaymentMethodList() { return apiFetch('/payment-methods/list') }
export async function getPlans() { return apiFetch('/plans') }

export async function login(email, password) {
  const data = await apiFetch('/login', { method: 'POST', body: JSON.stringify({ email, password }) })
  if (data.access_token) setAuthToken(data.access_token)
  return data
}

export async function register(name, email, phone, password, passwordConfirmation, referralCode) {
  const body = { name, email, phone, password, password_confirmation: passwordConfirmation }
  if (referralCode) body.ref = referralCode
  const data = await apiFetch('/register', { method: 'POST', body: JSON.stringify(body) })
  if (data.access_token) setAuthToken(data.access_token)
  return data
}

export async function forgotPassword(body) {
  return apiFetch('/forgot-password', { method: 'POST', body: JSON.stringify(body) })
}

export async function getConfig() {
  return apiFetch('/config')
}

export async function getPilarsAndSkills() {
  return apiFetch('/pilars')
}

export async function resetPassword(token, email, password, passwordConfirmation) {
  return apiFetch('/reset-password', { method: 'POST', body: JSON.stringify({ token, email, password, password_confirmation: passwordConfirmation }) })
}

export async function sendVerification(channel) {
  return apiFetch('/send-verification', { method: 'POST', body: JSON.stringify({ channel }) })
}

export async function verifyCode(code) {
  return apiFetch('/verify', { method: 'POST', body: JSON.stringify({ code }) })
}

export async function logout() {
  try { await apiFetch('/logout', { method: 'POST' }) } catch (e) { /* ignore */ }
  clearAuthToken()
}

export async function getAnakList() { return apiFetch('/anak') }
export async function addAnak(anak) { return apiFetch('/anak', { method: 'POST', body: JSON.stringify(anak) }) }
export async function updateAnak(id, data) { return apiFetch(`/anak/${id}`, { method: 'PUT', body: JSON.stringify(data) }) }
export async function deleteAnak(id) { return apiFetch(`/anak/${id}`, { method: 'DELETE' }) }

export async function addSkill(anakId, skill) { return apiFetch(`/anak/${anakId}/skills`, { method: 'POST', body: JSON.stringify(skill) }) }
export async function updateSkill(anakId, skillId, data) { return apiFetch(`/anak/${anakId}/skills/${skillId}`, { method: 'PUT', body: JSON.stringify(data) }) }
export async function deleteSkill(anakId, skillId) { return apiFetch(`/anak/${anakId}/skills/${skillId}`, { method: 'DELETE' }) }

export async function addActivity(anakId, activity) { return apiFetch(`/anak/${anakId}/activities`, { method: 'POST', body: JSON.stringify(activity) }) }
export async function deleteActivity(anakId, activityId) { return apiFetch(`/anak/${anakId}/activities/${activityId}`, { method: 'DELETE' }) }
export async function toggleActivity(anakId, activityId) { return apiFetch(`/anak/${anakId}/activities/${activityId}/toggle`, { method: 'PUT' }) }

export async function addCompletedSkill(anakId, data) { return apiFetch(`/anak/${anakId}/completed-skills`, { method: 'POST', body: JSON.stringify(data) }) }
export async function deleteCompletedSkill(anakId, key) { return apiFetch(`/anak/${anakId}/completed-skills/${key}`, { method: 'DELETE' }) }

export async function getChallenges(anakId) { return apiFetch(`/anak/${anakId}/challenges`) }
export async function addChallenge(anakId, challenge) { return apiFetch(`/anak/${anakId}/challenges`, { method: 'POST', body: JSON.stringify(challenge) }) }
export async function updateChallenge(anakId, challengeId, data) { return apiFetch(`/anak/${anakId}/challenges/${challengeId}`, { method: 'PUT', body: JSON.stringify(data) }) }
export async function deleteChallenge(anakId, challengeId) { return apiFetch(`/anak/${anakId}/challenges/${challengeId}`, { method: 'DELETE' }) }

export async function getChallengeHistory(anakId) { return apiFetch(`/anak/${anakId}/challenge-history`) }
export async function addChallengeHistory(anakId, history) { return apiFetch(`/anak/${anakId}/challenge-history`, { method: 'POST', body: JSON.stringify(history) }) }

export async function getChecklists(anakId) { return apiFetch(`/anak/${anakId}/checklists`) }
export async function addChecklist(anakId, checklist) { return apiFetch(`/anak/${anakId}/checklists`, { method: 'POST', body: JSON.stringify(checklist) }) }
export async function updateChecklist(anakId, checklistId, data) { return apiFetch(`/anak/${anakId}/checklists/${checklistId}`, { method: 'PUT', body: JSON.stringify(data) }) }
export async function deleteChecklist(anakId, checklistId) { return apiFetch(`/anak/${anakId}/checklists/${checklistId}`, { method: 'DELETE' }) }

export async function getSchedules(anakId) { return apiFetch(`/anak/${anakId}/schedules`) }
export async function addSchedule(anakId, schedule) { return apiFetch(`/anak/${anakId}/schedules`, { method: 'POST', body: JSON.stringify(schedule) }) }
export async function updateSchedule(anakId, scheduleId, data) { return apiFetch(`/anak/${anakId}/schedules/${scheduleId}`, { method: 'PUT', body: JSON.stringify(data) }) }
export async function toggleScheduleDone(anakId, scheduleId, date, time) { return apiFetch(`/anak/${anakId}/schedules/${scheduleId}/toggle`, { method: 'POST', body: JSON.stringify({ date, time }) }) }
export async function deleteSchedule(anakId, scheduleId) { return apiFetch(`/anak/${anakId}/schedules/${scheduleId}`, { method: 'DELETE' }) }
export async function getScheduleHistories(anakId, date) { return apiFetch(`/anak/${anakId}/schedule-histories${date ? `?date=${date}` : ''}`) }

export async function addWorksheet(anakId, worksheet) { return apiFetch(`/anak/${anakId}/worksheets`, { method: 'POST', body: JSON.stringify(worksheet) }) }
export async function deleteWorksheet(anakId, worksheetId) { return apiFetch(`/anak/${anakId}/worksheets/${worksheetId}`, { method: 'DELETE' }) }

export async function getWorksheetTypes() { return apiFetch('/worksheet-types') }

export async function syncToServer(anakList) { return apiFetch('/sync', { method: 'POST', body: JSON.stringify({ anak_list: anakList }) }) }
export async function fetchFromServer() { return apiFetch('/anak') }

export async function getVapidKey() {
  const res = await fetch(`${API_BASE}/push/vapid-key`, { headers: { 'Accept': 'application/json' } })
  if (!res.ok) throw new Error(`VAPID key request failed: ${res.status} ${res.statusText}`)
  const data = await res.json()
  if (!data.publicKey) throw new Error('Server returned empty VAPID public key')
  return data.publicKey
}

export async function subscribePush(subscription) { return apiFetch('/push/subscribe', { method: 'POST', body: JSON.stringify(subscription) }) }
export async function unsubscribePush(endpoint) { return apiFetch('/push/unsubscribe', { method: 'POST', body: JSON.stringify({ endpoint }) }) }
export async function getPushStatus() { return apiFetch('/push/status') }

export async function getNotifications(limit = 50) { return apiFetch(`/notifications?limit=${limit}`) }
export async function markNotificationRead(id) { return apiFetch(`/notifications/${id}/read`, { method: 'PUT' }) }
export async function markAllNotificationsRead() { return apiFetch('/notifications/read-all', { method: 'PUT' }) }
export async function deleteNotification(id) { return apiFetch(`/notifications/${id}`, { method: 'DELETE' }) }
export async function clearAllNotifications() { return apiFetch('/notifications', { method: 'DELETE' }) }

export async function getEvaluations(anakId) { return apiFetch(`/anak/${anakId}/evaluations`) }
export async function addEvaluation(anakId, data) { return apiFetch(`/anak/${anakId}/evaluations`, { method: 'POST', body: JSON.stringify(data) }) }
export async function deleteEvaluation(anakId, evalId) { return apiFetch(`/anak/${anakId}/evaluations/${evalId}`, { method: 'DELETE' }) }

export async function getActivities(params = {}) {
  const qs = new URLSearchParams(params).toString()
  return apiFetch(`/activities${qs ? '?' + qs : ''}`)
}
export async function getActivitiesGrouped() { return apiFetch('/activities?grouped=1') }
export async function getActivitiesByType(type) { return apiFetch(`/activities/type/${type}`) }
export async function syncActivitiesByType(type) { return apiFetch(`/activities/sync/${type}`) }
export async function getActivityBySlug(slug) { return apiFetch(`/activities/${slug}`) }
export async function getActivityTypes() { return apiFetch('/activities/types') }
export async function trackActivityView(id) { return apiFetch(`/activities/${id}/view`) }
export async function getPopularActivities(limit = 10) { return apiFetch(`/activities/popular?limit=${limit}`) }
export async function updateActivity(id, data) {
  if (data instanceof FormData) {
    data.append('_method', 'PUT')
    return apiFetch(`/activities/${id}/update`, { method: 'POST', body: data })
  }
  return apiFetch(`/activities/${id}/update`, { method: 'PUT', body: JSON.stringify(data) })
}
export async function generateIdea(data) {
  return apiFetch('/generate-idea', { method: 'POST', body: JSON.stringify(data) })
}
export async function getIdeas(params = {}) {
  const qs = new URLSearchParams(params).toString()
  return apiFetch(`/ideas${qs ? '?' + qs : ''}`)
}
export async function updateIdea(id, data) {
  return apiFetch(`/ideas/${id}`, { method: 'PUT', body: JSON.stringify(data) })
}
export async function deleteIdea(id) {
  return apiFetch(`/ideas/${id}`, { method: 'DELETE' })
}
export async function ideaToActivity(id, data = {}) {
  return apiFetch(`/ideas/${id}/generate-activity`, { method: 'POST', body: JSON.stringify(data) })
}
export async function getAiProviders() {
  return apiFetch('/ai-providers')
}
export async function getActivityTypeOptions() {
  return apiFetch('/activity-types')
}
export async function getSkillsList() {
  return apiFetch('/skills-list')
}
export async function getActivitiesList() {
  return apiFetch('/activities-list')
}
