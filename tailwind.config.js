import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    500: '#f97316',
                    600: '#ea580c',
                    700: '#c2410c',
                },
                neutral: {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    500: '#71717a',
                    700: '#3f3f46',
                    900: '#18181b',
                },
                success: {
                    50: '#f0fdf4',
                    500: '#22c55e',
                    600: '#16a34a',
                },
                warning: {
                    50: '#fffbeb',
                    500: '#f59e0b',
                    600: '#d97706',
                },
                danger: {
                    50: '#fef2f2',
                    500: '#ef4444',
                    600: '#dc2626',
                },
                info: {
                    50: '#eff6ff',
                    500: '#3b82f6',
                    600: '#2563eb',
                },
            },
            fontFamily: {
                sans: ['"Public Sans"', ...defaultTheme.fontFamily.sans],
                display: ['"Fraunces"', 'serif'],
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },
        },
    },

    plugins: [forms],
};
