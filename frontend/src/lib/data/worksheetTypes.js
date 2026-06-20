import { get } from 'svelte/store'
import { worksheets as worksheetsStore } from '../stores/authStore.js'
import * as api from '../services/api.js'

let worksheetTypesFetched = false

export async function fetchWorksheetTypes() {
  if (worksheetTypesFetched) return
  worksheetTypesFetched = true

  try {
    const data = await api.getWorksheetTypes()
    if (data && Array.isArray(data)) {
      worksheetsStore.set(data)
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
    const ageOk = childAge == null || (w.ages && w.ages.some(a => Number(a) === Number(childAge)))
    const agamaOk = !w.agama || !w.agama.length || !childAgama || w.agama.includes(childAgama)
    const planOk = !w.plans || !w.plans.length || !planId || w.plans.includes(planId)
    return ageOk && agamaOk && planOk
  })
}
