/** Tokens follow DESIGN.md "Executive Precision". */
export default {
  content: ['./resources/views/**/*.blade.php', './app/Livewire/**/*.php'],
  theme: {
    extend: {
      fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'Segoe UI', 'Roboto', 'sans-serif'] },
      colors: {
        navy: { 900: '#0F172A', 800: '#1E293B' },
        ink: { DEFAULT: '#0F172A', 700: '#334155', 500: '#64748B' },
        canvas: '#F8FAFC', well: '#F1F5F9', line: '#E2E8F0',
        accent: { 600: '#2563EB', 500: '#3B82F6' },
        crit: { fg: '#EF4444', bg: '#FEF2F2', bd: '#FECACA' },
        warn: { fg: '#D97706', bg: '#FFFBEB', bd: '#FDE68A' },
        good: { fg: '#059669', bg: '#ECFDF5', bd: '#A7F3D0' },
        pend: { fg: '#6366F1', bg: '#EEF2FF', bd: '#C7D2FE' },
        unv: { fg: '#8B5CF6', bg: '#F5F3FF', bd: '#DDD6FE' },
      },
      borderRadius: { DEFAULT: '0.25rem', lg: '0.5rem' },
      boxShadow: { card: '0 1px 2px 0 rgba(15,23,42,0.04)', float: '0 10px 15px -3px rgba(15,23,42,0.08), 0 4px 6px -2px rgba(15,23,42,0.03)' },
      maxWidth: { page: '1600px' },
    },
  },
};
