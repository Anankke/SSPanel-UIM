<template>
  <div class="page-user pure-u-1">
    <div class="title-back flex align-center">USERCENTER</div>
    <transition name="loading-fadex" mode="out-in">
      <div class="loading flex align-center" v-if="userLoadState === 'beforeload'">USERCENTER</div>

      <div class="loading flex align-center" v-else-if="userLoadState === 'loading'" key="loading">
        <div class="spinnercube">
          <div class="cube1"></div>
          <div class="cube2"></div>
        </div>
      </div>

      <div class="usrcenter text-left pure-g space-around" v-else-if="userLoadState === 'loaded'">
        <div class="pure-u-1 pure-u-xl-6-24 pure-g usrcenter-left">
          <div class="pure-u-1 pure-u-sm-8-24 pure-u-xl-1 card account-base">
            <div class="flex space-between">
              <div class="card-title">账号明细</div>
            </div>
            <div class="card-body">
              <div class="pure-g">
                <div class="pure-u-1-2 pure-u-sm-1 pure-u-xl-1-2">
                  <p class="tips tips-blue">用户名</p>
                  <p class="font-light">{{userCon.user_name}}</p>
                  <p class="tips tips-blue">邮箱</p>
                  <p class="font-light">{{userCon.email}}</p>
                </div>
                <div class="pure-u-1-2 pure-u-sm-1 pure-u-xl-1-2">
                  <p class="tips tips-blue">VIP等级</p>
                  <p class="font-light">
                    <span
                      class="user-config"
                      :class="{ 'font-gold-trans':userResourseTrans }"
                    >Lv. {{userCon.class}}</span>
                  </p>
                  <p class="tips tips-blue">余额</p>
                  <p class="font-light">
                    <span
                      class="user-config"
                      :class="{ 'font-red-trans':userCreditTrans }"
                    >{{userCon.money}}</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="pure-u-1 pure-u-sm-15-24 pure-u-xl-1 card quickset margin-nobottom-xl">
            <div class="card-title">快速配置</div>
            <div class="card-body">
              <div class="pure-g">
                <button
                  @click="changeAgentType"
                  v-for="dl in downloads"
                  :data-type="dl.type"
                  :class="{ 'index-btn-active':currentDlType === dl.type }"
                  class="pure-u-1-3 btn-user dl-type"
                  :key="dl.type"
                >{{dl.type}}</button>
                <h5 class="pure-u-1">平台选择/客户端下载</h5>
                <transition name="rotate-fade" mode="out-in">
                  <div class="pure-g dl-link" :key="typeToken.tagkey">
                    <uim-dropdown
                      v-for="(value,key) in downloads[typeToken.arrIndex].agent"
                      class="pure-u-1-3 btn-user"
                      :key="key"
                    >
                      <template #dpbtn-content>{{key}}</template>
                      <template #dp-menu>
                        <li v-for="agent in value" :key="agent.id">
                          <a :href="agent.href">{{agent.agentName}}</a>
                        </li>
                      </template>
                    </uim-dropdown>
                  </div>
                </transition>
                <h5 class="pure-u-1 flex align-center space-between">
                  <span>订阅链接</span>
                  <span class="link-reset relative flex justify-center text-center">
                    <button @click="showToolTip('resetConfirm')" class="tips tips-red">
                      <font-awesome-icon icon="sync-alt"/>&nbsp;重置链接
                    </button>
                    <uim-tooltip
                      v-show="toolTips.resetConfirm"
                      class="uim-tooltip-top flex justify-center"
                    >
                      <template #tooltip-inner>
                        <span>确定要重置订阅链接？</span>
                        <div>
                          <button @click="resetSubscribLink" class="tips tips-green">
                            <font-awesome-icon icon="check" fixed-width/>
                          </button>
                          <button @click="hideToolTip('resetConfirm')" class="tips tips-red">
                            <font-awesome-icon icon="times" fixed-width/>
                          </button>
                        </div>
                      </template>
                    </uim-tooltip>
                  </span>
                </h5>
                <transition name="rotate-fade" mode="out-in">
                  <div class="input-copy" :key="typeToken.subKey">
                    <div class="pure-g align-center relative">
                      <span class="pure-u-6-24">{{currentDlType === 'SSR' ? '普通端口:' : '订阅链接:'}}</span>
                      <span class="pure-u-18-24 pure-g relative flex justify-center text-center">
                        <input
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="typeToken.subUrl"
                          @mouseenter="showToolTip(typeToken.muType)"
                          @mouseleave="hideToolTip(typeToken.muType)"
                          :class="{ 'sublink-reset':subLinkTrans }"
                          class="tips tips-blue pure-u-1"
                          type="text"
                          name
                          id
                          :value="typeToken.subUrl"
                          readonly
                        >
                        <uim-tooltip
                          v-show="toolTips[typeToken.muType]"
                          class="uim-tooltip-top flex justify-center"
                        >
                          <template #tooltip-inner>
                            <span>{{typeToken.subUrl}}</span>
                          </template>
                        </uim-tooltip>
                      </span>
                    </div>
                    <div
                      v-if="currentDlType === 'SSR' && mergeSub !== 'true'"
                      class="pure-g align-center relative"
                    >
                      <span class="pure-u-6-24">单端口:</span>
                      <span class="pure-u-18-24 pure-g relative flex justify-center text-center">
                        <input
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="suburlMu1"
                          @mouseenter="showToolTip('mu1')"
                          @mouseleave="hideToolTip('mu1')"
                          :class="{ 'sublink-reset':subLinkTrans }"
                          class="tips tips-blue pure-u-1"
                          type="text"
                          name
                          id
                          :value="suburlMu1"
                          readonly
                        >
                        <uim-tooltip
                          v-show="toolTips.mu1"
                          class="uim-tooltip-top flex justify-center"
                        >
                          <template #tooltip-inner>
                            <span>{{suburlMu1}}</span>
                          </template>
                        </uim-tooltip>
                      </span>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </div>
        <div class="pure-u-1 pure-u-xl-17-24">
          <div class="card relative">
            <uim-anchor>
              <template #uim-anchor-inner>
                <li
                  v-for="(page,index) in userSettings.pages"
                  @click="changeUserSetPage(index)"
                  :class="{ 'uim-anchor-active':userSettings.currentPage === page.id }"
                  :data-page="page.id"
                  :key="page.id"
                ></li>
              </template>
            </uim-anchor>
            <transition name="fade" mode="out-in">
              <keep-alive>
                <component
                  v-on:turnPageByWheel="scrollPage"
                  :resourseTrans="userResourseTrans"
                  :is="userSettings.currentPage"
                  :initialSet="userSettings"
                  class="settiings-toolbar card margin-nobottom"
                ></component>
              </keep-alive>
            </transition>
          </div>
          <div class="user-btngroup pure-g">
            <div class="pure-u-1-2 btngroup-left">
              <uim-dropdown>
                <template #dpbtn-content>
                  <transition name="fade" mode="out-in">
                    <div :key="currentCardComponent">{{menuOptions[currentCardComponentIndex].name}}</div>
                  </transition>
                </template>
                <template #dp-menu>
                  <li
                    @click="componentChange"
                    v-for="menu in menuOptions"
                    :data-component="menu.id"
                    :key="menu.id"
                  >{{menu.name}}</li>
                </template>
              </uim-dropdown>
              <a v-if="userCon.is_admin === true" class="btn-user" href="/admin">运营中心</a>
            </div>
            <div class="pure-u-1-2 text-right btngroup-right">
              <a href="/user" class="btn-user">管理面板</a>
              <button @click="logout" class="btn-user">账号登出</button>
            </div>
          </div>
          <transition name="fade" mode="out-in">
            <component
              @guideToShop="componentChange"
              :is="currentCardComponent"
              v-on:resourseTransTrigger="showTransition('userResourseTrans')"
              :baseURL="baseUrl"
              :annC="ann"
              class="card margin-nobottom"
            >
              <button
                @click="componentChange"
                class="btn-inline text-red"
                :data-component="menuOptions[3].id"
                slot="inviteToShop"
              >成为VIP请点击这里</button>
            </component>
          </transition>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import agentMixin from "@/mixins/agentMixin";
