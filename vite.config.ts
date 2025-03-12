import preserveDirectives from 'rollup-preserve-directives';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';
import { defineConfig } from 'vite';
import dts from 'vite-plugin-dts';

// https://vitejs.dev/config/
export default defineConfig({
  build: {
    lib: {
      entry: resolve(__dirname, 'src/main.ts'),
      formats: ['es'],
      fileName: 'index',
    },
    copyPublicDir: false,
    rollupOptions: {
      external: [
        'classnames',
        'react-cropper',
        'react-dom',
        'react-dropzone',
        'react-modal',
        'react',
        'react/jsx-runtime',
      ],
    },
  },
  plugins: [
    react(),
    preserveDirectives(),
    dts({
      tsconfigPath: resolve(__dirname, 'tsconfig.app.json'),
      rollupTypes: true,
    }),
  ],
});
