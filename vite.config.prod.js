import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command, mode }) => ({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/sass/custom.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: true,
        port: 5173,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'dev1.alexander-ruben.ro',
            protocol: process.env.VITE_HMR_PROTOCOL || 'https',
            clientPort: process.env.VITE_HMR_PORT || 443,
        },
    },
    build: {
        outDir: 'public/build',  // Output directory for production build
        emptyOutDir: true,       // Clear the output directory before building
    },
}));
