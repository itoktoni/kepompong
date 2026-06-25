import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig, loadEnv } from 'vite';
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

function vitePluginFavicon(appName) {
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

      writeFileSync(resolve(publicDir, 'manifest.webmanifest'), JSON.stringify({
        name: appName,
        short_name: appName,
        description: 'Pendamping Anak',
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
      }, null, 2));
    }
  };
}

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '');
  const appName = env.VITE_APP_NAME || 'Jejak Tumbuh';

  return {
    optimizeDeps: {
      include: []
    },
    build: {
      rollupOptions: {
        output: {
          manualChunks: () => 'app'
        }
      }
    },
    server: {
      allowedHosts: true,
      hmr: {
        overlay: false,
        clientPort: 5173,
        host: 'localhost',
      },
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
      vitePluginFavicon(appName)
    ]
  };
});
