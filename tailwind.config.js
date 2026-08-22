import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                pos: {
                    bg:        '#2B2420', // page background — espresso charcoal
                    surface:   '#3D3229', // cards, panels — roasted-bean brown
                    surface2:  '#4A3D31', // hover/raised surface, one step lighter
                    crema:     '#E8A855', // primary action, active tab, total row
                    copper:    '#C97A3D', // secondary accent, hover state, featured badge
                    sage:      '#7C9473', // in-stock / success / completed status
                    rust:      '#B5473D', // low-stock / destructive / error
                    cream:     '#F2EAE0', // primary text on dark surfaces
                    'cream-dim': '#B8AA9C', // secondary/muted text, placeholders
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
