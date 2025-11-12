import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';

// 🔍 Dynamically get local IP for dev HMR (handy for mobile testing)
function getLanIp() {
  try {
    const nets = os.networkInterfaces();
    for (const name of Object.keys(nets)) {
      for (const net of nets[name] || []) {
        if (net.family === 'IPv4' && !net.internal) return net.address;
      }
    }
  } catch (_) {}
  return '127.0.0.1';
}

const DEV_HOST = process.env.VITE_DEV_HOST || getLanIp();
const DEV_PORT = Number(process.env.VITE_DEV_PORT || 5173);
const DEV_PROTOCOL = process.env.VITE_DEV_PROTOCOL || 'ws';
const DEV_ORIGIN = process.env.VITE_DEV_ORIGIN || `http://${DEV_HOST}:${DEV_PORT}`;

export default defineConfig(() => ({
  // 🧩 Dev server (ignored in production)
  server: {
    host: true,
    port: DEV_PORT,
    strictPort: false,
    origin: DEV_ORIGIN,
    cors: {
      origin: [
        'http://localhost:8080',
        `http://${DEV_HOST}:8080`,
        DEV_ORIGIN,
      ],
    },
    hmr: {
      host: DEV_HOST,
      port: DEV_PORT,
      protocol: DEV_PROTOCOL,
    },
  },

  // ⚙️ Production build (Railway, VPS, etc.)
  build: {
    outDir: 'public/build',  // ✅ assets + manifest go here
    emptyOutDir: true,
    manifest: true,          // ✅ generates public/build/manifest.json
    rollupOptions: {
      // keep default chunking (better cacheability)
      // remove this if you really want a single bundle:
      // output: { manualChunks: undefined },
    },
    chunkSizeWarningLimit: 1000,
  },

  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
      ],
      refresh: true,
    }),
    tailwindcss(),
  ],
}));
