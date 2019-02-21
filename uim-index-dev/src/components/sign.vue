<template>
  <div
    :class="{ 'uim-signer-ready':sign.isSignShow,'uim-signer-transition':sign.transition }"
    class="uim-signer"
  >
    <div class="uim-signer-container" :style="{ top:containerTop(),transform:containerTransform }">
      <div
        @click.stop
        :class="{ 'uim-signer-main-shadow':showSigner }"
        class="uim-signer-main"
        ref="signerMain"
      >
        <button
          @click="checkin"
          :class="{ 'uim-signer-btn-success':!userCon.isAbleToCheckin }"
          class="uim-signer-btn"
        >
          <transition name="signer" mode="out-in">
            <div v-if="userCon.isAbleToCheckin" key="notChekined">
              <p>点击，或摇一摇手机</p>
              <p>签到</p>
            </div>
            <div v-else key="chekined">
              <p>
                <font-awesome-icon icon="check" size="4x" />
              </p>
              <p>今日已签到</p>
            </div>
          </transition>
        </button>
        <div class="flex wrap">
          <div v-if="globalConfig.captchaProvider === 'geetest'" id="embed-captcha-user"></div>
          <form action="?" method="POST">
            <div
              v-if="globalConfig.recaptchaSiteKey"
              id="g-recaptcha-user"
              class="g-recaptcha"
              data-theme="dark"
              :data-sitekey="globalConfig.recaptchaSiteKey"
            ></div>
          </form>
        </div>
      </div>
      <div :style="{ top:drawerTop() }" class="uim-signer-drawer">
        <button :class="{ 'uim-signer-drawer-active':showSigner }" @click.stop="signerTrigger">
          <font-awesome-icon icon="chevron-down" :class="{ 'uim-signer-rotate':showSigner }" />&nbsp;签到
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'
import storeAuth from '@/mixins/storeAuth'
import { _post } from '../js/fetch'

export default {
  mixins: [storeMap, storeAuth],
  computed: {
    containerTransform: function () {
      return this.showSigner
        ? 'translateY(' + this.drawerTop() + ')'
        : 'translateY(0px)'
    }
  },
  data: function () {
    return {
      showSigner: false,
      shakeEvent: ''
    }
  },
  methods: {
    containerTop () {
      if (this.$refs.signerMain) {
        return -this.$refs.signerMain.clientHeight + 'px'
      } else {
        return '-250px'
      }
    },
    drawerTop () {
      if (this.$refs.signerMain) {
        return this.$refs.signerMain.clientHeight + 'px'
      } else {
        return '250px'
      }
    },
    signerTrigger () {
      if (this.showSigner === false) {
        this.showSigner = true
      } else {
        this.showSigner = false
      }
    },
    hideSigner () {
      this.showSigner = false
    },
    checkin () {
      let body = {}

      let callConfig = {
        msg: '',
        icon: '',
        time: 0
      }

      if (this.globalConfig.enableCheckinCaptcha !== 'false') {
        switch (this.globalConfig.captchaProvider) {
          case 'recaptcha':
            body.recaptcha = window.grecaptcha.getResponse()
            break
          case 'geetest':
            if (this.validate !== '') {
              body.geetest_challenge = this.validate.geetest_challenge
              body.geetest_validate = this.validate.geetest_validate
              body.geetest_seccode = this.validate.geetest_seccode
            } else {
              callConfig.msg += '请滑动验证码来完成验证。'
            }
            break
        }
      }

      _post('/user/checkin', JSON.stringify(body), 'include').then(r => {
        if (r.ret) {
          window.console.log(r)
          callConfig.msg += r.msg
          callConfig.icon = 'check-circle'
          callConfig.time = 4000
          this.setAllResourse({ isAbleToCheckin: false })
          setTimeout(() => {
            this.signerTrigger()
            this.callMsgr(callConfig)
            this.addNewUserCon(r.trafficInfo)
            this.TraffictransTrigger()
          }, 1000)
        } else {
          window.console.log(r)
          callConfig.msg += r.msg
          callConfig.icon = 'times-circle'
          callConfig.time = 1500
          this.callMsgr(callConfig)
          this.signerTrigger()
        }
      })
    },
    shakeEventDidOccur () {
      if ('vibrate' in navigator) {
        navigator.vibrate(500)
      }

      this.checkin()
    }
  },
  mounted () {
    window.addEventListener('shake', this.shakeEventDidOccur, false)

    let app = document.getElementById('app')
    app.addEventListener('click', this.hideSigner, false)

    if (this.globalConfig.enableCheckinCaptcha === 'false') {
      return
    }
    this.loadCaptcha('g-recaptcha-user')
    this.loadGT('#embed-captcha-user')
  },
  beforeDestroy () {
    window.removeEventListener('shake', this.shakeEventDidOccur, false)

    let app = document.getElementById('app')
    app.removeEventListener('click', this.hideSigner, false)
  }
}
</script>

<style>
.uim-signer {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  margin-right: auto;
  margin-left: auto;
  width: 300px;
  text-align: center;
  z-index: -1;
  opacity: 0;
}
.uim-signer.uim-signer-ready {
  z-index: 5;
  opacity: 1;
}
.uim-signer-transition {
  transition: all 0.4s;
}
.uim-signer-main,
.uim-signer-container,
.uim-signer-main button,
.uim-signer-drawer svg {
  transition: all 0.4s;
}
.uim-signer-container {
  position: relative;
  top: -250px;
}
.uim-signer-main {
  background: white;
  padding: 1rem;
  border-radius: 0 0 10px 10px;
  box-shadow: none;
  position: absolute;
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
}
.uim-signer-btn {
  color: #d1335b;
  background: transparent;
  border-radius: 50%;
  border: 8px solid #d1335b;
  height: 200px;
  width: 200px;
  padding: 0.5rem 1rem;
  outline: none;
}
.uim-signer-btn-success {
  color: #52c41a;
  border-color: #52c41a;
  cursor: default;
}
.uim-signer-drawer {
  position: absolute;
  top: 250px;
  left: 0;
  right: 0;
}
.uim-signer-drawer button {
  outline: none;
  font-size: 13px;
  border: 1px solid;
  border-top: 0;
  padding: 0.3rem 1rem;
  border-radius: 0 0 6px 6px;
  background: transparent;
  color: white;
}
.uim-signer-drawer button:hover,
.uim-signer-drawer button.uim-signer-drawer-active {
  background: white;
  color: black;
}
.uim-signer-rotate {
  transform: rotateZ(180deg);
}
.uim-signer-main.uim-signer-main-shadow {
  box-shadow: 0 0 5px 1px gray;
}
#embed-captcha-user .geetest_holder.geetest_wind {
  width: 100% !important;
  margin-top: 1.5rem;
}
</style>
