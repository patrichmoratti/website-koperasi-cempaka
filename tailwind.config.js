import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:    ['DM Sans', ...defaultTheme.fontFamily.sans],
                serif:   ['Playfair Display', ...defaultTheme.fontFamily.serif],
                mono:    ['DM Mono', ...defaultTheme.fontFamily.mono],
                display: ['Playfair Display', 'Georgia', 'serif'],
            },
            colors: {
                // Primary brand palette
                green: {
                    DEFAULT: '#1A3D2E',
                    dark:    '#0f2a1e',
                    hover:   '#2a5a44',
                    light:   '#EAF3DE',
                    muted:   '#3d6b53',
                },
                gold: {
                    DEFAULT: '#C9A84C',
                    hover:   '#dbb85a',
                    dark:    '#a88330',
                    light:   '#FDF6E3',
                },
                cream: {
                    DEFAULT: '#F5F0E8',
                    dark:    '#ede7db',
                    white:   '#FDFCFA',
                },
                // Aliases for backward compat with existing app components
                primary:    { DEFAULT: '#1A3D2E', dark: '#0f2a1e', light: '#EAF3DE' },
                secondary:  { DEFAULT: '#C9A84C', dark: '#a88330' },
                mony: {
                    bg:      '#F5F0E8',
                    surface: '#FFFFFF',
                    text:    '#1C1C1A',
                    muted:   '#6B7280',
                    danger:  '#991B1B',
                    success: '#065F46',
                    warning: '#92400E',
                },
                // Status badge colors
                status: {
                    pending:  { bg: '#FEF3C7', text: '#92400E' },
                    review:   { bg: '#DBEAFE', text: '#1E40AF' },
                    active:   { bg: '#D1FAE5', text: '#065F46' },
                    rejected: { bg: '#FEE2E2', text: '#991B1B' },
                    paid:     { bg: '#EDE9FE', text: '#5B21B6' },
                },
            },
            boxShadow: {
                card:        '0 1px 3px 0 rgb(0 0 0 / 0.07), 0 1px 2px -1px rgb(0 0 0 / 0.07)',
                'card-hover':'0 8px 25px -5px rgb(0 0 0 / 0.12), 0 4px 6px -2px rgb(0 0 0 / 0.05)',
                gold:        '0 4px 20px rgb(201 168 76 / 0.3)',
                green:       '0 4px 20px rgb(26 61 46 / 0.2)',
            },
            borderRadius: {
                xl2: '1rem',
                xl3: '1.5rem',
                xl4: '2rem',
            },
            animation: {
                'fade-in':     'fadeIn .35s ease-out',
                'fade-up':     'fadeUp .5s ease-out',
                'slide-down':  'slideDown .25s ease-out',
                'float':       'float 4s ease-in-out infinite',
                'float-slow':  'float 6s ease-in-out infinite',
                'pulse-soft':  'pulseSoft 3s ease-in-out infinite',
                'spin-slow':   'spin 12s linear infinite',
                'slide-right': 'slideRight .5s ease-out',
                'count-up':    'countUp .1s ease-out',
            },
            keyframes: {
                fadeIn:    { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                fadeUp:    { '0%': { opacity: 0, transform: 'translateY(24px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                slideDown: { '0%': { opacity: 0, transform: 'translateY(-10px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                slideRight:{ '0%': { opacity: 0, transform: 'translateX(-20px)' }, '100%': { opacity: 1, transform: 'translateX(0)' } },
                float:     { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-12px)' } },
                pulseSoft: { '0%,100%': { opacity: 1 }, '50%': { opacity: 0.6 } },
            },
            backgroundImage: {
                'grain': "url(\"data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E\")",
            },
        },
    },

    plugins: [forms],
};
