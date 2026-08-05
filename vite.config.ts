import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vite';

/**
 * Plugin bundles are standalone: they build to dist/, get published to
 * public/vendor/plugins/user-activity, and register themselves against the
 * global Vanguard runtime at load time. Installing the plugin therefore never
 * requires rebuilding the host application.
 *
 * Vue, Inertia and the Vanguard UI kit are externalised onto that runtime so a
 * plugin shares the host's single Vue instance rather than booting a second one.
 */
export default defineConfig({
    plugins: [vue(), tailwindcss()],

    resolve: {
        alias: {
            '@': new URL('./resources/js', import.meta.url).pathname,
        },
    },

    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: 'resources/js/plugin.ts',
            external: ['vue', '@inertiajs/vue3', '@vanguard/ui'],
            output: {
                // IIFE rather than ESM so the externals resolve through globals
                // on window.Vanguard instead of bare import specifiers, which a
                // browser could not resolve.
                format: 'iife',
                name: 'VanguardUserActivity',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash][extname]',
                globals: {
                    vue: 'Vanguard.vue',
                    '@inertiajs/vue3': 'Vanguard.inertia',
                    '@vanguard/ui': 'Vanguard.ui',
                },
            },
        },
    },
});
