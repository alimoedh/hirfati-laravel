import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {

    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/**/*.php",
    ],
darkMode: ['class', '[data-theme="dark"]'],
    theme: {
        extend: {
            fontFamily: {
                cairo: ['Cairo', 'sans-serif'],
            },
            colors: {
                // الألوان الأساسية
                brand: {
                    50:  '#F0F4FB',
                    100: '#D9E2F0',
                    200: '#B3C5E0',
                    300: '#8CA8D0',
                    400: '#4A6FA5',
                    500: '#2D4E80',
                    600: '#1E3A5F',
                    700: '#152C4A',
                    800: '#0F1E3C',
                    900: '#0A1428',
                },
                gold: {
                    50:  '#FDF9EF',
                    100: '#FAF0D7',
                    200: '#F4E1AF',
                    300: '#EBCE7E',
                    400: '#E5B968',
                    500: '#D4A24C',
                    600: '#B8873A',
                    700: '#966B2E',
                    800: '#7A5628',
                    900: '#654724',
                },
                // ألوان الحالات
                success: {
                    DEFAULT: '#10B981',
                    light: '#D1FAE5',
                    dark: '#065F46',
                },
                warning: {
                    DEFAULT: '#F59E0B',
                    light: '#FEF3C7',
                    dark: '#92400E',
                },
                danger: {
                    DEFAULT: '#EF4444',
                    light: '#FEE2E2',
                    dark: '#991B1B',
                },
                info: {
                    DEFAULT: '#3B82F6',
                    light: '#DBEAFE',
                    dark: '#1E40AF',
                },
                // خلفيات
                surface: {
                    light: '#F5F7FA',
                    DEFAULT: '#FFFFFF',
                    dark: '#F8FAFC',
                },
            },
            boxShadow: {
                'soft': '0 2px 12px rgba(15, 30, 60, 0.06)',
                'card': '0 4px 24px rgba(15, 30, 60, 0.08)',
                'lift': '0 12px 32px rgba(15, 30, 60, 0.12)',
                'glow': '0 0 24px rgba(212, 162, 76, 0.35)',
                'glow-lg': '0 0 40px rgba(212, 162, 76, 0.5)',
            },
            borderRadius: {
                'xl':  '12px',
                '2xl': '16px',
                '3xl': '24px',
                '4xl': '32px',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-up': 'fadeUp 0.5s ease-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'pulse-gold': 'pulseGold 2s infinite',
                'float': 'float 6s ease-in-out infinite',
                'shimmer': 'shimmer 2s linear infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { opacity: '0', transform: 'translateX(-20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                pulseGold: {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgba(212, 162, 76, 0.4)' },
                    '50%': { boxShadow: '0 0 0 12px rgba(212, 162, 76, 0)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-1000px 0' },
                    '100%': { backgroundPosition: '1000px 0' },
                },
            },
            backdropBlur: {
                xs: '2px',
            },
        },
    },
       plugins: [
        forms,
        typography,
    ],
}


