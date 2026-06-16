import { writable, get } from 'svelte/store'
import * as api from '../services/api.js'

export const isSubscribed = writable(false)
export const isSupported = writable(false)
export const permission = writable('default')
export const pushLoading = writable(false)
export const debugLog = writable([])

function log(msg) {
  debugLog.update(arr => [...arr, `[${new Date().toLocaleTimeString()}] ${msg}`])
}

export async function checkSubscription() {
  try {
    const reg = await navigator.serviceWorker.ready
    const sub = await reg.pushManager.getSubscription()
    isSubscribed.set(sub !== null)
    log(`SW ready, local subscription: ${sub ? 'exists' : 'none'}`)
    if (sub && api.isAuthenticated()) {
      try {
        const status = await api.getPushStatus()
        log(`Server status: ${JSON.stringify(status)}`)
      } catch (e) {
        log(`Server status check failed: ${e.message}`)
      }
    }
  } catch (e) {
    log(`Check failed: ${e.message}`)
    isSubscribed.set(false)
  }
}

export function initPush() {
  if (typeof window === 'undefined') return
  const supported = 'serviceWorker' in navigator && 'PushManager' in window
  isSupported.set(supported)
  log(`Supported: ${supported}`)
  if (supported) {
    permission.set(Notification.permission)
    log(`Permission: ${Notification.permission}`)
    checkSubscription()
  }
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const rawData = window.atob(base64)
  const outputArray = new Uint8Array(rawData.length)
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i)
  }
  return outputArray
}

export async function subscribe() {
  if (!get(isSupported)) throw new Error('Push notifications not supported')
  pushLoading.set(true)
  try {
    const perm = await Notification.requestPermission()
    permission.set(perm)
    log(`Permission result: ${perm}`)
    if (perm !== 'granted') throw new Error('Izin notifikasi ditolak')

    const reg = await navigator.serviceWorker.ready
    log('Getting VAPID key...')
    const vapidKey = await api.getVapidKey()
    log(`VAPID key: ${vapidKey ? 'received' : 'MISSING'}`)
    if (!vapidKey) throw new Error('VAPID key tidak tersedia dari server')

    const subscription = await reg.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(vapidKey),
    })
    log('Browser push subscribed')

    const subJson = subscription.toJSON()
    log(`Endpoint: ${subJson.endpoint.substring(0, 60)}...`)

    if (api.isAuthenticated()) {
      await api.subscribePush(subJson)
      log('Saved to server')
    } else {
      log('Not logged in — subscription is local only')
    }

    isSubscribed.set(true)
    return true
  } catch (err) {
    log(`ERROR: ${err.message}`)
    throw err
  } finally {
    pushLoading.set(false)
  }
}

export async function unsubscribe() {
  pushLoading.set(true)
  try {
    const reg = await navigator.serviceWorker.ready
    const subscription = await reg.pushManager.getSubscription()
    if (subscription) {
      if (api.isAuthenticated()) {
        try {
          await api.unsubscribePush(subscription.endpoint)
          log('Removed from server')
        } catch (e) { log(`Server remove failed: ${e.message}`) }
      }
      await subscription.unsubscribe()
      log('Browser unsubscribed')
    }
    isSubscribed.set(false)
    return true
  } catch (err) {
    log(`Unsubscribe error: ${err.message}`)
    throw err
  } finally {
    pushLoading.set(false)
  }
}

export async function togglePush() {
  const subscribed = get(isSubscribed)
  if (subscribed) return unsubscribe()
  return subscribe()
}
