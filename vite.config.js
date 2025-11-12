import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import os from 'os';

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

export default defineConfig(({ command }) => ({
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
            credentials: false,
        },
        hmr: {
            host: DEV_HOST,
            port: DEV_PORT,
            protocol: DEV_PROTOCOL,
        },
    },

    // 👇 Add these for Railway / production build
    build: {
        outDir: 'public/build',
        manifest: true,
        emptyOutDir: true,
    },

    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
}));
