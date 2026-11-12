/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        // RentSpace brand palette
        navy: {
          50:  '#f0f4ff',
          100: '#e0e9ff',
          200: '#c0d0ff',
          300: '#93adff',
          400: '#6080ff',
          500: '#3d5afe',
          600: '#1e3a8a',   // primary — deep navy blue
          700: '#1e3072',
          800: '#16265c',
          900: '#0d1b4b',
          950: '#060e2b',
        },
        teal: {
          50:  '#f0fdfa',
          100: '#ccfbf1',
          200: '#99f6e4',
          300: '#5eead4',
          400: '#2dd4bf',
          500: '#14b8a6',   // accent — teal
          600: '#0d9488',
          700: '#0f766e',
          800: '#115e59',
          900: '#134e4a',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 2px 8px 0 rgba(30, 58, 138, 0.08)',
        'card-hover': '0 8px 24px 0 rgba(30, 58, 138, 0.15)',
      },
    },
  },
  plugins: [],
}
