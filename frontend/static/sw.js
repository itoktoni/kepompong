const CACHE_NAME = 'jt-shell-v1'
const FONT_CACHE = 'jt-fonts-v1'
const IMAGE_CACHE = 'jt-images-v1'

const PRECACHE_URLS = [
  '/',
  '/manifest.webmanifest',
  '/favicon.svg',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
]

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
  )
  self.skipWaiting()
})

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((names) =>
      Promise.all(
        names
          .filter((n) => n !== CACHE_NAME && n !== FONT_CACHE && n !== IMAGE_CACHE)
          .map((n) => caches.delete(n))
      )
    )
  )
  self.clients.claim()
})

self.addEventListener('fetch', (event) => {
  const { request } = event
  const url = new URL(request.url)

  if (request.method !== 'GET') return

  if (url.hostname.includes('fonts.googleapis.com') || url.hostname.includes('fonts.gstatic.com')) {
    event.respondWith(fontFirst(request))
    return
  }

  if (url.hostname.includes('images.unsplash.com') || url.hostname.includes('lh3.googleusercontent.com')) {
    event.respondWith(imageFirst(request))
    return
  }

  if (url.origin === self.location.origin) {
    event.respondWith(localFirst(request))
    return
  }
})

async function fontFirst(request) {
  const cache = await caches.open(FONT_CACHE)
  const cached = await cache.match(request)
  if (cached) return cached
  try {
    const response = await fetch(request)
    if (response.ok) cache.put(request, response.clone())
    return response
  } catch {
    return new Response('', { status: 503, statusText: 'Offline' })
  }
}

async function imageFirst(request) {
  const cache = await caches.open(IMAGE_CACHE)
  const cached = await cache.match(request)
  if (cached) return cached
  try {
    const response = await fetch(request)
    if (response.ok) cache.put(request, response.clone())
    return response
  } catch {
    return new Response('', { status: 503, statusText: 'Offline' })
  }
}

async function localFirst(request) {
  const cache = await caches.open(CACHE_NAME)
  const cached = await cache.match(request)
  if (cached) {
    fetch(request).then((response) => {
      if (response.ok) cache.put(request, response)
    }).catch(() => {})
    return cached
  }
  try {
    const response = await fetch(request)
    if (response.ok) cache.put(request, response.clone())
    return response
  } catch {
    if (request.mode === 'navigate') {
      const fallback = await cache.match('/')
      if (fallback) return fallback
    }
    return new Response('Offline', { status: 503, statusText: 'Offline' })
  }
}
