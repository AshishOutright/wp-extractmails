module.exports = {
  content: ["./**/*.{php,js}"],
  // Keep commonly used arbitrary utilities when rebuilding
  safelist: [
    "max-w-[800px]",
    "max-w-[1550px]",
    "max-w-[810px]",
    "max-w-[790px]",
    "text-[clamp(2.025rem,5vw,3.7rem)]",
  ],
  theme: {
    extend: {
      fontFamily: {
        cormorant: ["'Cormorant_Garamond'", "serif"],
        manrope: ["Manrope", "ui-sans-serif", "system-ui"],
      },
    },
  },
  plugins: [],
};
