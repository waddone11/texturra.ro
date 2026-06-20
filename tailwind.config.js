import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';
import lineClamp from '@tailwindcss/line-clamp';

/** @type {import('tailwindcss').Config} */
export default {
    mode: 'jit', // Enable Just-In-Time mode for faster builds

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',  // If using Vue
        './resources/js/**/*.js',   // If using JavaScript
        './resources/js/**/*.jsx',  // If using React
        './resources/css/**/*.css', // If using Tailwind in CSS files
        './app/View/Components/**/*.php', // If using Laravel components
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography, aspectRatio, lineClamp],
};
