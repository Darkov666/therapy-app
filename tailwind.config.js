import defaultTheme from 'tailwindcss/defaultTheme';

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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Merriweather', ...defaultTheme.fontFamily.serif], // Adding a serif font for a "therapy" feel
            },
            colors: {
                // Salmon/Coral Palette (Warm & Professional)
                primary: {
                    50: '#fff1f0',
                    100: '#ffe0de',
                    200: '#ffc5c2',
                    300: '#ff9d96',
                    400: '#ff6b61',
                    500: '#f83b2f', // Vibrant Salmon
                    600: '#e52216',
                    700: '#c0180e',
                    800: '#9f170f',
                    900: '#831812',
                    950: '#480704',
                },
                // Warm Neutrals (Sand/Beige for Light, Warm Grey for Dark)
                secondary: {
                    50: '#fbf9f8',
                    100: '#f5f2f0',
                    200: '#ebe5e1',
                    300: '#dcd3cc',
                    400: '#bcaea3',
                    500: '#9e8e82',
                    600: '#837267',
                    700: '#6b5c54',
                    800: '#584c46',
                    900: '#493f3b',
                    950: '#27211f',
                },
            },
        },
    },
    darkMode: 'class', // Enable manual dark mode toggle

    plugins: [],
};
