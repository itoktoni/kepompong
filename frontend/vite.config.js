import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig, loadEnv } from 'vite';
import { SvelteKitPWA } from '@vite-pwa/sveltekit';
import { writeFileSync, mkdirSync } from 'node:fs';
import { resolve } from 'node:path';
import sharp from 'sharp';

function generatePwaIcon(size) {
  const canvas = `
<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}">
  <rect width="${size}" height="${size}" rx="${Math.round(size * 0.1875)}" fill="#176c33"/>
  <text x="${size / 2}" y="${size * 0.62}" font-family="sans-serif" font-size="${size * 0.55}" font-weight="800" fill="white" text-anchor="middle">JT</text>
</svg>`;
  return canvas;
}

function getInitials(name) {
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
}

function vitePluginFavicon(appName) {
  const svgIcon = generatePwaIcon(512);

  return {
    name: 'vite-plugin-favicon',
    configureServer(server) {
      server.middlewares.use((req, res, next) => {
        if (req.url === '/favicon.svg') {
          res.setHeader('Content-Type', 'image/svg+xml');
          res.setHeader('Cache-Control', 'no-cache');
          res.end(generatePwaIcon(512));
          return;
        }
        next();
      });
    },
    async buildStart() {
      const publicDir = resolve('static');
      mkdirSync(resolve(publicDir, 'icons'), { recursive: true });

      const svg512 = generatePwaIcon(512);
      const svg192 = generatePwaIcon(192);

      writeFileSync(resolve(publicDir, 'favicon.svg'), svg512);
      writeFileSync(resolve(publicDir, 'icons/icon-192.svg'), svg192);
      writeFileSync(resolve(publicDir, 'icons/icon-512.svg'), svg512);

      await sharp(Buffer.from(svg512)).resize(512, 512).png().toFile(resolve(publicDir, 'icons/icon-512.png'));
      await sharp(Buffer.from(svg192)).resize(192, 192).png().toFile(resolve(publicDir, 'icons/icon-192.png'));
    }
  };
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  const appName = env.VITE_APP_NAME || 'Jejak Tumbuh';
  const isDev = mode === 'development';

  const pwaPlugins = isDev ? [] : [
    ...SvelteKitPWA({
      registerType: 'autoUpdate',
      includeAssets: ['favicon.svg', 'icons/*.svg', 'icons/*.png'],
      manifest: {
        name: appName,
        short_name: appName,
        description: env.VITE_APP_TAGLINE || 'Pendamping Anak',
        theme_color: '#176c33',
        background_color: '#FFF9F3',
        display: 'standalone',
        orientation: 'portrait',
        start_url: '/',
        scope: '/',
        icons: [
          { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
          { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
          { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' }
        ]
      },
      workbox: {
        globPatterns: ['**/*.{js,css,html,svg,png,woff2}'],
        navigateFallback: '/',
        navigateFallbackDenylist: [/^\/api/],
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
  ];

  return {
    optimizeDeps: {
      include: ['@iconify/svelte']
    },
    build: {
      rollupOptions: {
        output: {
          manualChunks: {
            'activity-cards': [
              './src/lib/pages/activity/StoryCard.svelte',
              './src/lib/pages/activity/RoleplayCard.svelte',
              './src/lib/pages/activity/GameCard.svelte',
              './src/lib/pages/activity/ScriptCard.svelte',
              './src/lib/pages/activity/ProjectCard.svelte',
              './src/lib/pages/activity/SongCard.svelte',
              './src/lib/pages/activity/PuzzleCard.svelte',
              './src/lib/pages/activity/ExerciseCard.svelte',
              './src/lib/pages/activity/OutdoorCard.svelte',
              './src/lib/pages/activity/ExperimentCard.svelte',
              './src/lib/pages/activity/WorksheetCard.svelte',
              './src/lib/pages/activity/GuessCard.svelte',
              './src/lib/pages/activity/HandGameCard.svelte',
              './src/lib/pages/activity/BrainTrainCard.svelte',
              './src/lib/pages/activity/ComicCard.svelte',
            ]
          }
        }
      }
    },
    server: {
      allowedHosts: true,
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
      ...pwaPlugins
    ]
  };
});
