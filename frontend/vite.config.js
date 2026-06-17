import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

export default defineConfig({
  plugins: [react()],
  server: {
    host: true,
    port: 3000,
    proxy: {
      "/transactions": {
        target: "http://172.18.0.4:80",
        changeOrigin: true,
      },
    },
  },
});
