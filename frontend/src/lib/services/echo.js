import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { getAuthToken } from './api.js'

const notificationEnabled = import.meta.env.VITE_NOTIFICATION_ENABLE === 'true'

if (notificationEnabled) {
  window.Pusher = Pusher
}

let echoInstance = null

export function initEcho() {
  if (!notificationEnabled) return null
  if (echoInstance) return echoInstance

  const token = getAuthToken()
  if (!token) return null

  const apiBase = import.meta.env.VITE_API_URL || '/api'

  echoInstance = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 8080,
    wssPort: Number(import.meta.env.VITE_REVERB_PORT) || 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: `${apiBase}/broadcasting/auth`,
    auth: {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json',
      },
    },
  })

  return echoInstance
}

export function getEcho() {
  return echoInstance
}

export function disconnectEcho() {
  if (echoInstance) {
    echoInstance.disconnect()
    echoInstance = null
  }
}
