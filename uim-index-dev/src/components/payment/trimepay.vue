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
import { _post } from "../../js/fetch";

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
      tid: "",
      qrcode: {}
    };
  },
  methods: {
    setChargeType(type) {
      this.chargeType = type;
      if (type === "alipay") {
        this.hideQr();
      }
    },
    hideQr() {
      this.isQrShow = false;
      clearTimeout(this.tid);
    },
    charge() {
      let type = this.chargeType;
      let pid = 0;

      if (type === "alipay") {
        if (/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
          type = "ALIPAY_WAP";
        } else {
          type = "ALIPAY_WEB";
        }
      }

      if (type === "wechat") {
        type = "WEPAY_JSAPI";
      }

      let price = parseFloat(this.price);
      window.console.log(
        "将要使用 " + this.chargeType + " 充值" + price + "元"
      );

      if (isNaN(price)) {
        let callConfig = {
          msg: "非法的金额!",
          icon: "times-circle",
          time: 1500
        };
        this.callMsgr(callConfig);
        return;
      }
      this.callModal(() => {
        setTimeout(() => {
          let body = { price, type };
          _post("/user/payment/purchase", JSON.stringify(body), "include")
            .then(data => {
              if (data.code === 0) {
                window.console.log(data);
                this.callModal();
                if (type === "ALIPAY_WAP" || type === "ALIPAY_WEB") {
                  window.location.href = data.data;
                } else {
                  pid = data.pid;
                  this.isQrShow = true;
                  return data.data;
                }
              } else {
                window.console.log(data);
                this.callModal(() => {
                  setTimeout(() => {
                    let callConfig = {
                      msg: data.msg,
                      icon: "times-circle",
                      time: 1500
                    };
                    this.callMsgr(callConfig);
                  }, 300);
                });
              }
            })
            .then(r => {
              window.console.log(r);
              this.qrcode = new window.QRCode("trimeweqr", {
                render: "canvas",
                width: 200,
                height: 200,
                text: encodeURI(r)
              });
              this.tid = setTimeout(() => {
                this.chargeChecker(pid);
              }, 1000);
            });
        }, 300);
      });
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
    },
    chargeChecker(token) {
      let headers = {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
      };
      let params = new URLSearchParams();
      params.set("pid", token);
      _post("/payment/status", params, "include", headers).then(data => {
        if (data.result) {
          let callConfig = {
            msg: "充值成功",
            icon: "check-circle",
            time: 1500
          };
          this.callMsgr(callConfig);
          this.hideQr();
          this.reConfigResourse();
        } else {
          this.tid = setTimeout(() => {
            this.chargeChecker(token);
          }, 1000);
        }
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
