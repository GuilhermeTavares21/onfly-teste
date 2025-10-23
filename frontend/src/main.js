import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

// Pinia
import { createPinia } from 'pinia'

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#009efb',
          secondary: '#424242',
          accent: '#82B1FF',
        },
      },
    },
  },
})

const pinia = createPinia()  // <- criar instância do Pinia

const app = createApp(App)
app.use(router)
app.use(pinia)   // <- registrar Pinia antes de usar qualquer store
app.use(vuetify)
app.mount('#app')
