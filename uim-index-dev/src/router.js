import Vue from 'vue'
import Router from 'vue-router'
import Root from './views/Root.vue'
import Auth from './views/Auth.vue'
import Login from './views/Login.vue'
import Register from './views/Register.vue'
import Password from './views/Password.vue'
import Reset from './views/Reset.vue'
import User from './views/User.vue'
import Panel from './views/Panel.vue'
import Node from './views/Node.vue'

import {
  _get
} from './js/fetch'
import tmp from './store'

Vue.use(Router)

let globalConfig

const router = new Router({
  routes: [{
      path: '/',
      components: {
        default: Root
      },
      meta: {
        title: 'Index'
      }
    },
    {
      path: '/auth/',
      component: Auth,
      redirect: '/auth/login',
      meta: {
        alreadyAuth: true
      },
      children: [{
          path: 'Login',
          component: Login,
          meta: {
            title: 'login'
          }
        },
        {
          path: 'register',
          component: Register,
          meta: {
            title: 'Register'
          }
        }
      ]
    },
    {
      path: '/password/',
      component: Password,
      redirect: '/password/reset',
      meta: {
        alreadyAuth: true
      },
      children: [{
        path: 'reset',
        component: Reset,
        meta: {
          title: 'Reset'
        }
      }]
    },
    {
      path: '/user/',
      component: User,
      redirect: '/user/panel',
      meta: {
        requireAuth: true
      },
      children: [{
          path: 'panel',
          component: Panel,
          meta: {
            title: 'Usercenter'
          }
        },
        {
          path: 'node',
          component: Node,
          meta: {
            title: 'Usercenter'
          }
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  if (!globalConfig) {
    _get('/globalconfig', 'include').then((r) => {
      if (r.ret === 1) {
        globalConfig = r.globalConfig
        if (globalConfig.geetest_html && globalConfig.geetest_html.success) {
          globalConfig.isGetestSuccess = '1'
          tmp.commit('SET_GLOBALCONFIG', globalConfig)
        } else {
          globalConfig.isGetestSuccess = '0'
          tmp.commit('SET_GLOBALCONFIG', globalConfig)
        }
      }
    }).then((r) => {
      navGuardsForEach()
    })
  } else {
    navGuardsForEach()
  }

  function navGuardsForEach() {
    if (tmp.state.logintoken && to.matched.some(function (record) {
        return record.meta.alreadyAuth
      })) {
      next('/user/panel')
    } else if (!tmp.state.logintoken && to.matched.some(function (record) {
        return record.meta.requireAuth
      })) {
      next('/auth/login')
    } else {
      document.title = tmp.state.globalConfig.indexMsg.appname + ' - ' + to.meta.title
      next()
    }
  }
})

export default router
