import Vue from 'vue'
import App from './App.vue'
import './css/index_base.css'
import './css/index.css'

import {
  library
} from '@fortawesome/fontawesome-svg-core'

import {
  faCircle,
  faHome,
  faKey,
  faUser,
  faSignInAlt,
  faUserPlus,
  faUnlockAlt,
  faShare,
  faCheckCircle,
  faTimesCircle,
  faBell,
  faCheck,
  faChevronLeft,
  faChevronRight,
  faChevronDown,
  faAngleLeft,
  faAngleRight,
  faTimes,
  faSyncAlt,
  faArrowUp,
  faReply,
  faPencilAlt,
  faYenSign,
  faComments,
  faCodeBranch,
  faCaretDown
} from '@fortawesome/free-solid-svg-icons'

import {
  faCopy
} from '@fortawesome/free-regular-svg-icons'

import {
  faAlipay
} from '@fortawesome/free-brands-svg-icons'

import {
  FontAwesomeIcon,
  FontAwesomeLayers,
  FontAwesomeLayersText
} from '@fortawesome/vue-fontawesome'

import Uimclip from './directives/uimclip'

let iconList = [
  faCircle,
  faHome,
  faKey,
  faUser,
  faSignInAlt,
  faUserPlus,
  faUnlockAlt,
  faShare,
  faCheckCircle,
  faTimesCircle,
  faBell,
  faCheck,
  faChevronLeft,
  faChevronRight,
  faChevronDown,
  faAngleLeft,
  faAngleRight,
  faTimes,
  faSyncAlt,
  faArrowUp,
  faReply,
  faPencilAlt,
  faYenSign,
  faCopy,
  faAlipay,
  faComments,
  faCodeBranch,
  faCaretDown
]
library.add(...iconList)

Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)
Vue.component('font-awesome-layers-text', FontAwesomeLayersText)

Vue.config.productionTip = false

let validate, captcha
window.validate = validate
window.captha = captcha

Vue.directive('uimclip', Uimclip)

new Vue({
  render: h => h(App)
}).$mount('#app')
