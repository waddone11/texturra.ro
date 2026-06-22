import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
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
                sans: [
                    'Figtree',
                    'ui-sans-serif',
                    'system-ui',
                    'sans-serif',
                    '"Apple Color Emoji"',
                    '"Segoe UI Emoji"',
                    '"Segoe UI Symbol"',
                    '"Noto Color Emoji"',
                ],
                // Homepage redesign 2026: editorial serif headings + DM Sans UI.
                display: ['"Playfair Display"', 'Georgia', 'serif'],
                dm: ['"DM Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
        },
    },

    plugins: [forms, typography],
};
