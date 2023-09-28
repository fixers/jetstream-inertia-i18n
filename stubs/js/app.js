import './bootstrap';
import '../css/app.css';

import {createApp, h } from 'vue';
import {createInertiaApp, router} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {ZiggyVue} from '../../vendor/tightenco/ziggy/dist/vue.m';
import VueMatomo from 'vue-matomo'
import { createI18n } from 'vue-i18n'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
import daLang from '../../lang/da.json';

const i18n = createI18n({
    locale: 'da', // set locale
    fallbackLocale: 'en', // set fallback locale
    formatFallbackMessages: true,
    silentTranslationWarn: true,
    silentFallbackWarn: true,
    messages: {
        da: daLang,
    }
})

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({el, App, props, plugin, title}) {
        const app = createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(VueMatomo, {
                host: 'https://matomo.pakkeshop.dev',
                siteId: 1,
                requireCookieConsent: true,
                enableLinkTracking: true,
            })
            .use(i18n)
            .mount(el);

        router.on('navigate', (event) => {
            if(!!event.detail.page.props.cookieConsent) {
                window._paq.push(['setCookieConsentGiven']);
                window._paq.push(['trackPageView']);
                if(event.detail.page.props.auth.user) {
                    window._paq.push(['setUserId', page.props.auth.user.id]);
                }

            }
        });


        return app;
    },
    progress: {
        color: '#4B5563',
    },
});

