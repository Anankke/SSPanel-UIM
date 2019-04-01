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

    <transition name="fade" mode="out-in">
      <div v-if="isQrShow" class="text-center pure-g flex align-center">
        <div class="pure-u-1 pure-u-sm-1-2">
          <p>使用微信扫描二维码支付</p>
          <p>充值完毕后会自动跳转</p>
        </div>
        <div class="pure-u-1 pure-u-sm-1-2">
          <div align="center" id="trimeweqr" style="padding-top:10px;"></div>
        </div>
      </div>
    </transition>

    <transition name="fade" mode="out-in">
      <uim-modal :bindMask="isMaskShow" :bindCard="isCardShow" v-if="isMaskShow">
        <h3 slot="uim-modal-title">正在连接支付网关</h3>
        <div class="flex align-center justify-center wrap" slot="uim-modal-body">
          <div class="order-checker-content">感谢您对我们的支持，请耐心等待</div>
        </div>
      </uim-modal>
    </transition>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";
import modal from "@/components/modal.vue";
import { _get, _post } from "../../js/fetch";

export default {
  mixins: [userMixin, storeMap],
  components: {
    "uim-modal": modal
  },
  data: function() {
    return {
      chargeType: "alipay",
      price: "",
      isMaskShow: false,
      isCardShow: false,
      isQrShow: false,
      tid: ""
    };
  },
  methods: {
    setChargeType(type) {
      this.chargeType = type;
      if (type === "alipay") {
        this.hideQr();
      }
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
      _get(`/user/code/codepay?price=${price}&type=${type}`, "include").then(
        r => {
          console.log(r);
        }
      );
    },
    callModal(func) {
      if (this.isMaskShow === false) {
        this.isMaskShow = true;
        setTimeout(() => {
          this.isCardShow = true;
          if (func) {
            func();
          }
        }, 300);
      } else {
        this.isCardShow = false;
        setTimeout(() => {
          this.isMaskShow = false;
          if (func) {
            func();
          }
        }, 300);
      }
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
