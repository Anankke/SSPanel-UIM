<template>
  <div id="app">
    <transition name="loading-fade" mode="out-in">
      <div class="loading flex align-center" v-if="isLoading === 'loading'" key="loading">
        <div class="spinner"></div>
      </div>

      <div v-cloak v-else-if="isLoading === 'loaded'" class="flex wrap" key="loaded">
        <div class="nav pure-g">
          <div class="pure-u-1-2 logo-sm flex align-center">
            <a href="/indexold" class="flex align-center">
              <img class="logo" src="/images/logo_white.png" alt="logo">
              <div class="info">
                <div class="name">{{globalConfig.indexMsg.appname}}</div>
                <div class="sign">{{globalConfig.indexMsg.jinrishici}}</div>
              </div>
            </a>
          </div>
          <div class="pure-u-1-2 auth-sm flex align-center">
            <transition name="fade" mode="out-in">
              <router-link class="button-index" :to="globalGuide.href" :key="routerN">
                <span>
                  <i class="fa" :class="globalGuide.icon"></i>
                  <span class="hide-sm"> {{globalGuide.content}}</span>
                </span>
              </router-link>
            </transition>
          </div>
        </div>
        <div class="main pure-g">
          <transition :name="transType" mode="out-in">
            <router-view :routermsg="globalConfig.indexMsg"></router-view>
          </transition>
        </div>
        <div class="footer pure-g">
          <div class="pure-u-1 pure-u-sm-1-2 staff">
            POWERED BY
            <a href="./staff">SSPANEL-UIM</a>
          </div>
          <div
            class="pure-u-1 pure-u-sm-1-2 time"
            :class="{ enableCrisp:globalConfig.crisp === 'true' }"
          >&copy;{{globalConfig.indexMsg.date}} {{globalConfig.indexMsg.appname}}</div>
        </div>

        <transition name="slide-fade" mode="out-in">
          <uim-messager v-show="msgrCon.isShow">
            <i slot="icon" :class="msgrCon.icon"></i>
            <span slot="msg">{{msgrCon.msg}}</span>
            <div v-if="msgrCon.html !== ''" slot="html" v-html="msgrCon.html"></div>
          </uim-messager>
        </transition>
      </div>
    </transition>
  </div>
</template>

<script>
import Router from "./router";
import storeMap from "@/mixins/storeMap";
import Messager from "./components/messager.vue";

import { _get } from "./js/fetch";

export default {
  router: Router,
  mixins: [storeMap],
  components: {
    "uim-messager": Messager
  },
  computed: {
    globalGuide: function() {
      switch (this.routerN) {
        case "index":
          return {
            icon: "fa-home",
            content: "回到首页",
            href: "/"
          };
          break;
        case "auth":
          return {
            icon: "fa-key",
            content: "登录/注册",
            href: "/auth/login"
          };
          break;
        case "user":
          return {
            icon: "fa-user",
            content: "用户中心",
            href: "/user/panel"
          };
          break;
      }
    }
  },
  data: function() {
    return {
      routerN: "auth",
      transType: "slide-fade"
    };
  },
  methods: {
    routeJudge() {
      switch (this.$route.path) {
        case "/":
          if (this.logintoken == false) {
            this.routerN = "auth";
          } else {
            this.routerN = "user";
          }
          break;
        default:
          this.routerN = "index";
      }
    }
  },
  watch: {
    $route(to, from) {
      this.routeJudge();
      if (to.path === "/password/reset" || from.path === "/password/reset") {
        this.transType = "rotate-fade";
      } else {
        this.transType = "slide-fade";
      }
    }
  },
  beforeMount() {
    fetch("https://api.lwl12.com/hitokoto/v1")
      .then(r => {
        return r.text();
      })
      .then(r => {
        this.setHitokoto(r);
      });
    _get("https://v2.jinrishici.com/one.json", "include").then(r => {
      this.setJinRiShiCi(r.data.content);
    });
  },
  mounted() {
    this.routeJudge();
    setTimeout(() => {
      this.setLoadState();
    }, 1000);
  }
};
</script>


<style>
.slide-fade-enter-active,
.fade-enter-active,
.loading-fade-enter-active,
.rotate-fade-enter-active,
.loading-fadex-enter-active,
.slide-fadex-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-leave-active,
.fade-leave-active,
.loading-fade-leave-active,
.rotate-fade-leave-active,
.loading-fadex-leave-active,
.slide-fadex-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.loading-fade-enter {
  transform: scaleY(0.75);
  opacity: 0;
}
.loading-fadex-enter {
  transform: scaleX(0.75);
  opacity: 0;
}
.slide-fade-enter {
  transform: translateY(-20px);
  opacity: 0;
}
.slide-fadex-enter {
  transform: translateX(-20px);
  opacity: 0;
}
.slide-fadex-enter-to {
  transform: translateX(0px);
  opacity: 1;
}
.rotate-fade-enter {
  transform: rotateY(90deg);
  -webkit-transform: rotateY(90deg);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
.slide-fadex-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
.rotate-fade-leave-to {
  transform: rotateY(90deg);
  -webkit-transform: rotateY(90deg);
  opacity: 0;
}
.fade-enter,
.fade-leave-to,
.loading-fade-leave-to,
.loading-fadex-leave-to {
  opacity: 0;
}
</style>
