/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './web/**/*.{php,html,js,ts,jsx,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        'soft-white': '#F9FAFB',
        'soft-grey': '#E5E7EB',
        'soft-blue': '#3B82F6',
        'soft-green': '#10B981',
        'hard-grey': '#111827',
      },
    },
  },
  plugins: [],
}

