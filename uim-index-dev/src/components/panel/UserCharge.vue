<template>
  <div>
    <div class="flex space-between align-center">
      <div class="card-title">充值中心</div>
      <div class="card-title-right">
        <uim-dropdown>
          <template #dpbtn-content>
            <transition name="fade" mode="out-in">
              <div :key="paymentType.name">{{paymentType.name}}</div>
            </transition>
          </template>
          <template #dp-menu>
            <li
              v-for="(item,key) in menuList"
              @click="changePayementType(key)"
              :key="key"
            >{{item.name}}</li>
          </template>
        </uim-dropdown>
      </div>
    </div>
    <div class="card-body user-charge">
      <transition name="fade" mode="out-in">
        <component :is="paymentType.component"></component>
      </transition>
    </div>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";

import Dropdown from "@/components/dropdown.vue";
import Code from "@/components/payment/code.vue";
import Log from "@/components/payment/log.vue";

export default {
  mixins: [userMixin, storeMap],
  components: {
    "uim-dropdown": Dropdown,
    "payment-code": Code,
    "payment-log": Log
  },
  computed: {
    paymentType: function() {
      switch (this.currentPayment) {
        case "code":
          return this.menuList["code"];
        case "log":
          return this.menuList["log"];
      }
    }
  },
  data: function() {
    return {
      currentPayment: "code",
      menuList: {
        code: {
          name: "充值码",
          component: "payment-code"
        },
        log: {
          name: "充值记录",
          component: "payment-log"
        }
      }
    };
  },
  methods: {
    changePayementType(type) {
      this.currentPayment = type;
    }
  }
};
</script>

<style>
.user-charge .tips {
  margin-right: 0.5rem;
  margin-bottom: 0.75rem;
}
</style>
