import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

import vue from '@vitejs/plugin-vue'


export default defineConfig({
    plugins: [

        vue(),

        laravel({
            buildDirectory: 'public/build',
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
