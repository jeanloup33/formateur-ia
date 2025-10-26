/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  content: ["./index.html"],
  safelist: [
    'pt-24',
    'primary', 'secondary', 'accent', 'info',
    'dark',
    'md:hidden',
    'text-indigo-600', 'text-cyan-500', 'text-pink-500',
    'text-indigo-800',
    'bg-indigo-600', 'bg-cyan-500',
    'icon-brain', 'icon-rocket', 'icon-users', 'icon-laptop-code', 'icon-circle-check',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};