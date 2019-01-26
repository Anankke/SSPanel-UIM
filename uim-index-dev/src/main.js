import Vue from 'vue'
import App from './App.vue'
import Router from './router' 
import storeMap from './mixins/storeMap'
import './css/index_base.css'
import './css/index.css'

Vue.config.productionTip = false

import Messager from './components/messager.vue'
import Uimclip from './directives/uimclip'
import { _get } from './js/fetch'

Vue.directive('uimclip',Uimclip)

new Vue({
  router: Router,
  delimiters: ['$[', ']$'],
  mixins: [storeMap],
  components: {
    'uim-messager': Messager,
  },
  data: {
    routerN: 'auth',
    transType: 'slide-fade'
  },
  methods: {
    routeJudge() {
      switch (this.$route.path) {
        case '/':
          if (this.logintoken == false) {
            this.routerN = 'auth';
          } else {
            this.routerN = 'user';
          }
          break;
        default:
          this.routerN = 'index';
      }
    },
  },
  watch: {
    '$route'(to, from) {
      this.routeJudge();
      if (to.path === '/password/reset' || from.path === '/password/reset') {
        this.transType = 'rotate-fade';
      } else {
        this.transType = 'slide-fade';
      }
    }
  },
  beforeMount() {
    fetch('https://api.lwl12.com/hitokoto/v1').then((r) => {
      return r.text();
    }).then((r) => {
      this.setHitokoto(r);
    })
    _get('https://v2.jinrishici.com/one.json', 'include').then((r) => {
      this.setJinRiShiCi(r.data.content);
    })
  },
  mounted() {
    this.routeJudge();
    setTimeout(() => {
      this.setLoadState();
    }, 1000);
  },
  render: h => h(App)
}).$mount('#app')