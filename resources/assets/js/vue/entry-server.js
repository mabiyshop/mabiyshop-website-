import app from './app';
import renderVueComponentToString from 'vue-server-renderer/basic';

app.$router.push(context.url);
app.$store.commit('LOADED_SSR', context.meta );

renderVueComponentToString(app, (err, html) => {
    if (err) {
        throw new Error(err);
    }
    dispatch(html);
});