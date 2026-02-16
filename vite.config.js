import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/css/create.css',
                'resources/css/dashboard.css',
                'resources/css/my_tickets.css',
                'resources/css/ops.css',
                'resources/css/settings.css',
                'resources/css/ticket.css',
                'resources/css/welcome_page.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: '10.28.0.153',
        },
    },
});
