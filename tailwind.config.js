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

                primary: {
                  'alt': '#008114',
                  'base': '#035944',
                  50: '#81aca2',
                  100: '#689b8f',
                  200: '#4f8b7c',
                  300: '#357a69',
                  400: '#1c6a57',
                  500: '#035944',
                  600: '#03503d',
                  700: '#024736',
                  800: '#023e30',
                  900: '#023529',
                },
                secondary: {
                    'base': '#cc9c21',
                    50: '#e6ce90',
                    100: '#e0c47a',
                    200: '##dbba64',
                    300: '#d6b04d',
                    400: '#d1a637',
                    500: '#cc9c21',
                    600: '#b88c1e',
                    700: '#a37d1a',
                    800: '#8f6d17',
                    900: '#7a5e14',
                    },
              }
        },
    },

    plugins: [
        require('@tailwindcss/typography'),
        forms
    ],
};
