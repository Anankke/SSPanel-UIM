<template>
  <div class="page-auth pure-g pure-u-1 pure-u-sm-20-24 wrap">
    <div class="title-back flex align-center">LOGIN</div>
    <h1>登录</h1>
    <div class="pure-u-1 basis-max" :class="[ isTgEnabled ? 'pure-u-sm-11-24' : 'pure-u-sm-1-2' ]">
      <div class="input-control flex wrap">
        <label for="Email">邮箱</label>
        <input v-model="email" type="text" name="Email">
      </div>
      <div class="input-control flex wrap">
        <label for="Password">密码</label>
        <input v-model="passwd" type="password" name="Password">
      </div>
      <div class="input-control flex wrap">
        <label for="stepcode">两步验证（未设置请忽略）</label>
        <input v-model="stepcode" type="stepcode" name="stepcode">
      </div>
      <div class="input-control flex wrap">
        <div v-if="globalConfig.captchaProvider === 'geetest'" id="embed-captcha-login"></div>
        <form action="?" method="POST">
          <div
            v-if="globalConfig.recaptchaSiteKey"
            id="g-recaptcha-login"
            class="g-recaptcha"
            data-theme="dark"
            :data-sitekey="globalConfig.recaptchaSiteKey"
          ></div>
        </form>
      </div>
      <button
        @click.prevent="login"
        @keyup.13.native="login"
        class="auth-submit"
        id="login"
        type="submit"
        :disabled="isDisabled"
      >确认登录</button>
      <div class="input-control flex">
        <div class="input-inner flex no-wrap space-between">
          <uim-checkbox v-model="remember_me">
            <template #content>
              <span>记住我</span>
            </template>
          </uim-checkbox>
          <router-link class="link" to="/password/reset">忘记密码？</router-link>
        </div>
      </div>
    </div>
    <div
      v-if="globalConfig.enable_telegram === 'true'"
      class="pure-u-1 pure-u-sm-11-24 pure-g auth-tg"
    >
      <h3>Telegram登录</h3>
      <div>
        <p>Telegram OAuth一键登陆</p>
      </div>
      <p id="telegram-alert">正在载入 Telegram，如果长时间未显示请刷新页面或检查代理</p>
      <div class="text-center" id="telegram-login-box"></div>
      <p>
        或者添加机器人账号
        <a :href="telegramHref">@{{globalConfig.telegram_bot}}</a>，发送下面的数字/二维码验证码给它
      </p>
      <transition name="fade" mode="out-in">
        <div v-if="!isTgtimeout" class="pure-g pure-u-20-24" key="notTimeout">
          <div class="text-center qr-center pure-u-11-24">
            <div id="telegram-qr" class="flex space-around"></div>
          </div>
          <div class="pure-u-11-24">
            <div class="auth-submit" id="code_number">{{globalConfig.login_number}}</div>
          </div>
        </div>
        <div v-else class="pure-g space-around" key="timeout">
          <div class="auth-submit pure-u-18-24 tg-timeout">验证方式已过期，请刷新页面后重试</div>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

import storeMap from "@/mixins/storeMap";
import storeAuth from "@/mixins/storeAuth";

import Checkbox from "@/components/checkbox.vue";

import { _post } from "../js/fetch";

