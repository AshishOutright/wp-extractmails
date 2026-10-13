/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.php", "./assets/js/**/*.js", "./inc/**/*.js"],
  plugins: [require("@tailwindcss/forms")],
};
