import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';

// ✅ Optional: detect local IP for HMR during dev
function getLanIp() {
  try {
    const nets = os.networkInterfaces();
    for (const name of Object.keys(nets)) {
      for (const net of nets[name]) {
        if (net.family === 'IPv4' && !net.internal) return net.address;
      }
    }
  } catch (e) {}
  return '127.0.0.1';
}

export default defineConfig({
  build: {
    outDir: 'public/build',     // ✅ correct directory for Laravel
    manifest: true,             // ✅ generates /public/build/manifest.json
    emptyOutDir: true,
  },
  server: {
    host: getLanIp(),
    port: 5173,
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    tailwindcss(),
  ],
});
