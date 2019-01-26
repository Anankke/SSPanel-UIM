import Vue from 'vue'
import App from './App.vue'
import './css/index_base.css'
import './css/index.css'

Vue.config.productionTip = false;

import Uimclip from './directives/uimclip'

let validate,captcha;
window.validate = validate;
window.captha = captcha;

Vue.directive('uimclip',Uimclip)

new Vue({
  render: h => h(App)
}).$mount('#app')