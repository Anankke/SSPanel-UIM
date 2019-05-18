import Vue from 'vue'
import Vuex from 'vuex'
import UserTmp from './stores/UserStore'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    isLoading: 'loading',
    wait: 60,
    logintoken: false,
    msgrCon: {
      msg: '操作成功',
      html: '',
      icon: 'check-circle',
      isShow: false
    },
    modalCon: {
      isMaskShow: false,
      isCardShow: false,
      title: '订单确认',
      bodyContent: ''
    },
    globalConfig: {
      captchaProvider: '',
      recaptchaSiteKey: '',
      jumpDelay: '',
      isGetestSuccess: '',
      registMode: '',
      crisp: '',
      base_url: '',
      isEmailVeryify: '',
      login_token: '',
      login_number: '',
      telegram_bot: '',
      enable_telegram: '',
      enableLoginCaptcha: '',
      enableRegCaptcha: '',
      enableCheckinCaptcha: '',
      indexMsg: {
        appname: '',
        hitokoto: '',
        date: '',
        jinrishici: ''
      },
      paymentType: ''
    }
  },
  mutations: {
    SET_LOADSTATE (state) {
      state.isLoading = 'loaded'
    },
    SET_LOGINTOKEN (state, n) {
      state.logintoken = n
    },
    SET_MSGRCON (state, config) {
      if (state.msgrCon.html !== '') {
        state.msgrCon.html = ''
      }
      state.msgrCon.msg = config.msg
      state.msgrCon.icon = config.icon
      state.msgrCon.html = config.html
    },
    ISSHOW_MSGR (state, boolean) {
      state.msgrCon.isShow = boolean
    },
    ISSHOW_MODAL_MASK (state, boolean) {
      state.modalCon.isMaskShow = boolean
    },
    ISSHOW_MODAL_CARD (state, boolean) {
      state.modalCon.isCardShow = boolean
    },
    SET_GLOBALCONFIG (state, config) {
      state.logintoken = config.isLogin
      state.globalConfig.base_url = config.base_url
      state.globalConfig.captchaProvider = config.captcha_provider
      state.globalConfig.recaptchaSiteKey = config.recaptcha_sitekey
      state.globalConfig.jumpDelay = config.jump_delay
      state.globalConfig.isGetestSuccess = config.isGetestSuccess
      state.globalConfig.registMode = config.register_mode
      state.globalConfig.isEmailVeryify = config.enable_email_verify
      state.globalConfig.enableLoginCaptcha = config.enable_logincaptcha
      state.globalConfig.enableRegCaptcha = config.enable_regcaptcha
      state.globalConfig.enableCheckinCaptcha = config.enable_checkin_captcha
      state.globalConfig.login_token = config.login_token
      state.globalConfig.login_number = config.login_number
      state.globalConfig.telegram_bot = config.telegram_bot
      state.globalConfig.crisp = config.enable_mylivechat
      state.globalConfig.enable_telegram = config.enable_telegram
      state.globalConfig.indexMsg.appname = config.appName
      state.globalConfig.indexMsg.date = config.dateY
      state.globalConfig.paymentType = config.payment_type
    },
    SET_HITOKOTO (state, content) {
      state.globalConfig.indexMsg.hitokoto = content
    },
    SET_JINRISHICI (state, content) {
      state.globalConfig.indexMsg.jinrishici = content
    }
  },
  actions: {
    CALL_MSGR ({
      dispatch,
      commit,
      state
    }, config) {
      if (state.msgrCon.isShow === true) {
        commit('ISSHOW_MSGR', false)
        setTimeout(() => {
          dispatch('CALL_MSGR', config)
        }, 300)
      } else {
        commit('SET_MSGRCON', config)
        commit('ISSHOW_MSGR', true)
        window.setTimeout(function () {
          commit('ISSHOW_MSGR', false)
        }, config.time)
      }
    },
    CALL_MODAL ({
      commit,
      state
    }, config) {
      if (state.modalCon.isMaskShow === false) {
        commit('ISSHOW_MODAL_MASK', true)
        window.setTimeout(() => {
          commit('ISSHOW_MODAL_CARD', true)
        }, 300)
      } else {
        commit('ISSHOW_MODAL_CARD', false)
        window.setTimeout(() => {
          commit('ISSHOW_MODAL_MASK', false)
        }, 300)
      }
    }
  },
  modules: {
    userState: UserTmp
  }
})
