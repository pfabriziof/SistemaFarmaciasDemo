/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from "vue";

require('./bootstrap');

window.Vue = require('vue');

//import 'bootstrap/dist/css/bootstrap.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'vue-datetime/dist/vue-datetime.css';

import 'chart.js/dist/Chart.css';
import { Form, HasError, AlertError } from 'vform';
import { Datetime } from 'vue-datetime';
import DataTable from 'laravel-vue-datatable';
import VueProgressBar from 'vue-progressbar';
import swal from 'sweetalert2';

import VueSweetalert2 from 'vue-sweetalert2';

import moment from 'moment';
import VueRouter from 'vue-router';
import Autocomplete from '@trevoreyre/autocomplete-vue';
import Chart from 'chart.js';
import routes from './routes';
import App from './App.vue';
import Login from './auth/LoginPage.vue';
import vuetify from './plugins/vuetify';
import '../sass/app.scss'

import "@mdi/font/css/materialdesignicons.min.css";

//--- Casl ---
import { abilitiesPlugin } from '@casl/vue';
import ability from './plugins/ability';

Vue.use(abilitiesPlugin, ability)
//--- End ---
 

Vue.use(VueSweetalert2);
Vue.use(Autocomplete);

window.Form = Form; 
window.Swal = swal;

const toast = swal.mixin({
   toast: true, 
   position: 'top-end',
   showConfirmButton: false,
   timer: 3000,
   timerProgressBar: true,
   didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
   }
});



window.Toast = toast;

Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)
Vue.component('datetime', Datetime);

Vue.component('pagination', require('laravel-vue-pagination'))

Vue.use(VueRouter)
Vue.use(DataTable);
Vue.use(Datetime);

Vue.use(VueProgressBar, {
   color: 'rgb(0, 153, 255)',
   failedColor: 'red',
   height: '4px'
});

/** FILTROS */

Vue.filter('round', function(value, decimals) {
   if(!value) {
      value = 0;
   }
 
   if(!decimals) {
     decimals = 0;
   }
 
   value = Math.round(value * Math.pow(10, decimals)) / Math.pow(10, decimals);
   
   return value.toFixed(2);
});

Vue.filter('formatDate', function(value) {
   if (value) {
     return moment(String(value)).format('DD-MM-YY')
   }
});
Vue.filter('formatTime', function(value) {
   if (value) {
     return moment(String(value)).format('LT')
   }
});
Vue.filter('zerosPadStart', function(value) {
    if (value) {
      return String(value).padStart(8,'0');
    }
 });

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    components: {
        Autocomplete,
        App,
        Login
    },
    vuetify,
});
