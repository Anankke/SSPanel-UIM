<template>
  <div class="page-user pure-u-1">
    <div class="title-back flex align-center">NODELIST</div>

    <transition name="loading-fadex" mode="out-in">
      <div class="loading flex align-center" v-if="userLoadState === 'beforeload'">NODELIST</div>

      <div class="loading flex align-center" v-else-if="userLoadState === 'loading'" key="loading">
        <div class="spinnercube">
          <div class="cube1"></div>
          <div class="cube2"></div>
        </div>
      </div>

      <div class="usrcenter text-left pure-g space-around" v-else-if="userLoadState === 'loaded'">
        <div class="pure-u-1 pure-u-xl-6-24 pure-g usrcenter-left">节点详情</div>
        <div class="pure-u-1 pure-u-xl-17-24">节点列表</div>
      </div>
    </transition>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import { _get } from "../js/fetch";

export default {
  mixins: [storeMap],
  data: function() {
    return {
      userLoadState: "beforeload"
    };
  },
  mounted() {
    let self = this;
    this.userLoadState = "loading";

    _get("/getnodelist", "include")
      .then(r => {
        window.console.log(r);
      })
      .then(r => {
        setTimeout(() => {
          self.userLoadState = "loaded";
        }, 1000);
      });
  },
  beforeRouteLeave(to, from, next) {
    if (
      to.matched.some(function(record) {
        return record.meta.alreadyAuth;
      })
    ) {
      next(false);
    } else {
      this.setSignSet({ isSignShow: false });
      setTimeout(() => {
        this.setSignSet({ transition: false });
        next();
      }, 200);
    }
  }
};
</script>
