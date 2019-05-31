<template>
  <div>
    <div class="user-invite-title pure-g">
      <div class="pure-u-1-2 pure-u-sm-18-24 flex align-center wrap">
        <div class="card-title">邀请链接</div>
        <div class="user-invite-subtitle">
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
                  <font-awesome-icon icon="arrow-up"/>
                </button>
                <button @click="hideToolInput" class="btn-forinput" name="reset">
                  <font-awesome-icon icon="sync-alt"/>
                </button>
              </label>
            </transition>
            <uim-tooltip v-show="showOrderCheck" class="uim-tooltip-top flex justify-center">
              <template #tooltip-inner>
                <span v-if="toolInputType === 'buy'">
                  <div>
                    确认购买
                    <span class="text-red">{{toolInputContent}}</span>&nbsp;个吗？总价为
                    <span class="text-red">￥{{totalPrice}}</span>
                  </div>
                </span>
                <span v-if="toolInputType === 'custom'">
                  确认定制链接后缀为
                  <span class="text-red">{{toolInputContent}}</span>&nbsp;吗？价格为
                  <span class="text-red">￥{{customPrice}}</span>
                </span>
                <div>
                  <button @click="submitOrder" class="tips tips-green">
                    <font-awesome-icon icon="check" fixed-width/>
                  </button>
                  <button @click="hideOrderCheck" class="tips tips-red">
                    <font-awesome-icon icon="times" fixed-width/>
                  </button>
                </div>
              </template>
            </uim-tooltip>
          </div>
          <transition name="fade" mode="out-in">
            <div class="toolinput-state" v-show="showToolInput">
              <div class="flex align-center" v-if="toolInputType === 'buy'" key="buy">
                <span v-show="toolInputType === 'buy'" class="tips tips-green">￥{{invitePrice}}/次</span>
                <span v-show="toolInputType === 'buy'" class="tips tips-gold">总价：￥{{totalPrice}}</span>
              </div>
              <div class="flex align-center" v-else key="custom">
                <span
                  v-show="toolInputType === 'custom'"
                  class="tips tips-green"
                >价格：￥{{customPrice}}</span>
              </div>
            </div>
          </transition>
        </div>
      </div>
      <transition name="fade">
        <div v-if="showInviteLog" class="pure-u-1-2 pure-u-sm-6-24 flex-end flex align-center">
          <button @click="closeInviteLog" class="btn-user">
            <font-awesome-icon icon="reply"/>&nbsp;返回
          </button>
        </div>
      </transition>
    </div>
    <div class="card-body">
      <div class="user-invite">
        <div v-if="userCon.class !== 0">
          <transition name="fade" mode="out-in">
            <div v-if="!showInviteLog" key="closeLog">
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
                    <font-awesome-icon icon="sync-alt"/>&nbsp;重置
                  </button>

                  <uim-tooltip
                    v-show="inviteResetConfirm"
                    class="uim-tooltip-top flex justify-center"
                  >
                    <template #tooltip-inner>
                      <span>确定要重置邀请链接？</span>
                      <div>
                        <button @click="resetInviteLink" class="tips tips-green">
                          <font-awesome-icon icon="check" fixed-width/>
                        </button>
                        <button @click="hideInviteReset" class="tips tips-red">
                          <font-awesome-icon icon="times" fixed-width/>
                        </button>
                      </div>
                    </template>
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
                    <font-awesome-icon icon="pencil-alt"/>&nbsp;定制
                  </button>
                </span>
              </div>
              <h5>
                邀请链接剩余次数：
                <span
                  :class="{ 'tips-gold-trans':inviteTimeTrans }"
                  class="invite-number tips tips-gold"
                >{{userCon.invite_num}}次</span>
                <span v-if="invitePrice >= 0">
                  <button
                    @click="showBuyToolInput"
                    :disabled="isToolDisabled"
                    class="invite-number tips tips-green"
                  >
                    <font-awesome-icon icon="yen-sign"/>&nbsp;购买
                  </button>
                </span>
              </h5>
              <h5>
                每邀请1位用户注册，您会获得
                <span class="tips tips-sm tips-cyan">{{invite_gift}}G</span>&nbsp;流量奖励
              </h5>
              <h5>
                对方将获得
                <span class="tips tips-sm tips-cyan">￥{{invite_get_money}}</span>&nbsp;作为初始资金
              </h5>
              <h5>
                对方充值时您还会获得对方充值金额
                <span class="tips tips-sm tips-cyan">{{code_payback}}%</span>&nbsp;的返利
              </h5>
              <h4>
                已获得返利：
                <span class="tips tips-cyan">￥{{paybacks_sum}}</span>
              </h4>
              <button @click="checkInviteLog" class="tips tips-gold">查看返利明细</button>
            </div>
            <div v-else key="viewLog">
              <div class="user-table-container">
                <uim-table>
                  <template #uim-th>
                    <th>ID</th>
                    <th>被邀请用户ID</th>
                    <th>获得返利</th>
                  </template>

                  <template #uim-tbd>
                    <tr class="uim-tr-body" v-for="(item,key) in paybacks.data" :key="key+item.id">
                      <td>{{item.id}}</td>
                      <td>{{item.userid}}</td>
                      <td>￥{{item.ref_get}}</td>
                    </tr>
                  </template>
                </uim-table>
              </div>
              <div class="uim-pagenation-container">
                <uim-pagenation @turnPage="turnInviteLogPage" :pageinfo="pagenation"></uim-pagenation>
              </div>
            </div>
          </transition>
        </div>
        <div v-else>
          <h3>
            {{userCon.user_name}}，您不是VIP暂时无法使用邀请链接，
            <slot name="inviteToShop"></slot>
          </h3>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";

