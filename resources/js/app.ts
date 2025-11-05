import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import '../css/app.css'

createInertiaApp({
    resolve: name => import(`./Pages/${name}.vue`),
    setup({el, App, props, plugin}) {
        createApp({render: () => h(App, props)})
            .use(plugin)
            .mount(el)
    }
})