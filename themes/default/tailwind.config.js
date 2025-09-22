/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./pages/**/*.php",
    "./layouts/**/*.php", 
    "./partials/**/*.php",
    "./assets/js/**/*.js",
    "./*.php",
    "./**/*.html",
    "./**/*.php",
    "./**/*.js"
  ],
  corePlugins: {
    // Please make sure this is not set to false
    preflight: true,
  },
  theme: {
    extend: {},
  },
  plugins: [],
}
