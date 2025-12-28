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

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                // Primary Colors - Calming & Professional
                primary: {
                    50: '#e6f5f2',
                    100: '#ccebe5',
                    200: '#99d7cb',
                    300: '#66c3b1',
                    400: '#33af97',
                    500: '#0D7C66', // Islamic Green - Main brand color
                    600: '#0a6352',
                    700: '#084a3d',
                    800: '#053229',
                    900: '#031914',
                },
                // Gold Accent
                gold: {
                    50: '#fdf9e6',
                    100: '#fbf3cd',
                    200: '#f7e79b',
                    300: '#f3db69',
                    400: '#efcf37',
                    500: '#DAA520', // Gold Accent
                    600: '#ae841a',
                    700: '#836313',
                    800: '#57420d',
                    900: '#2c2106',
                },
                // Deep Blue
                deep: {
                    50: '#e8ebf2',
                    100: '#d1d7e5',
                    200: '#a3afcb',
                    300: '#7587b1',
                    400: '#475f97',
                    500: '#1E3A8A', // Deep Blue
                    600: '#182e6e',
                    700: '#122353',
                    800: '#0c1737',
                    900: '#060c1c',
                },
                // Neutral Colors
                surface: {
                    50: '#fafafa',
                    100: '#f5f5f5',
                    200: '#eeeeee',
                    300: '#e0e0e0',
                    400: '#bdbdbd',
                    500: '#9e9e9e',
                    600: '#757575',
                    700: '#616161',
                    800: '#424242',
                    900: '#212121',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Cal Sans', 'Inter', ...defaultTheme.fontFamily.sans],
                arabic: ['Amiri', 'Scheherazade New', 'serif'],
            },
            borderRadius: {
                '4xl': '2rem',
            },
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'glow': '0 0 20px rgba(13, 124, 102, 0.3)',
                'glow-gold': '0 0 20px rgba(218, 165, 32, 0.3)',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.5s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
