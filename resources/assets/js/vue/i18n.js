import bn from '../../../lang/bn/bn.json';
import en from '../../../lang/en/en.json';

import Vue from 'vue';
import i18n from 'vue-i18n';
import VueI18n from 'vue-i18n';

Vue.use(i18n);

export default new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    silentTranslationWarn: true,
    messages:{
        bn:bn,
        en:en,
    }
});