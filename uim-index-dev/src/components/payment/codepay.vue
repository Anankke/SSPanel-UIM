<template>
  <div>
    <div class="charge-btngroup">
      <button
        @click="setChargeType('alipay')"
        class="btn-user"
        :class="{ 'index-btn-active':chargeType === 'alipay' }"
      >
        <font-awesome-icon :icon="['fab','alipay']"/>&nbsp;支付宝
      </button>
      <button
        @click="setChargeType('wechat')"
        class="btn-user"
        :class="{ 'index-btn-active':chargeType === 'wechat' }"
      >
        <font-awesome-icon icon="comments"/>&nbsp;微信
      </button>
    </div>

    <input type="text" v-model="price" class="tips tips-blue" placeholder="输入充值金额">
    <button @click="charge" class="tips tips-gold">充值</button>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";

export default {
  mixins: [userMixin, storeMap],
  data: function() {
    return {
      chargeType: "alipay",
      price: ""
    };
  },
  methods: {
    setChargeType(type) {
      this.chargeType = type;
    },
    charge() {
      let type;
      switch (this.chargeType) {
        case "alipay":
          type = 1;
          break;
        case "wechat":
          type = 3;
          break;
      }
      let price = parseFloat(this.price);
      fetch(`/user/code/codepay?price=${price}&type=${type}`).then(r => {
        window.location.href = r.url;
      });
    }
  }
};
</script>

<style>
.charge-btngroup {
  margin-bottom: 1rem;
}
.charge-btngroup button.btn-user {
  margin-right: 1rem;
  min-width: 100px;
}
</style>
