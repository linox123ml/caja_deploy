import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
export default defineConfig(({ mode }) => ({
  base: mode === 'production' ? '/caja-postulantes/dist' : '/',
  plugins: [vue()],
  resolve: {
    alias: {
      "@": "/src",
    },
  },
}));