<template>
  <div>
    <div class="pure-g">
      <div class="pure-u-1 wrap flex align-center">
        <div class="card-title">套餐购买</div>
        <transition name="fade" mode="out-in">
          <label v-if="isCheckerShow" class="relative" for>
            <input
              @keyup.13="couponCheck"
              class="coupon-checker tips tips-blue"
              v-model="coupon"
              type="text"
              placeholder="优惠码"
            >
            <button @click="couponCheck" class="btn-forinput" name="check">
              <font-awesome-icon icon="arrow-up"/>
            </button>
            <button @click="hideChecker" class="btn-forinput" name="reset">
              <font-awesome-icon icon="sync-alt"/>
            </button>
          </label>
        </transition>
      </div>
    </div>
    <div class="card-body">
      <div class="user-shop">
        <div v-for="shop in shops" class="list-shop pure-g" :key="shop.id">
          <div class="pure-u-1 pure-u-sm-20-24">
            <span class="user-shop-name">{{shop.name}}</span>
            <span class="tips tips-gold">
              VIP {{shop.details.class}}/
              <span
                v-if="shop.details.class_expire !== '0'"
              >{{shop.details.class_expire}}天</span>
            </span>
            <span class="tips tips-green">￥{{shop.price}}</span>
            <span class="tips tips-cyan">
              {{shop.details.bandwidth}}G
              <span
                v-if="shop.details.reset"
              >+{{shop.details.reset_value}}G/({{shop.details.reset}}天/{{shop.details.reset_exp}}天)</span>
            </span>
            <span
              v-if="shop.details.expire !== '0'"
              class="tips tips-blue"
            >账号续期{{shop.details.expire}}天</span>
          </div>
          <div class="pure-u-1 pure-u-sm-4-24 list-shop-footer">
            <button :disabled="isDisabled" class="buy-submit" @click="buy(shop)">购买</button>
          </div>
        </div>
      </div>
    </div>

    <transition name="fade" mode="out-in">
      <uim-modal
        v-on:closeModal="callOrderChecker"
        v-on:callOrderChecker="orderCheck"
        :bindMask="isMaskShow"
        :bindCard="isCardShow"
        v-if="isMaskShow"
      >
        <h3 slot="uim-modal-title">{{modalCon.title}}</h3>
        <div class="flex align-center justify-center wrap" slot="uim-modal-body">
          <div class="order-checker-content">
            商品名称：
            <span>{{orderCheckerContent.name}}</span>
          </div>
          <div class="order-checker-content">
            优惠额度：
            <span>{{orderCheckerContent.credit}}</span>
          </div>
          <div class="order-checker-content">
            总金额：
            <span>{{orderCheckerContent.total}}</span>
          </div>
        </div>
        <div class="flex align-center" slot="uim-modal-footer">
          <uim-switch @click.native="test" v-model="orderCheckerContent.disableothers"></uim-switch>
          <span class="switch-text">关闭旧套餐自动续费</span>
        </div>
      </uim-modal>
    </transition>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";

import Shopmodal from "@/components/modal.vue";
import Switch from "@/components/switch.vue";

import { _get, _post } from "../../js/fetch";

