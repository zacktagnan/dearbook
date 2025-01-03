import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import CKEditor from '@ckeditor/ckeditor5-vue'

import { i18nVue } from 'laravel-vue-i18n';

const html = window.document.documentElement
const darkMode = parseInt(localStorage.getItem('darkMode') || 0)
if (darkMode) {
    html.classList.add('dark')
} else {
    html.classList.remove('dark')
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18nVue, {
                resolve: async lang => {
                    const langs = import.meta.glob('../../lang/*.json');
                    return await langs[`../../lang/${lang}.json`]();
                }
            })
            .use(CKEditor)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
