import react from '@vitejs/plugin-react';
import { defineConfig, loadEnv } from 'vite';

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, '.', '');
  const apiUrl = env.VITE_API_URL || '/api';
  const proxyTarget = env.VITE_BACKEND_PROXY_TARGET || 'http://localhost:8080';
  const apiPathname = apiUrl.startsWith('http') ? new URL(apiUrl).pathname || '/api' : apiUrl;

  return {
    plugins: [react()],
    build: {
      rollupOptions: {
        output: {
          manualChunks(id) {
            if (!id.includes('node_modules')) {
              return;
            }

            if (id.includes('@tanstack/react-query')) {
              return 'query';
            }

            if (id.includes('react-hook-form') || id.includes('@hookform/resolvers') || id.includes('zod')) {
              return 'forms';
            }

            if (id.includes('react-router-dom') || id.includes('react-router')) {
              return 'router';
            }

            if (id.includes('react') || id.includes('scheduler')) {
              return 'react-vendor';
            }

            return 'vendor';
          },
        },
      },
    },
    server: {
      host: '0.0.0.0',
      port: 5173,
      proxy: {
        [apiPathname]: {
          target: proxyTarget,
          changeOrigin: true,
        },
      },
    },
  };
});
