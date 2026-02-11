import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Custom color palette
                'cream': {
                    DEFAULT: '#F6F0D7',
                    50: '#FDFCF7',
                    100: '#F6F0D7',
                    200: '#EDE5C0',
                    300: '#E4DAA9',
                },
                'sage': {
                    light: '#C5D89D',
                    DEFAULT: '#9CAB84',
                    dark: '#89986D',
                    50: '#E8EFD9',
                    100: '#D9E4C2',
                    200: '#C5D89D',
                    300: '#9CAB84',
                    400: '#89986D',
                    500: '#768560',
                    600: '#636E50',
                    700: '#505840',
                },
            },
        },
    },

    plugins: [forms],
};
