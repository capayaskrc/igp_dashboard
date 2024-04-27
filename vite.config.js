import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // other build options...
        rollupOptions: {
            // This allows you to use imports like '@fullcalendar/core'
            external: ['@fullcalendar/core'],
            output: {
                globals: {
                    '@fullcalendar/core': 'FullCalendar', // Ensure FullCalendar is available as a global variable
                },
            },
        },
    },
});
