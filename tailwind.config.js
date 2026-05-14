import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                surface:  '#1a1d27',
                elevated: '#242736',
                border:   '#2e3245',
                accent:   '#6366f1',
                'accent-soft': '#4f46e5',
                'text-primary': '#f1f3f9',
                'text-muted':   '#8b90a7',
                'text-faint':   '#4a4f6a',
                'status-review':   '#ef4444',
                'status-progress': '#f59e0b',
                'status-mastered': '#22c55e',
                'level-junior':  '#38bdf8',
                'level-mid':     '#a78bfa',
                'level-senior':  '#fb7185',
            },
            fontFamily: {
                mono: ['"JetBrains Mono"', 'monospace'],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};