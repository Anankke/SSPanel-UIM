import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'

Vue.config.productionTip = false

new Vue({
  router,
  store,
  delimiters: ['$[', ']$'],
  mixins: [storeMap],
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