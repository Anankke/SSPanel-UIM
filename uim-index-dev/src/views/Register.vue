<template>
  <div class="page-auth pure-g pure-u-1 pure-u-sm-20-24">
    <div class="title-back flex align-center">REGISTER</div>
    <h1>账号注册</h1>
    <div class="flex space-around reg">
      <div class="input-control flex wrap">
        <label for="usrname">昵称</label>
        <input v-model="usrname" type="text" name="usrname">
      </div>
      <div class="input-control flex wrap">
        <label for="email">邮箱(唯一凭证请认真对待)</label>
        <input v-model="email" type="text" name="email">
      </div>
      <div class="input-control flex wrap">
        <label for="password">密码</label>
        <input v-model="passwd" type="password" name="password">
      </div>
      <div class="input-control flex wrap">
        <label for="repasswd">重复密码</label>
        <input v-model="repasswd" type="password" name="repasswd">
      </div>
      <div class="input-control flex wrap">
        <label for="imtype">选择您的联络方式</label>
        <select v-model="imtype" name="imtype" id="imtype">
          <option value="1">微信</option>
          <option value="2">QQ</option>
          <option value="3">Facebook</option>
          <option value="4">Telegram</option>
        </select>
      </div>
      <div class="input-control flex wrap">
        <label for="contect">联络方式账号</label>
        <input v-model="contect" type="text" name="contect">
      </div>
      <div v-if="globalConfig.registMode === 'invite'" class="input-control flex">
        <label for="code">邀请码(必填)</label>
        <input v-model="code" type="text" name="code">
      </div>
      <div v-if="globalConfig.isEmailVeryify === 'true'" class="input-control flex twin">
        <div class="input-control-inner flex">
          <label for="email_code">邮箱验证码</label>
          <input v-model="email_code" type="text" name="email_code">
        </div>

        <button class="auth-submit" @click="sendVerifyMail" :disabled="isVmDisabled">{{vmText}}</button>
      </div>
      <div class="input-control wrap flex align-center">
        <div v-if="globalConfig.captchaProvider === 'geetest'" id="embed-captcha-reg"></div>
        <form action="?" method="POST">
          <div
            v-if="globalConfig.recaptchaSiteKey"
            id="g-recaptcha-reg"
            class="g-recaptcha"
            data-theme="dark"
            :data-sitekey="globalConfig.recaptchaSiteKey"
          ></div>
        </form>
      </div>
    </div>

    <button
      @click="register"
      class="auth-submit"
      id="register"
      type="submit"
      :disabled="isDisabled"
    >确认注册</button>
  </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'
import storeAuth from '@/mixins/storeAuth'

import { _post } from '../js/fetch'

import tmp from '../store.js'

