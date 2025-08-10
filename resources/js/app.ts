import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(
        `./pages/${name}.vue`, 
        import.meta.glob<DefineComponent>('./pages/**/*.vue', { eager: false }) // Lazy loading
    ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);
        
        // Performance optimizations
        app.config.performance = true;
        
        app.mount(el);
    },
    progress: {
        color: '#000000', // Match our black theme
        showSpinner: true,
    },
});

// This will set light / dark mode on page load...
initializeTheme();