import UserAnnouncement from "@/components/panel/UserAnnouncement.vue";
import UserInvite from "@/components/panel/UserInvite.vue";
import UserShop from "@/components/panel/UserShop.vue";
import UserGuide from "@/components/panel/UserGuide.vue";
import UserResourse from "@/components/panel/UserResourse.vue";
import UserSettings from "@/components/panel/UserSettings.vue";
import UserCharge from "@/components/panel/UserCharge.vue";

import Dropdown from "@/components/dropdown.vue";
import Tooltip from "@/components/tooltip.vue";
import Anchor from "@/components/anchor.vue";

import { _get } from "../js/fetch";

export default {
  mixins: [storeMap, agentMixin],
  components: {
    "user-announcement": UserAnnouncement,
    "user-invite": UserInvite,
    "user-shop": UserShop,
    "user-guide": UserGuide,
    "user-resourse": UserResourse,
    "user-settings": UserSettings,
    "user-charge": UserCharge,
    "uim-dropdown": Dropdown,
    "uim-tooltip": Tooltip,
    "uim-anchor": Anchor
  },
  props: ["routermsg"],
  computed: {
    typeToken: function() {
      switch (this.currentDlType) {
        case "SSR":
          return {
            tagkey: "dl-ssr",
            subKey: "sub-ssr",
            arrIndex: 0,
            muType: "mu0",
            subUrl: this.suburlMu0
          };
        case "SS/SSD":
          return {
            tagkey: "dl-ss",
            subKey: "sub-ss",
            arrIndex: 1,
            muType: "mu3",
            subUrl: this.suburlMu3
          };
        case "V2RAY":
          return {
            tagkey: "dl-v2",
            subKey: "sub-v2",
            arrIndex: 2,
            muType: "mu2",
            subUrl: this.suburlMu2
          };
      }
    },
    currentCardComponentIndex: function() {
      switch (this.currentCardComponent) {
        case "user-announcement":
          return 0;
        case "user-guide":
          return 1;
        case "user-invite":
          return 2;
        case "user-charge":
          return 3;
        case "user-shop":
          return 4;
      }
    }
  },
  data: function() {
    return {
      userLoadState: "beforeload",
      ann: {
        content: "",
        date: "",
        id: "",
        markdown: ""
      },
      baseUrl: "",
      mergeSub: "false",
      toolTips: {
        mu0: false,
        mu1: false,
        mu2: false,
        mu3: false,
        resetConfirm: false
      },
      subLinkTrans: false,
      userCreditTrans: false,
      userResourseTrans: false,
      menuOptions: [
        {
          name: "公告栏",
          id: "user-announcement"
        },
        {
          name: "配置指南",
          id: "user-guide"
        },
        {
          name: "邀请链接",
          id: "user-invite"
        },
        {
          name: "充值中心",
          id: "user-charge"
        },
        {
          name: "套餐购买",
          id: "user-shop"
        }
      ],
      currentCardComponent: "user-announcement"
    };
  },
  watch: {
    "userCon.money"(to, from) {
      this.showTransition("userCreditTrans");
    }
  },
  methods: {
    logout() {
      let callConfig = {
        msg: "",
        icon: "",
        time: 1000
      };
      _get("/logout", "include").then(r => {
        if (r.ret === 1) {
          callConfig.msg += "账户成功登出Kira~";
          callConfig.icon += "check-circle";
          this.callMsgr(callConfig);
          window.setTimeout(() => {
            this.setLoginToken(false);
            this.$router.replace("/");
          }, this.globalConfig.jumpDelay);
        }
      });
    },
    indexPlus(index, arrlength) {
      if (index === arrlength - 1) {
        this.userSettings.currentPageIndex = index;
      } else {
        this.userSettings.currentPageIndex += 1;
      }
      return this.userSettings.currentPageIndex;
    },
    indexMinus(index) {
      if (index === 0) {
        this.userSettings.currentPageIndex = index;
      } else {
        this.userSettings.currentPageIndex -= 1;
      }
      return this.userSettings.currentPageIndex;
    },
    showTransition(key) {
      this[key] = true;
      setTimeout(() => {
        this[key] = false;
      }, 500);
    },
    componentChange(e) {
      this.currentCardComponent = e.target.dataset.component;
    },
    changeUserSetPage(index) {
      this.userSettings.currentPage = this.userSettings.pages[index].id;
      this.userSettings.currentPageIndex = index;
    },
    showToolTip(id) {
      this.toolTips[id] = true;
    },
    hideToolTip(id) {
      this.toolTips[id] = false;
    },
    resetSubscribLink() {
      _get("/getnewsubtoken", "include").then(r => {
        if (r.ret === 1) {
          this.ssrSubToken = r.arr.ssr_sub_token;
          this.hideToolTip("resetConfirm");
          this.showTransition("subLinkTrans");
          let callConfig = {
            msg: "已重置您的订阅链接，请变更或添加您的订阅链接！",
            icon: "bell",
            time: 1500
          };
          this.callMsgr(callConfig);
        } else if (r.ret === -1) {
          this.ajaxNotLogin();
        }
      });
    },
    scrollPage(token) {
      if (token > 0) {
        let index = this.indexPlus(
          this.userSettings.currentPageIndex,
          this.userSettings.pages.length
        );
        this.changeUserSetPage(index);
      } else {
        let index = this.indexMinus(this.userSettings.currentPageIndex);
        this.changeUserSetPage(index);
      }
    },
    showSigner() {
      let promise = new Promise((resolve, reject) => {
        this.setSignSet({ transition: true });
        resolve();
      });
      promise.then(r => {
        window.console.log(r);
        setTimeout(() => {
          this.setSignSet({ isSignShow: true });
        }, 500);
      });
    }
  },
  mounted() {
    let self = this;
    this.userLoadState = "loading";

    _get("/getuserinfo", "include")
      .then(r => {
        if (r.ret === 1) {
          window.console.log(r.info);
          this.setUserCon(r.info.user);
          this.setUserSettings(this.userCon);
          window.console.log(this.userCon);
          if (r.info.ann) {
            this.ann = r.info.ann;
          }
          this.setAllBaseCon({
            subUrl: r.info.subUrl,
            ssrSubToken: r.info.ssrSubToken,
            iosAccount: r.info.iosAccount,
            iosPassword: r.info.iosPassword,
            displayIosClass: r.info.displayIosClass
          });
          this.baseUrl = r.info.baseUrl;
          this.mergeSub = r.info.mergeSub;
        } else if (r.ret === -1) {
          this.ajaxNotLogin();
        }
      })
      .then(r => {
        setTimeout(() => {
          self.userLoadState = "loaded";
          this.showSigner();
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

<style>
.pure-g.usrcenter-left {
  justify-content: space-around;
  display: flex;
}
@media screen and (min-width: 80em) {
  .pure-g.usrcenter-left {
    display: inline-block;
  }
}
</style>
