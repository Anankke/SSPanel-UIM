import { _get } from '../js/fetch'

export default {
  data: function () {
    return {
      validate: '',
      captcha: ''
    }
  },
  methods: {
    loadCaptcha (id) {
      if (this.globalConfig.recaptchaSiteKey !== null) {
        this.$nextTick(function () {
          this.grecaptchaRender(id)
        })
      }
    },
    loadGT (id) {
      if (this.globalConfig.captchaProvider === 'geetest') {
        this.$nextTick(function () {
          _get('/getCaptcha', 'include')
            .then((r) => {
              let GeConfig = {
                gt: r.GtSdk.gt,
                challenge: r.GtSdk.challenge,
                product: 'embed'
              }

              if (parseInt(this.globalConfig.isGetestSuccess)) {
                GeConfig.offline = 0
              } else {
                GeConfig.offline = 1
              }

              let self = this

              window.initGeetest(GeConfig, function (captchaObj) {
                captchaObj.appendTo(id)
                captchaObj.onSuccess(function () {
                  self.validate = captchaObj.getValidate()
                })
                self.captcha = captchaObj
              })
            })
        })
      }
    },
    // 加载完成的时间很谜
    grecaptchaRender (id) {
      setTimeout(() => {
        if (!window.grecaptcha || !window.grecaptcha.render) {
          this.grecaptchaRender(id)
        } else {
          window.grecaptcha.render(id)
        }
      }, 300)
    }
  }
}
