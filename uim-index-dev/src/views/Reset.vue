<template>
  <div class="page-pw pure-u-1 pure-g flex align-center space-around wrap">
    <div class="title-back flex align-center">PASSWORD</div>
    <div class="pure-u-1 pure-u-sm-10-24 flex space-around wrap basis-max">
      <h1>密码重置</h1>
      <div class="input-control flex wrap">
        <label for="Email" class="flex space-between align-center">
          <span>邮箱</span>
          <span>
            <router-link class="button-index" to="/auth/login">
              <font-awesome-icon icon="share" />&nbsp;返回登录页
            </router-link>
          </span>
        </label>
        <input v-model="email" type="text" name="Email">
      </div>
      <button
        @click.prevent="reset"
        @keyup.13.native="reset"
        class="auth-submit"
        id="reset"
        type="submit"
        :disabled="isDisabled"
      >重置密码</button>
    </div>
  </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'
import { _post } from '../js/fetch'

export default {
  mixins: [storeMap],
  data: function () {
    return {
      email: '',
      isDisabled: false
    }
  },
  methods: {
    reset () {
      let callConfig = {
        msg: '',
        icon: '',
        time: 1000
      }

      _post(
        '/password/reset',
        JSON.stringify({
          email: this.email
        }),
        'omit'
      ).then(r => {
        if (r.ret === 1) {
          callConfig.msg += '邮件发送成功kira~'
          callConfig.icon += 'check-circle'
          this.callMsgr(callConfig)
          window.setTimeout(() => {
            this.$router.push('/auth/login')
          }, this.globalConfig.jumpDelay)
        } else {
          callConfig.msg += 'WTF……邮件发送失败，请检查邮箱地址'
          callConfig.icon += 'times-circle'
          this.callMsgr(callConfig)
          window.setTimeout(() => {
            this.isDisabled = false
          }, 3000)
        }
      })
    }
  }
}
</script>
