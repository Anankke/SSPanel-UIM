<template>
  <div class="auth pure-g align-center">
    <div class="pure-u-1 pure-u-sm-4-24 flex wrap space-around auth-links">
      <router-link
        v-for="(links,key) in routerLinks"
        @click.native="setButtonState"
        :class="{ active:links.isActive }"
        class="button-round flex align-center"
        :to="links.href"
        :key="links.id"
      >
        <span class="fa-stack">
          <i class="fa fa-circle fa-stack-2x"></i>
          <i :class="links.icon"></i>
        </span>
        <span>{{links.content}}</span>
      </router-link>
    </div>
    <transition name="slide-fade" mode="out-in">
      <router-view></router-view>
    </transition>
  </div>
</template>

<script>
export default {
  props: ["routermsg"],
  data: function() {
    return {
      routerLinks: {
        login: {
          id: "R_AUTH_0",
          href: "/auth/login",
          content: "登录",
          icon: ["fa", "fa-sign-in", "fa-stack-1x", "fa-inverse"],
          isActive: false
        },
        register: {
          id: "R_AUTH_1",
          href: "/auth/register",
          content: "注册",
          icon: ["fa", "fa-user-plus", "fa-stack-1x", "fa-inverse"],
          isActive: false
        },
        reset: {
          id: "R_PW_0",
          href: "/password/reset",
          content: "密码重置",
          icon: ["fa", "fa-unlock-alt", "fa-stack-1x", "fa-inverse"],
          isActive: false
        }
      }
    };
  },
  methods: {
    setButtonState() {
      for (let key in this.routerLinks) {
        if (this.$route.path == this.routerLinks[key].href) {
          this.routerLinks[key].isActive = true;
        } else {
          this.routerLinks[key].isActive = false;
        }
      }
    }
  },
  watch: {
    $route: "setButtonState"
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.setButtonState();
    });
  },
  beforeRouteLeave(to, from, next) {
    this.setButtonState();
    next();
  }
};
</script>

