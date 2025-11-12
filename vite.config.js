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
                if (net.family === 'IPv4' && !net.internal) {
                    return net.address;
                }
            }
        }
    } catch (_) {}
    return '127.0.0.1';
}

const DEV_HOST = process.env.VITE_DEV_HOST || getLanIp();
const DEV_PORT = Number(process.env.VITE_DEV_PORT || 5173);
const DEV_PROTOCOL = process.env.VITE_DEV_PROTOCOL || 'ws';
const DEV_ORIGIN = process.env.VITE_DEV_ORIGIN || `http://${DEV_HOST}:${DEV_PORT}`;

export default defineConfig(({ command, mode }) => ({
    // 🧩 Development server config
    server: {
        host: true,
        port: DEV_PORT,
        strictPort: false,
        origin: DEV_ORIGIN,
        cors: {
            origin: [
                'http://localhost:8000',
                `http://${DEV_HOST}:8000`,
                DEV_ORIGIN,
            ],
        },
        hmr: {
            host: DEV_HOST,
            port: DEV_PORT,
            protocol: DEV_PROTOCOL,
        },
    },

    // ⚙️ Production build config (for Railway, VPS, etc.)
    build: {
        outDir: 'public/build',          // ✅ where assets go
        emptyOutDir: true,
        manifest: true,                  // ✅ generate manifest.json
        rollupOptions: {
            output: {
                manualChunks: undefined, // simpler single-bundle output
            },
        },
        chunkSizeWarningLimit: 1000,     // silence 500 kB warning
        // ✅ ensures Laravel sees the manifest at /public/build/manifest.json
        manifestDir: 'public/build',
    },

    // 🔌 Plugins
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
