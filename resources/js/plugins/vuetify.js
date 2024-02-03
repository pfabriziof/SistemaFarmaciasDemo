// Vuetify Documentation https://vuetifyjs.com

import Vue from 'vue'
//import Vuetify from 'vuetify/lib/framework'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import ripple from 'vuetify/lib/directives/ripple'

Vue.use(Vuetify, { directives: { ripple } })

const theme = {
  primary: '#4DB2ED',
  secondary: '#37474F',
  accent: '#127bc7',
  info: '#00CAE3',
  success: '#4CAF50',
  warning: '#FB8C00',
  error: '#FF5252',
}

export default new Vuetify({
  breakpoint: { mobileBreakpoint: 960 },
  icons: {
    values: { expand: 'mdi-menu-down' },
  },
  font: {
    family: 'Quicksand'
  },
  theme: {
    themes: {
      dark: theme,
      light: theme,
    },
  },
})
