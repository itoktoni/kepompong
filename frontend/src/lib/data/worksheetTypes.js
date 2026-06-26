import { get } from 'svelte/store'
import { worksheets as worksheetsStore, userPlan } from '../stores/authStore.js'
import * as api from '../services/api.js'

if (typeof localStorage !== 'undefined') {
  try {
    const raw = localStorage.getItem('lk_cache_worksheets')
    if (raw && raw.includes('mdi:')) {
      localStorage.removeItem('lk_cache_worksheets')
      worksheetsStore.set([])
    }
  } catch {}
}

let worksheetTypesFetched = false

export async function fetchWorksheetTypes() {
  if (worksheetTypesFetched) return
  worksheetTypesFetched = true

  try {
    const plan = get(userPlan)
    const planId = plan?.plan_id || null
    const data = await api.getWorksheetTypes(planId)
    if (data && Array.isArray(data) && data.length > 0) {
      worksheetsStore.set(data)
      if (typeof localStorage !== 'undefined') {
        localStorage.setItem('lk_cache_worksheets', JSON.stringify(data))
      }
    }
  } catch (e) {
    console.warn('Failed to fetch worksheet types:', e)
  }
}

export function getWorksheetTypes() {
  return get(worksheetsStore) || []
}

export function filterWorksheetTypes({ childAge, childAgama, planId } = {}) {
  const list = getWorksheetTypes()
  return list.filter(w => {
    const ages = Array.isArray(w.ages) ? w.ages : []
    const agama = Array.isArray(w.agama) ? w.agama : []
    const plans = Array.isArray(w.plans) ? w.plans : []
    const ageOk = childAge == null || ages.some(a => Number(a) === Number(childAge))
    const agamaOk = !agama.length || !childAgama || agama.includes(childAgama)
    const planOk = !plans.length || !planId || plans.includes(planId)
    return ageOk && agamaOk && planOk
  })
}
