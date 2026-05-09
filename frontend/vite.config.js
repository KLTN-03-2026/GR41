import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import { fileURLToPath } from 'url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

export default defineConfig(({ mode }) => ({
  plugins: [
    vue(),
    {
      name: 'html-site-url',
      transformIndexHtml(html) {
        const env = loadEnv(mode, process.cwd(), '')
        const base = String(env.VITE_SITE_URL || 'http://localhost:5173').replace(/\/$/, '')
        return html.replaceAll('__SITE_URL__', base)
      },
    },
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
  build: {
    sourcemap: true,    // ← thêm
    minify: false,      // ← thêm tạm thời
  },
}))
