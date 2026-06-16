import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig, loadEnv } from 'vite';
import { VitePWA } from 'vite-plugin-pwa';
import { writeFileSync, mkdirSync } from 'node:fs';
import { resolve } from 'node:path';

function generateFaviconSvg(initials, size, rx, fontSize, y) {
  return `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">
  <rect width="${size}" height="${size}" rx="${rx}" fill="#176c33"/>
  <text x="${size / 2}" y="${y}" font-family="Nunito Sans, sans-serif" font-size="${fontSize}" font-weight="800" fill="white" text-anchor="middle">${initials}</text>
</svg>`;
}

function getInitials(name) {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

function vitePluginFavicon(appName) {
  const initials = getInitials(appName);
  const favicons = {
    '/favicon.svg': generateFaviconSvg(initials, 512, 96, 280, 320),
    '/icons/icon-192.svg': generateFaviconSvg(initials, 192, 36, 105, 125),
    '/icons/icon-512.svg': generateFaviconSvg(initials, 512, 96, 280, 320),
  };

  return {
    name: 'vite-plugin-favicon',
    configureServer(server) {
      server.middlewares.use((req, res, next) => {
        if (favicons[req.url]) {
          res.setHeader('Content-Type', 'image/svg+xml');
          res.setHeader('Cache-Control', 'no-cache');
          res.end(favicons[req.url]);
          return;
        }
        next();
      });
    },
    buildStart() {
      const publicDir = resolve('static');
      writeFileSync(resolve(publicDir, 'favicon.svg'), favicons['/favicon.svg']);
      mkdirSync(resolve(publicDir, 'icons'), { recursive: true });
      writeFileSync(resolve(publicDir, 'icons/icon-192.svg'), favicons['/icons/icon-192.svg']);
      writeFileSync(resolve(publicDir, 'icons/icon-512.svg'), favicons['/icons/icon-512.svg']);
    }
  };
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  const appName = env.VITE_APP_NAME || 'Jejak Tumbuh';

  return {
    optimizeDeps: {
      include: ['@iconify/svelte']
    },
    server: {
      headers: mode === 'development' ? { 'Cache-Control': 'no-store' } : undefined,
      proxy: {
        '/api/quotes': {
          target: 'https://quotes.liupurnomo.com',
          changeOrigin: true,
          rewrite: (path) => path
        },
        '/api/bukuacak': {
          target: 'https://bukuacak.vercel.app',
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api\/bukuacak/, '/api')
        },
        '/api/pixabay': {
          target: 'https://pixabay.com',
          changeOrigin: true,
          rewrite: (path) => path.replace(/^\/api\/pixabay/, '/api')
        },
        '/api': {
          target: 'https://backend.test',
          changeOrigin: true,
          secure: false,
        }
      }
    },
    plugins: [
      sveltekit(),
      vitePluginFavicon(appName),
      VitePWA({
        registerType: 'autoUpdate',
        includeAssets: ['favicon.svg', 'icons/*.svg'],
        manifest: {
          name: appName,
          short_name: appName,
          description: env.VITE_APP_TAGLINE || 'Pendamping Anak',
          theme_color: '#176c33',
          background_color: '#FFF9F3',
          display: 'standalone',
          orientation: 'portrait',
          start_url: '/',
          icons: [
            { src: '/icons/icon-192.svg', sizes: '192x192', type: 'image/svg+xml' },
            { src: '/icons/icon-512.svg', sizes: '512x512', type: 'image/svg+xml' },
            { src: '/icons/icon-512.svg', sizes: '512x512', type: 'image/svg+xml', purpose: 'maskable' }
          ]
        },
        workbox: {
          globPatterns: ['**/*.{js,css,html,svg,woff2}'],
          runtimeCaching: [
            {
              urlPattern: /^https:\/\/api\.iconify\.design\/.*/i,
              handler: 'CacheFirst',
              options: { cacheName: 'iconify-cache', expiration: { maxEntries: 200, maxAgeSeconds: 60 * 60 * 24 * 30 } }
            },
            {
              urlPattern: /^https:\/\/fonts\.googleapis\.com\/.*/i,
              handler: 'CacheFirst',
              options: { cacheName: 'google-fonts-cache', expiration: { maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 * 365 } }
            },
            {
              urlPattern: /^https:\/\/fonts\.gstatic\.com\/.*/i,
              handler: 'CacheFirst',
              options: { cacheName: 'gstatic-fonts-cache', expiration: { maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 * 365 } }
            },
            {
              urlPattern: /^https:\/\/images\.unsplash\.com\/.*/i,
              handler: 'CacheFirst',
              options: { cacheName: 'unsplash-cache', expiration: { maxEntries: 50, maxAgeSeconds: 60 * 60 * 24 * 30 } }
            },
            {
              urlPattern: /^https:\/\/lh3\.googleusercontent\.com\/.*/i,
              handler: 'CacheFirst',
              options: { cacheName: 'google-images-cache', expiration: { maxEntries: 10, maxAgeSeconds: 60 * 60 * 24 * 30 } }
            }
          ]
        }
      })
    ]
  };
});
