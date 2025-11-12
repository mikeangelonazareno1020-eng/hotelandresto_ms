import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';

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
    build: {
    outDir: 'public/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
        output: {
        entryFileNames: 'assets/[name]-[hash].js',
        chunkFileNames: 'assets/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash].[ext]'
        }
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
