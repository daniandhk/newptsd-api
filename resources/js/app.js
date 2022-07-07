import Vue from 'vue'
import App from './App.vue'
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
import Vuelidate from 'vuelidate'
import VueSweetalert2 from 'vue-sweetalert2'
import VueMask from 'v-mask'
import vco from "v-click-outside"
import Notification from 'vue-notification'
import router from './router'
import i18n from './i18n'
import store from './store'
import "./assets/scss/app.scss"
import VueScreen from 'vue-screen'
import Croppa from 'vue-croppa'
import 'vue-croppa/dist/vue-croppa.css'
import './bootstrap'

const options = {
  name: '_blank',
  specs: [
    'fullscreen=yes',
    'titlebar=yes',
    'scrollbars=yes'
  ],
  styles: [
    'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css',
    'https://unpkg.com/kidlat-css/css/kidlat.css'
  ],
  timeout: 1000,
  autoClose: true,
}

// We import JQuery
const $ = require('jquery');
// We declare it globally
window.$ = $;

Vue.config.productionTip = false;
Vue.prototype.$backendUrl = '/api';
Vue.use(BootstrapVue);
Vue.use(BootstrapVueIcons);
Vue.use(vco);
Vue.use(Vuelidate);
Vue.use(VueSweetalert2);
Vue.use(VueMask);
Vue.use(Notification);
Vue.use(require('vue-chartist'));
Vue.component('Apexchart', VueApexCharts);
Vue.use(VueScreen);
Vue.use(Croppa);

new Vue({
  router,
  store,
  i18n,
  render: h => h(App)
}).$mount('#app')