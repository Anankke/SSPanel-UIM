<template>
  <div>
    <div class="flex space-between align-center">
      <div class="card-title">充值中心</div>
      <div class="card-title-right">
        <uim-dropdown show-arrow>
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
import Trime from "@/components/payment/trimepay.vue";
import CodePay from "@/components/payment/codepay.vue";

export default {
  mixins: [userMixin, storeMap],
  components: {
    "uim-dropdown": Dropdown,
    "payment-code": Code,
    "payment-log": Log,
    "payment-trimepay": Trime,
    "payment-codepay": CodePay
  },
  computed: {
    paymentType: function() {
      switch (this.currentPayment) {
        case "trimepay":
          return this.menuList["trimepay"];
        case "codepay":
          return this.menuList["codepay"];
        case "code":
          return this.menuList["code"];
        case "log":
          return this.menuList["log"];
      }
    }
  },
  data: function() {
    return {
      currentPayment: "",
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
  },
  created() {
    let type = this.globalConfig.paymentType;
    this.currentPayment = type;
    let curPayment = {
      name: "自助充值",
      component: ""
    };
    switch (type) {
      case "trimepay":
        curPayment.component = "payment-trimepay";
        break;
      case "codepay":
        curPayment.component = "payment-codepay";
        break;
    }
    this.$set(this.menuList, this.globalConfig.paymentType, curPayment);
    window.console.log(this.menuList);
  }
};
</script>

<style>
.user-charge .tips {
  margin-right: 0.5rem;
  margin-bottom: 0.75rem;
}
</style>
