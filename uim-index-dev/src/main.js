import Vue from 'vue'
import App from './App.vue'
import './css/index_base.css'
import './css/index.css'

import Uimclip from './directives/uimclip'

Vue.config.productionTip = false

let validate, captcha
window.validate = validate
window.captha = captcha

Vue.directive('uimclip', Uimclip)

new Vue({
  render: h => h(App)
}).$mount('#app')
