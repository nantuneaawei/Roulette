import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    laravel([
      'resources/css/app.css',
      'resources/js/app.js',
    ]),
  ],
  server: {
    host: true,
    hmr: {
      host: 'localhost'
    },
    watch: {
      usePolling: true,
    },
  },
  resolve: {
    alias: {
      'vue': 'vue/dist/vue.esm-bundler.js',
      'vue-router': 'vue-router',
    }
  }
});