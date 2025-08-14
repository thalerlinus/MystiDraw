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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                },
                gold: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                }
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(30px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                glow: {
                    '0%, 100%': { boxShadow: '0 0 5px rgba(251, 191, 36, 0.2)' },
                    '50%': { boxShadow: '0 0 20px rgba(251, 191, 36, 0.6)' },
                }
            },
            animation: {
                'fade-in': 'fadeIn 1s ease-out forwards',
                'float': 'float 3s ease-in-out infinite',
                'glow': 'glow 2s ease-in-out infinite',
            },
            backgroundImage: {
                'hero-gradient': 'linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%)',
                'gold-gradient': 'linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%)',
                'navy-gradient': 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
            }
        },
    },

    plugins: [forms],
};
