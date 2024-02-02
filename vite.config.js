import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import tailwind from "tailwindcss"
import autoprefixer from "autoprefixer"

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    css: {
        postcss: {
          plugins: [tailwind(), autoprefixer()],
        },
    },
});
