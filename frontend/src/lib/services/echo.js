import { Centrifuge } from 'centrifuge'
import { getAuthToken } from './api.js'

const notificationEnabled = import.meta.env.VITE_NOTIFICATION_ENABLE === 'true'

let centrifugeInstance = null
let subInstance = null

export function initCentrifugo(userId) {
  if (!notificationEnabled) return null
  if (!userId) return null

  if (centrifugeInstance) return centrifugeInstance

  const token = getAuthToken()
  if (!token) return null

  const wsUrl = import.meta.env.VITE_CENTRIFUGO_URL || 'ws://localhost:8000/connection/websocket'
  const apiBase = import.meta.env.VITE_API_URL || '/api'

  async function fetchToken(channel) {
    const body = channel ? JSON.stringify({ channel }) : undefined
    const res = await fetch(`${apiBase}/centrifugo/token`, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body,
    })
    const data = await res.json()
    return data.token
  }

  centrifugeInstance = new Centrifuge(wsUrl, {
    getToken: () => fetchToken(),
  })

  const channelName = `notifications#${userId}`
  subInstance = centrifugeInstance.newSubscription(channelName, {
    getToken: () => fetchToken(channelName),
  })

  subInstance.subscribe()
  centrifugeInstance.connect()

  return centrifugeInstance
}

export function onNotification(callback) {
  if (!subInstance) return
  subInstance.on('publication', (ctx) => {
    callback(ctx.data)
  })
}

export function disconnectCentrifugo() {
  if (subInstance) {
    subInstance.removeAllListeners()
    subInstance.unsubscribe()
    subInstance = null
  }
  if (centrifugeInstance) {
    centrifugeInstance.disconnect()
    centrifugeInstance = null
  }
}
