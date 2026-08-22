import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, DefineComponent } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { ensureCsrfCookie } from '@/lib/axios';

const appName = import.meta.env.VITE_APP_NAME || 'Coffee POS';

// Bootstrap CSRF cookie from Sanctum for SPA requests
ensureCsrfCookie().catch((err) => {
    console.warn('Sanctum CSRF cookie initialization notice:', err);
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#f97316',
    },
});
