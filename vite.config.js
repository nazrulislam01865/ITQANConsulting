import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/starpmaminul/portfolio.css',
                'resources/js/starpmaminul/portfolio.js',
                'resources/css/starpmaminul/admin.css',
                'resources/js/starpmaminul/admin.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