export default {
  mixins: [storeMap, storeAuth],
  data: function () {
    return {
      usrname: '',
      email: '',
      passwd: '',
      repasswd: '',
      contect: '',
      code: '',
      imtype: '',
      email_code: '',
      isDisabled: false,
      vmText: '获取邮箱验证码',
      isVmDisabled: false
    }
  },
  methods: {
    register () {
      this.isDisabled = true

      let ajaxCon = {
        email: this.email,
        name: this.usrname,
        passwd: this.passwd,
        repasswd: this.repasswd,
        wechat: this.contect,
        imtype: this.imtype,
        code: this.code
      }

      let callConfig = {
        msg: '',
        icon: '',
        time: 1000
      }

      if (this.globalConfig.isEmailVeryify === 'true') {
        ajaxCon.emailcode = this.email_code
      }

      if (this.globalConfig.registMode !== 'invite') {
        ajaxCon.code = 0
        if (this.getCookie('code') !== '') {
          ajaxCon.code = this.getCookie('code')
        }
      }

      if (this.globalConfig.enableRegCaptcha !== 'false') {
        switch (this.globalConfig.captchaProvider) {
          case 'recaptcha':
            ajaxCon.recaptcha = window.grecaptcha.getResponse()
            break
          case 'geetest':
            if (this.validate !== '') {
              ajaxCon.geetest_challenge = this.validate.geetest_challenge
              ajaxCon.geetest_validate = this.validate.geetest_validate
              ajaxCon.geetest_seccode = this.validate.geetest_seccode
            } else {
              callConfig.msg += '请滑动验证码来完成验证。'
            }
            break
        }
      }

      _post('/auth/register', JSON.stringify(ajaxCon), 'include').then(r => {
        if (r.ret === 1) {
          callConfig.msg = '注册成功meow~'
          callConfig.icon = 'check-circle'
          this.callMsgr(callConfig)
          window.setTimeout(() => {
            this.$router.replace('/auth/login')
          }, this.globalConfig.jumpDelay)
        } else {
          callConfig.msg = `WTF……注册失败,${r.msg}`
          callConfig.icon += 'times-circle'
          this.callMsgr(callConfig)
          window.setTimeout(() => {
            this.isDisabled = false
          }, 3000)
        }
      })
    },
    // dumplin：轮子1.js读取url参数//nymph: 重拼字符串
    getQueryVariable (variable) {
      var query = window.location.hash.substring(1).split('?')[1]
      if (typeof query === 'undefined') {
        return ''
      }
      var vars = query.split('&')
      for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=')
        if (pair[0] === variable) {
          return pair[1]
        }
      }
      return ''
    },
    // dumplin:轮子2.js写入cookie
    setCookie (cname, cvalue, exdays) {
      var d = new Date()
      d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000)
      var expires = 'expires=' + d.toGMTString()
      document.cookie = cname + '=' + cvalue + '; ' + expires
    },
    // dumplin:轮子3.js读取cookie
    getCookie (cname) {
      var name = cname + '='
      var ca = document.cookie.split(';')
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim()
        if (c.indexOf(name) === 0) return c.substring(name.length, c.length)
      }
      return ''
    },
    time (time) {
      if (time === 0) {
        this.isVmDisabled = false
        this.vmText = '获取验证码'
        time = 60
      } else {
        this.isVmDisabled = true
        this.vmText = '重新发送(' + time + ')'
        time = time - 1
        setTimeout(() => {
          this.time(time)
        }, 1000)
      }
    },
    sendVerifyMail () {
      let time = tmp.state.wait
      this.time(time)

      let ajaxCon = {
        email: this.email
      }

      _post('auth/send', JSON.stringify(ajaxCon), 'omit').then(r => {
        if (r.ret) {
          let callConfig = {
            msg: 'biu~邮件发送成功',
            icon: 'check-circle',
            time: 1000
          }
          this.callMsgr(callConfig)
        } else {
          let callConfig = {
            msg: 'emm……邮件发送失败',
            icon: 'times-circle',
            time: 1000
          }
          this.callMsgr(callConfig)
        }
      })
    },
    registerBindEnter (e) {
      if (this.$route.path === '/auth/register' && e.keyCode === 13) {
        this.register()
      }
    }
  },
  mounted () {
    // dumplin:读取url参数写入cookie，自动跳转隐藏url邀请码
    if (this.getQueryVariable('code') !== '') {
      this.setCookie('code', this.getQueryVariable('code'), 30)
      this.$router.replace('/auth/register')
    }
    // dumplin:读取cookie，自动填入邀请码框
    if (this.globalConfig.registMode === 'invite') {
      if (this.getCookie('code') !== '') {
        this.code = this.getCookie('code')
      }
    }

    document.addEventListener('keyup', this.registerBindEnter, false)

    // 验证加载
    if (this.globalConfig.enableRegCaptcha === 'false') {
      return
    }
    this.loadCaptcha('g-recaptcha-reg')
    this.loadGT('#embed-captcha-reg')
  },
  beforeRouteLeave (to, from, next) {
    document.removeEventListener('keyup', this.registerBindEnter, false)
    next()
  }
}
</script>
