import { get } from 'svelte/store'
import { worksheets as worksheetsStore } from '../stores/authStore.js'
import { worksheetTypes_1_3 } from './worksheetTypes_1_3.js'
import { worksheetTypes_3_5 } from './worksheetTypes_3_5.js'
import { worksheetTypes_4_7 } from './worksheetTypes_4_7.js'
import { worksheetTypes_6_9 } from './worksheetTypes_6_9.js'
import { worksheetTypes_7_plus } from './worksheetTypes_7_plus.js'
import * as api from '../services/api.js'

// Fallback data dari split files per age group
const fallbackWorksheetTypes = [
  ...worksheetTypes_1_3,
  ...worksheetTypes_3_5,
  ...worksheetTypes_4_7,
  ...worksheetTypes_6_9,
  ...worksheetTypes_7_plus,
]

// State untuk menyimpan data dari server
let serverWorksheetTypes = []
let worksheetTypesFetched = false

export async function fetchWorksheetTypes() {
  if (worksheetTypesFetched) return
  worksheetTypesFetched = true

  if (!api.isAuthenticated()) return

  try {
    const data = await api.getWorksheetTypes()
    if (data && Array.isArray(data)) {
      serverWorksheetTypes = data
      worksheetsStore.set(data)
    }
  } catch (e) {
    console.warn('Failed to fetch worksheet types:', e)
  }
}

export const worksheetTypes = fallbackWorksheetTypes

export function getWorksheetTypes() {
  const storeData = get(worksheetsStore)
  const data = storeData.length ? storeData : serverWorksheetTypes
  return data.length ? data : fallbackWorksheetTypes
}

export function filterWorksheetTypes({ childAge, childAgama, planId } = {}) {
  const list = getWorksheetTypes()
  return list.filter(w => {
    const ageOk = childAge == null || (w.ages && w.ages.includes(childAge))
    const agamaOk = !w.agama || !w.agama.length || !childAgama || w.agama.includes(childAgama)
    const planOk = !w.plans || !w.plans.length || !planId || w.plans.includes(planId)
    return ageOk && agamaOk && planOk
  })
}
