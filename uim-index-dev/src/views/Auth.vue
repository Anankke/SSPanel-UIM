<template>
  <div class="auth pure-g align-center">
    <div class="pure-u-1 pure-u-sm-4-24 flex wrap space-around auth-links">
      <router-link
        v-for="links in routerLinks"
        @click.native="setButtonState"
        :class="{ active:links.isActive }"
        class="button-round flex align-center"
        :to="links.href"
        :key="links.id"
      >
        <font-awesome-layers class="fa-2x">
          <font-awesome-icon icon="circle" :style="{ color: 'white' }"/>
          <font-awesome-icon :icon="links.icon" :transform="links.iconTransform" :style="{ color: 'black' }"/>
        </font-awesome-layers>
        <span>&nbsp;{{links.content}}</span>
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
          icon: "sign-in-alt",
          iconTransform: "shrink-6",
          isActive: false
        },
        register: {
          id: "R_AUTH_1",
          href: "/auth/register",
          content: "注册",
          icon: "user-plus",
          iconTransform: "shrink-7 left-1",
          isActive: false
        },
        reset: {
          id: "R_PW_0",
          href: "/password/reset",
          content: "密码重置",
          icon: "unlock-alt",
          iconTransform: "shrink-6",
          isActive: false
        }
      }
    };
  },
  methods: {
    setButtonState() {
      for (let key in this.routerLinks) {
        if (this.$route.path === this.routerLinks[key].href) {
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
