import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/portfolio.css',
                'resources/js/portfolio.js',
                'resources/css/admin.css',
                'resources/js/admin.js',
                'resources/css/auth-login.css',
                'resources/js/auth-login.js',
                'resources/css/auth-otp.css',
                'resources/js/auth-otp.js',
            ],
            refresh: true,
        }),
    ],
});
