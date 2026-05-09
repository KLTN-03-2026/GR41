/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js}'],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
        accent: {
          500: '#10b981',
          600: '#059669',
        },
        surface: '#fafbfc',
        'surface-soft': '#f4f6fa',
        'surface-sunken': '#eef1f6',
        ink: {
          900: '#0b1220',
          700: '#1f2937',
          500: '#475569',
          400: '#64748b',
          300: '#94a3b8',
        },
        'primary-blue': '#2563eb',
        'accent-emerald': '#10b981',
        'surface-gray': '#fafbfc',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
      },
      boxShadow: {
        soft: '0 1px 2px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.06)',
        lift: '0 4px 8px rgba(15,23,42,0.06), 0 16px 32px rgba(15,23,42,0.10)',
        glow: '0 0 0 1px rgba(37,99,235,0.15), 0 8px 32px rgba(37,99,235,0.25)',
        card: '0 2px 8px rgba(15,23,42,0.06)',
      },
      backgroundImage: {
        'hero-mesh':
          'radial-gradient(at 20% 20%, #3b82f6 0px, transparent 50%), radial-gradient(at 80% 0%, #6366f1 0px, transparent 50%), radial-gradient(at 50% 100%, #10b981 0px, transparent 50%), linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3730a3 100%)',
        shimmer: 'linear-gradient(90deg, transparent, rgba(255,255,255,0.5), transparent)',
      },
      animation: {
        'fade-up': 'fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) both',
        'fade-in': 'fadeIn 0.5s ease-out both',
        'scale-in': 'scaleIn 0.4s cubic-bezier(0.22,1,0.36,1) both',
        shimmer: 'shimmer 1.6s infinite linear',
        float: 'float 6s ease-in-out infinite',
        'gradient-pan': 'gradientPan 12s ease infinite',
        'pulse-soft': 'pulseSoft 2.5s ease-in-out infinite',
        caret: 'caret 1s steps(2) infinite',
      },
      keyframes: {
        fadeUp: {
          '0%': { opacity: '0', transform: 'translateY(24px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        scaleIn: {
          '0%': { opacity: '0', transform: 'scale(0.96)' },
          '100%': { opacity: '1', transform: 'scale(1)' },
        },
        shimmer: {
          '0%': { transform: 'translateX(-100%)' },
          '100%': { transform: 'translateX(100%)' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-12px)' },
        },
        gradientPan: {
          '0%, 100%': { backgroundPosition: '0% 50%' },
          '50%': { backgroundPosition: '100% 50%' },
        },
        pulseSoft: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.6' },
        },
        caret: {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0' },
        },
      },
    },
  },
  plugins: [],
}