import Tooltip from "@/components/tooltip.vue";
import Table from "@/components/table.vue";
import Pagenation from "@/components/pagenation.vue";

import { _get, _post } from "../../js/fetch";

export default {
  mixins: [userMixin, storeMap],
  components: {
    "uim-tooltip": Tooltip,
    "uim-table": Table,
    "uim-pagenation": Pagenation
  },
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
      theUnWatch: "",
      showInviteLog: false,
      paybacks: "",
      paybacks_sum: "",
      pagenation: {
        lastPage: 1,
        currentPage: 1
      }
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
        if (r.ret === 1) {
          this.code = r.arr.code.code;
          this.hideInviteReset();
          this.showLinkTrans();
          let callConfig = {
            msg: "已重置您的邀请链接，复制您的邀请链接发送给其他人！",
            icon: "bell",
            time: 1500
          };
          this.callMsgr(callConfig);
        } else if (r.ret === -1) {
          this.ajaxNotLogin();
        }
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
          icon: "times-circle",
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
          icon: "times-circle",
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
        if (r.ret === 1) {
          this.reConfigResourse();
          this.showInviteTimeTrans();
          this.setInviteNum(r.invite_num);
          let callConfig = {
            msg: r.msg,
            icon: "check-circle",
            time: 1000
          };
          this.callMsgr(callConfig);
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
      });
    },
    customInvite() {
      this.hideToolInput(1);
      let ajaxBody = {
        customcode: this.toolInputContent
      };
      _post("/user/custom_invite", JSON.stringify(ajaxBody), "include").then(
        r => {
          if (r.ret === 1) {
            window.console.log(r);
            this.reConfigResourse();
            this.showLinkTrans();
            this.code = this.oldCode = this.toolInputContent;
            let callConfig = {
              msg: r.msg,
              icon: "check-circle",
              time: 1000
            };
            this.callMsgr(callConfig);
          } else if (r.ret === 0) {
            this.showLinkTrans();
            this.code = this.oldCode;
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
    checkInviteLog() {
      this.showInviteLog = true;
      this.hideToolInput();
    },
    closeInviteLog() {
      this.showInviteLog = false;
    },
    turnInviteLogPage(current) {
      let body = { current: current };
      _post("/getuserinviteinfo", JSON.stringify(body), "include").then(r => {
        if (r.ret === 1) {
          this.paybacks = r.inviteInfo.paybacks;
          this.pagenation.currentPage = r.inviteInfo.paybacks.current_page;
        } else if (r.ret === -1) {
          this.ajaxNotLogin();
        }
      });
    }
  },
  mounted() {
    let body = { current: 1 };
    _post("/getuserinviteinfo", JSON.stringify(body), "include").then(r => {
      if (r.ret === 1) {
        this.code = this.oldCode = r.inviteInfo.code.code;
        this.invitePrice = r.inviteInfo.invitePrice;
        this.customPrice = r.inviteInfo.customPrice;
        this.paybacks = r.inviteInfo.paybacks;
        this.paybacks_sum = r.inviteInfo.paybacks_sum;
        this.invite_get_money = r.inviteInfo.invite_get_money;
        this.invite_gift = r.inviteInfo.invite_gift;
        this.code_payback = r.inviteInfo.code_payback;
        this.pagenation = {
          lastPage: r.inviteInfo.paybacks.last_page
        };
        this.setInviteNum(r.inviteInfo.invite_num);
      } else if (r.ret === -1) {
        this.ajaxNotLogin();
      }
    });
  },
  beforeDestroy() {
    this.hideToolInput();
  }
};
</script>

<style>
.invite-tools {
  position: relative;
  margin: 1rem 0.75rem 0 0;
}
.invite-number.tips {
  margin-right: 0.75rem;
  font-size: 14px;
}
.invite-number.tips:last-of-type {
  margin-right: 0;
}
.invite-tools .tips.tips-red {
  position: relative;
  top: 1px;
}
.invite-tools.tips-green {
  padding: 3.5px 0.7rem;
  bottom: 1px;
  margin-left: 0.55rem;
}
.user-invite-subtitle {
  padding-left: 1rem;
  margin-bottom: 1rem;
}
.toolinput-state .tips {
  margin-right: 0.75rem;
}
.user-invite-title .tips {
  position: relative;
}
@media screen and (min-width: 35.5em) {
  .invite-tools {
    margin: 0 0 0 0.75rem;
  }
  .user-invite-subtitle > div {
    display: inline-block;
  }
  .user-invite-subtitle {
    padding-left: 0;
    margin-bottom: 0;
  }
  .toolinput-state .tips {
    margin-right: 0;
    margin-left: 0.75rem;
  }
  .user-invite-title .btn-forinput {
    top: 0.05rem;
  }
}
</style>
