/// <reference types="@sveltejs/kit" />
import { build, files, prerendered } from '$service-worker';
import { precacheAndRoute } from 'workbox-precaching';
import { registerRoute } from 'workbox-routing';
import { CacheFirst, NetworkFirst } from 'workbox-strategies';

const manifest = [
  ...build.map((url) => ({ url, revision: null })),
  ...files.map((url) => ({ url, revision: null })),
  ...prerendered.map((url) => ({ url, revision: null })),
];

precacheAndRoute(manifest);

registerRoute(
  ({ url }) => url.pathname.startsWith('/api/'),
  new NetworkFirst({ cacheName: 'api-cache' })
);

registerRoute(
  ({ url }) => url.origin === 'https://fonts.googleapis.com' || url.origin === 'https://fonts.gstatic.com',
  new CacheFirst({ cacheName: 'google-fonts-cache' })
);

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});
