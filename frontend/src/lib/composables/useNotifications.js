import { writable, derived, get } from 'svelte/store'
import * as api from '../services/api.js'
import { initCentrifugo, onNotification, disconnectCentrifugo } from '../services/echo.js'

export const notifications = writable([])
export const loading = writable(false)

export const unreadCount = derived(notifications, ($n) => $n.filter(n => !n.read).length)

const notificationEnabled = import.meta.env.VITE_NOTIFICATION_ENABLE === 'true'

export async function fetchNotifications() {
  if (!notificationEnabled) return
  if (!api.isAuthenticated()) return
  loading.set(true)
  try {
    const data = await api.getNotifications()
    notifications.set(data.notifications || [])
  } catch (e) { /* ignore */ }
  loading.set(false)
}

export function initRealtime(userId) {
  if (!notificationEnabled) return

  const centrifuge = initCentrifugo(userId)
  if (!centrifuge) return

  onNotification((event) => {
    notifications.update(list => {
      if (list.find(n => n.id === event.id)) return list
      return [event, ...list]
    })
  })
}

export function disconnectRealtime() {
  disconnectCentrifugo()
}

export function addNotification(notification) {
  notifications.update(list => {
    if (list.find(n => n.id === notification.id)) return list
    return [notification, ...list]
  })
}

export async function markRead(n) {
  if (!n.id || typeof n.id !== 'number') return
  n.read = true
  notifications.update(list => list)
  try { await api.markNotificationRead(n.id) } catch (e) { /* ignore */ }
}

export async function markAllRead() {
  notifications.update(list => list.map(n => ({ ...n, read: true })))
  try { await api.markAllNotificationsRead() } catch (e) { /* ignore */ }
}

export async function clearAll() {
  notifications.set([])
  try { await api.clearAllNotifications() } catch (e) { /* ignore */ }
}
