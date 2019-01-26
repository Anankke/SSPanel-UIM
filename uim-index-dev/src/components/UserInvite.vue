<template>
  <div>
    <div class="user-invite-title flex align-center">
      <div class="card-title">邀请链接</div>
      <div class="relative flex align-center justify-center text-center">
        <transition name="fade" mode="out-in">
          <label v-show="showToolInput" class="relative" for>
            <input
              @keyup.13="submitToolInput"
              v-model="toolInputContent"
              :data-type="toolInputType"
              class="coupon-checker tips tips-blue"
              type="text"
              :placeholder="placeholder"
            >
            <button @click="submitToolInput" class="btn-forinput" name="check">
              <span class="fa fa-arrow-up"></span>
            </button>
            <button @click="hideToolInput" class="btn-forinput" name="reset">
              <span class="fa fa-refresh"></span>
            </button>
          </label>
        </transition>
        <uim-tooltip v-show="showOrderCheck" class="uim-tooltip-top flex justify-center">
          <div slot="tooltip-inner">
            <span v-if="toolInputType === 'buy'">
              <div>确认购买
                <span class="text-red">$[toolInputContent]$</span> 个吗？总价为
                <span class="text-red">￥$[totalPrice]$</span>
              </div>
            </span>
            <span v-if="toolInputType === 'custom'">确认定制链接后缀为
              <span class="text-red">$[toolInputContent]$</span> 吗？价格为
              <span class="text-red">￥$[customPrice]$</span>
            </span>
            <div>
              <button @click="submitOrder" class="tips tips-green">
                <span class="fa fa-fw fa-check"></span>
              </button>
              <button @click="hideOrderCheck" class="tips tips-red">
                <span class="fa fa-fw fa-remove"></span>
              </button>
            </div>
          </div>
        </uim-tooltip>
      </div>
      <transition name="fade" mode="out-in">
        <div v-show="showToolInput">
          <div class="flex align-center" v-if="toolInputType === 'buy'" key="buy">
            <span v-show="toolInputType === 'buy'" class="tips tips-green">￥$[invitePrice]$/次</span>
            <span v-show="toolInputType === 'buy'" class="tips tips-gold">总价：￥$[totalPrice]$</span>
          </div>
          <div class="flex align-center" v-else key="custom">
            <span v-show="toolInputType === 'custom'" class="tips tips-green">价格：￥$[customPrice]$</span>
          </div>
        </div>
      </transition>
    </div>
    <div class="card-body">
      <div class="user-invite">
        <div v-if="userCon.class !== 0">
          <div class="flex align-center wrap">
            <input
              type="text"
              v-uimclip="{ onSuccess:successCopied }"
              :data-uimclip="inviteLink"
              :class="{ 'invite-reset':inviteLinkTrans }"
              class="invite-link tips tips-blue"
              :value="inviteLink"
              readonly
            >
            <span class="invite-tools link-reset relative flex justify-center text-center">
              <button @click="showInviteReset" class="tips tips-red">
                <span class="fa fa-refresh"></span> 重置
              </button>

              <uim-tooltip v-show="inviteResetConfirm" class="uim-tooltip-top flex justify-center">
                <div slot="tooltip-inner">
                  <span>确定要重置邀请链接？</span>
                  <div>
                    <button @click="resetInviteLink" class="tips tips-green">
                      <span class="fa fa-fw fa-check"></span>
                    </button>
                    <button @click="hideInviteReset" class="tips tips-red">
                      <span class="fa fa-fw fa-remove"></span>
                    </button>
                  </div>
                </div>
              </uim-tooltip>
            </span>
            <span
              v-if="customPrice >= 0"
              class="invite-tools relative flex justify-center text-center"
            >
              <button
                @click="showCustomToolInput"
                :disabled="isToolDisabled"
                class="tips tips-cyan"
              >
                <span class="fa fa-pencil"></span> 定制
              </button>
            </span>
          </div>
          <h5>
            邀请链接剩余次数：
            <span
              :class="{ 'tips-gold-trans':inviteTimeTrans }"
              class="invite-number tips tips-gold"
            >$[userCon.invite_num]$次</span>
            <span v-if="invitePrice >= 0">
              <button
                @click="showBuyToolInput"
                :disabled="isToolDisabled"
                class="invite-tools invite-number tips tips-green"
              >
                <span class="fa fa-cny"></span> 购买
              </button>
            </span>
          </h5>
        </div>
        <div v-else>
          <h3>$[userCon.user_name]$，您不是VIP暂时无法使用邀请链接，
            <slot name="inviteToShop"></slot>
          </h3>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mixins: [userMixin, storeMap],
  computed: {
    inviteLink: function() {
      return this.baseURL + "/#/auth/register?code=" + this.code;
    },
    totalPriceCa: function() {
      return parseInt(this.toolInputContent) * parseInt(this.invitePrice);
    },
    totalPrice: function() {
      return isNaN(this.totalPriceCa) ? "" : this.totalPriceCa;
    }
  },
  data: function() {
    return {
      oldCode: "",
      code: "",
      invitePrice: "",
      customPrice: "",
      toolInputContent: "",
      placeholder: "",
      toolInputType: "",
      orderCheckContent: "",
      inviteResetConfirm: false,
      inviteLinkTrans: false,
      inviteTimeTrans: false,
      showToolInput: false,
      isToolDisabled: false,
      showOrderCheck: false,
      theUnWatch: ""
    };
  },
  methods: {
    destoryWatch() {
      if (this.theUnWatch !== "") {
        this.theUnWatch();
      }
    },
    showInviteReset() {
      this.inviteResetConfirm = true;
    },
    hideInviteReset() {
      this.inviteResetConfirm = false;
    },
    showLinkTrans() {
      this.inviteLinkTrans = true;
      setTimeout(() => {
        this.inviteLinkTrans = false;
      }, 300);
    },
    showInviteTimeTrans() {
      this.inviteTimeTrans = true;
      setTimeout(() => {
        this.inviteTimeTrans = false;
      }, 300);
    },
    resetInviteLink() {
      _get("/getnewinvotecode", "include").then(r => {
        console.log(r);
        this.code = r.arr.code.code;
        this.hideInviteReset();
        this.showLinkTrans();
        let callConfig = {
          msg: "已重置您的邀请链接，复制您的邀请链接发送给其他人！",
          icon: "fa-bell",
          time: 1500
        };
        this.callMsgr(callConfig);
      });
    },
    hideToolInput(token) {
      if (token !== 1 || !token) {
        this.code = this.oldCode;
      }
      this.showToolInput = false;
      this.isToolDisabled = false;
      this.hideOrderCheck();
      this.destoryWatch();
      setTimeout(() => {
        this.toolInputContent = "";
      }, 300);
    },
    submitToolInput() {
      switch (this.toolInputType) {
        case "buy":
          this.buyOrdercheck();
          break;
        case "custom":
          this.customOrderCheck();
          break;
      }
    },
    showBuyToolInput() {
      this.destoryWatch();
      this.code = this.oldCode;
      this.showToolInput = true;
      this.isToolDisabled = true;
      this.placeholder = "输入购买数量";
      this.toolInputType = "buy";
    },
    showCustomToolInput() {
      this.showToolInput = true;
      this.isToolDisabled = true;
      this.placeholder = "输入链接后缀";
      this.toolInputType = "custom";
      let unwatchCustom = this.$watch("toolInputContent", function(
        newVal,
        oldVal
      ) {
        this.code = newVal;
      });
      this.theUnWatch = unwatchCustom;
    },
    hideOrderCheck() {
      this.showOrderCheck = false;
    },
    buyOrdercheck() {
      if (
        isNaN(parseInt(this.toolInputContent)) ||
        this.toolInputContent === ""
      ) {
        let callConfig = {
          msg: "请输入数字",
          icon: "fa-times-circle-o",
          time: 1500
        };
        this.callMsgr(callConfig);
      } else {
        this.showOrderCheck = true;
      }
    },
    customOrderCheck() {
      if (this.toolInputContent === "") {
        let callConfig = {
          msg: "后缀不能为空",
          icon: "fa-times-circle-o",
          time: 1500
        };
        this.callMsgr(callConfig);
      } else {
        this.showOrderCheck = true;
      }
    },
    submitOrder() {
      switch (this.toolInputType) {
        case "buy":
          this.buyInvite();
          break;
        case "custom":
          this.customInvite();
          break;
      }
    },
    buyInvite() {
      let ajaxBody = {
        num: parseInt(this.toolInputContent)
      };
      _post("/user/buy_invite", JSON.stringify(ajaxBody), "include").then(r => {
        this.hideToolInput();
        if (r.ret) {
          this.reConfigResourse();
          this.showInviteTimeTrans();
          this.setInviteNum(r.invite_num);
          let callConfig = {
            msg: r.msg,
            icon: "fa-check-square-o",
            time: 1000
          };
          this.callMsgr(callConfig);
        } else {
          let callConfig = {
            msg: r.msg,
            icon: "fa-times-circle-o",
            time: 1000
          };
          this.callMsgr(callConfig);
        }
      });
    },
    customInvite() {
      this.hideToolInput(1);
      let ajaxBody = {
        customcode: this.toolInputContent
      };
      _post("/user/custom_invite", JSON.stringify(ajaxBody), "include").then(
        r => {
          if (r.ret) {
            console.log(r);
            this.reConfigResourse();
            this.showLinkTrans();
            this.code = this.oldCode = this.toolInputContent;
            let callConfig = {
              msg: r.msg,
              icon: "fa-check-square-o",
              time: 1000
            };
            this.callMsgr(callConfig);
          } else {
            this.showLinkTrans();
            this.code = this.oldCode;
            let callConfig = {
              msg: r.msg,
              icon: "fa-times-circle-o",
              time: 1000
            };
            this.callMsgr(callConfig);
          }
        }
      );
    }
  },
  mounted() {
    _get("getuserinviteinfo", "include").then(r => {
      console.log(r);
      this.code = this.oldCode = r.inviteInfo.code.code;
      this.invitePrice = r.inviteInfo.invitePrice;
      this.customPrice = r.inviteInfo.customPrice;
      console.log(this.userCon);
    });
  },
  beforeDestroy() {
    this.hideToolInput();
  }
};
</script>

