{{ lang_import }}

const i18n = createI18n({
    locale: 'da', // set locale
    fallbackLocale: 'en', // set fallback locale
    formatFallbackMessages: true,
    silentTranslationWarn: true,
    silentFallbackWarn: true,
    messages: {
        {{ lang_map}}
    }
})
