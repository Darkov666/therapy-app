import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { i18nVue } from 'laravel-vue-i18n';
import { ZiggyVue } from 'ziggy-js';

import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

// Explicit imports moved to resolve function

createInertiaApp({
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {

        const app = createApp({ render: () => h(App, props) });

        // .use(plugin) // Intertia plugin - required for page render, keep it.
        // .use(ZiggyVue) // Commented out
        // .use(pinia)    // Commented out

        app.use(plugin);
        app.use(ZiggyVue);
        app.use(pinia);

        app.use(i18nVue, {
            lang: props.initialPage.props.locale || 'es',
            resolve: async lang => {
                const langs = import.meta.glob('./lang/*.json');
                if (langs[`./lang/${lang}.json`]) {
                    return await langs[`./lang/${lang}.json`]();
                }
                return {};
            },
        });

        app.mount(el);
        return app;
    },
});

