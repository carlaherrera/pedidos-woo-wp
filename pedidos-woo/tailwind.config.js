/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './admin/**/*.php',
    './includes/**/*.php',
    './templates/**/*.php',
  ],
  prefix: 'pwoo-', // Prefixo para evitar conflitos
  theme: {
    extend: {},
  },
  plugins: [],
  corePlugins: {
    preflight: false, // Desativa o reset CSS do Tailwind para não afetar o painel WP
  }
}
