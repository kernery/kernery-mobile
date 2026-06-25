import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['mobile/ui/{viteConfig}/public/css/style.css', 'mobile/ui/{viteConfig}/public/js/script.js'],
            refresh: true,
            buildDirectory: 'ui/{viteConfig}/build'

        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
