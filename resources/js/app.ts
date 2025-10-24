import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ui from '@nuxt/ui/vue-plugin'
 
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';
 
createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(
    `./Pages/${name}.vue`,
    import.meta.glob<DefineComponent>('./Pages/**/*.vue')
  ),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ui)
      .mount(el);
  },
});