export default {
  mixins: [storeMap, storeAuth],
  components: {
    "uim-checkbox": Checkbox
  },
  computed: mapState({
    telegramHref: function() {
      return "https://t.me/" + this.globalConfig.telegram_bot;
    },
    isTgEnabled: function() {
      return this.globalConfig.enable_telegram === "true";
    }
  }),
  data: function() {
    return {
      email: "",
      passwd: "",
      stepcode: "",
      remember_me: false,
      isDisabled: false,
      isTgtimeout: false
    };
  },
  methods: {
    login() {
      this.isDisabled = true;

      let ajaxCon = {
        email: this.email,
        passwd: this.passwd,
        code: this.stepcode,
        remember_me: this.remember_me
      };

      let callConfig = {
        msg: "",
        icon: "",
        time: 1000
      };

      if (this.globalConfig.enableLoginCaptcha !== "false") {
        switch (this.globalConfig.captchaProvider) {
          case "recaptcha":
            ajaxCon.recaptcha = window.grecaptcha.getResponse();
            break;
          case "geetest":
            if (this.validate !== "") {
              ajaxCon.geetest_challenge = this.validate.geetest_challenge;
              ajaxCon.geetest_validate = this.validate.geetest_validate;
              ajaxCon.geetest_seccode = this.validate.geetest_seccode;
            } else {
              callConfig.msg += "请滑动验证码来完成验证。";
            }
            break;
        }
      }

      _post("/auth/login", JSON.stringify(ajaxCon), "include").then(r => {
        if (r.ret === 1) {
          callConfig.msg += "登录成功Kira~";
          callConfig.icon += "check-circle";
          this.callMsgr(callConfig);
          window.setTimeout(() => {
            this.setLoginToken(true);
            this.$router.replace("/user/panel");
          }, this.globalConfig.jumpDelay);
        } else {
          callConfig.msg = `登录失败Boommm,${r.msg}`;
          callConfig.icon += "times-circle";
          this.callMsgr(callConfig);
          window.setTimeout(() => {
            this.isDisabled = false;
          }, 3000);
        }
      });
    },
    telegramRender() {
      let el = document.createElement("script");
      document.getElementById("telegram-login-box").append(el);
      el.onload = function() {
        document.getElementById("telegram-alert").outerHTML = "";
      };
      el.src = "https://telegram.org/js/telegram-widget.js?4";
      el.setAttribute("data-size", "large");
      el.setAttribute("data-telegram-login", this.globalConfig.telegram_bot);
      el.setAttribute(
        "data-auth-url",
        this.globalConfig.base_url + "/auth/telegram_oauth"
      );
      el.setAttribute("data-request-access", "write");

      let telegram_qrcode = "mod://login/" + this.globalConfig.login_token;
      let qrcode = new window.QRCode(document.getElementById("telegram-qr"));
      qrcode.clear();
      qrcode.makeCode(telegram_qrcode);
    },
    tgAuthTrigger(tid) {
      if (this.logintoken === 1) {
        return;
      }

      let callConfig = {
        msg: "",
        icon: "",
        time: 1000
      };
      _post(
        "/auth/qrcode_check",
        JSON.stringify({
          token: this.globalConfig.login_token,
          number: this.globalConfig.login_number
        }),
        "include"
      )
        .then(r => {
          if (r.ret > 0) {
            clearTimeout(tid);
            _post(
              "/auth/qrcode_login",
              JSON.stringify({
                token: this.globalConfig.login_token,
                number: this.globalConfig.login_number
              }),
              "include"
            ).then(r => {
              if (r.ret) {
                callConfig.msg += "登录成功Kira~";
                callConfig.icon += "check-circle";
                this.callMsgr(callConfig);
                window.setTimeout(() => {
                  this.setLoginToken(true);
                  this.$router.replace("/user/panel");
                }, this.globalConfig.jumpDelay);
              }
            });
          } else if (r.ret === -1) {
            this.isTgtimeout = true;
          }
        })
        .catch(r => {
          clearTimeout(tid);
          throw r;
        });
      tid = setTimeout(() => {
        this.tgAuthTrigger(tid);
      }, 2500);
    },
    loginBindEnter(e) {
      if (this.$route.path === "/auth/login" && e.keyCode === 13) {
        this.login();
      }
    }
  },
  mounted() {
    document.addEventListener("keyup", this.loginBindEnter, false);

    if (this.globalConfig.enable_telegram === "true") {
      this.telegramRender();
      let tid = setTimeout(() => {
        this.tgAuthTrigger(tid);
      }, 2500);
    }

    if (this.globalConfig.enableLoginCaptcha === "false") {
      return;
    }
    this.loadCaptcha("g-recaptcha-login");
    this.loadGT("#embed-captcha-login");
  },
  beforeRouteLeave(to, from, next) {
    document.removeEventListener("keyup", this.loginBindEnter, false);
    next();
  }
};
</script>

<style>
#login {
  margin-bottom: 1rem;
}
.input-inner {
  flex-basis: 80%;
}
.input-control a.link {
  color: #d1335b;
  transition: all 0.3s;
}
.input-control a.link:hover {
  color: #13c2c2;
}
@media screen and (min-width: 35.5em) {
  .input-inner {
    flex-basis: 65%;
  }
}
</style>