export default {
  mixins: [userMixin, storeMap],
  components: {
    "uim-modal": Shopmodal,
    "uim-switch": Switch
  },
  data: function() {
    return {
      shops: "",
      isDisabled: false,
      coupon: "",
      isCheckerShow: false,
      ajaxBody: {
        shop: "",
        autorenew: ""
      },
      isMaskShow: false,
      isCardShow: false,
      orderCheckerContent: {
        name: "",
        credit: "",
        total: "",
        disableothers: true
      }
    };
  },
  methods: {
    buy(shop) {
      this.isDisabled = true;
      this.isCheckerShow = true;
      let callConfig = {
        msg: "请输入优惠码，如没有请直接确认",
        icon: "bell",
        time: 1500
      };
      this.callMsgr(callConfig);
      let id = shop.id.toString();
      this.$set(this.ajaxBody, "shop", id);
      this.$set(this.ajaxBody, "autorenew", shop.autoRenew);
    },
    callOrderChecker() {
      if (this.isMaskShow === false) {
        this.isMaskShow = true;
        setTimeout(() => {
          this.isCardShow = true;
        }, 300);
      } else {
        this.isCardShow = false;
        setTimeout(() => {
          this.isMaskShow = false;
          this.hideChecker();
        }, 300);
      }
    },
    couponCheck() {
      let ajaxCon = {
        coupon: this.coupon,
        shop: this.ajaxBody.shop
      };
      _post("/user/coupon_check", JSON.stringify(ajaxCon), "include").then(
        r => {
          if (r.ret === 1) {
            this.isCheckerShow = false;
            this.orderCheckerContent.name = r.name;
            this.orderCheckerContent.credit = r.credit;
            this.orderCheckerContent.total = r.total;
            this.callOrderChecker();
          } else if (r.ret === 0) {
            let callConfig = {
              msg: r.msg,
              icon: "times-circle",
              time: 1000
            };
            this.callMsgr(callConfig);
          } else {
            this.ajaxNotLogin();
          }
        }
      );
    },
    orderCheck() {
      let ajaxCon = {
        coupon: this.coupon,
        shop: this.ajaxBody.shop,
        autorenew: this.ajaxBody.autorenew,
        disableothers: this.disableothers
      };
      _post("/user/buy", JSON.stringify(ajaxCon), "include").then(r => {
        let self = this;
        if (r.ret === 1) {
          this.callOrderChecker();
          this.reConfigResourse();
          this.$emit("resourseTransTrigger");
          let callConfig = {
            msg: r.msg,
            icon: "check-circle",
            time: 1500
          };
          let animation = new Promise(function(resolve) {
            self.callOrderChecker();
            setTimeout(() => {
              resolve("done");
            }, 600);
          });
          animation.then(r => {
            this.callMsgr(callConfig);
          });
        } else if (r.ret === 0) {
          let animation = new Promise(function(resolve) {
            self.callOrderChecker();
            setTimeout(() => {
              resolve("done");
            }, 600);
          });
          let message = r.msg;
          let subPosition = message.indexOf("</br>");
          let html;
          if (subPosition !== -1) {
            message = message.substr(0, subPosition);
            html = message.substr(subPosition);
          }
          let callConfig = {
            msg: message,
            html: html,
            icon: "times-circle",
            time: 6000
          };
          animation.then(r => {
            this.callMsgr(callConfig);
          });
        } else {
          this.ajaxNotLogin();
        }
      });
    },
    hideChecker() {
      this.isCheckerShow = false;
      this.isDisabled = false;
    }
  },
  mounted() {
    _get("/getusershops", "include").then(r => {
      if (r.ret === 1) {
        this.shops = r.arr.shops;
        this.shops.forEach((el, index) => {
          this.$set(this.shops[index], "details", JSON.parse(el.content));
        });
        window.console.log(this.shops);
      } else if (r.ret === 0) {
        this.ajaxNotLogin();
      }
    });
  }
};
</script>

<style>
.user-shop-name {
  display: block;
  text-align: center;
}
.list-shop .tips {
  margin-top: 0.5rem;
}
.list-shop-footer {
  margin-top: 1rem;
  padding-top: 0.5rem;
  border-top: 1px solid #434857;
  text-align: right;
}
.list-shop:hover {
  transform: translate3D(0.5rem, 0, 0);
}
@media screen and (min-width: 35.5em) {
  .user-shop-name {
    display: inline-block;
    text-align: left;
  }
  .list-shop .tips {
    margin-top: 0;
  }
  .list-shop-footer {
    margin-top: 0;
    padding-top: 0;
    border-top: none;
  }
  .list-shop:hover {
    transform: translate3D(1rem, 0, 0);
  }
}
</style>
