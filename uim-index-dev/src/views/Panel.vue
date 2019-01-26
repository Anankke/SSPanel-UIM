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

      <div class="usrcenter text-left pure-g space-between" v-else-if="userLoadState === 'loaded'">
        <div class="pure-u-1 pure-u-sm-6-24">
          <div class="card account-base">
            <div class="flex space-between">
              <div class="card-title">账号明细</div>
            </div>
            <div class="card-body">
              <div class="pure-g">
                <div class="pure-u-1-2">
                  <p class="tips tips-blue">用户名</p>
                  <p class="font-light">$[userCon.user_name]$</p>
                  <p class="tips tips-blue">邮箱</p>
                  <p class="font-light">$[userCon.email]$</p>
                </div>
                <div class="pure-u-1-2">
                  <p class="tips tips-blue">VIP等级</p>
                  <p class="font-light">
                    <span
                      class="user-config"
                      :class="{ 'font-gold-trans':userResourseTrans }"
                    >Lv. $[userCon.class]$</span>
                  </p>
                  <p class="tips tips-blue">余额</p>
                  <p class="font-light">
                    <span
                      class="user-config"
                      :class="{ 'font-red-trans':userCreditTrans }"
                    >$[userCon.money]$</span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="card quickset margin-nobottom-sm">
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
                >$[dl.type]$</button>
                <h5 class="pure-u-1">平台选择/客户端下载</h5>
                <transition name="rotate-fade" mode="out-in">
                  <div v-if="currentDlType === 'SSR'" class="dl-link" key="ssr">
                    <uim-dropdown
                      v-for="(value,key) in downloads[0].agent"
                      class="pure-u-1-3 btn-user"
                      :key="key"
                    >
                      <span slot="dpbtn-content">$[key]$</span>
                      <ul slot="dp-menu">
                        <li v-for="agent in value" :key="agent.id">
                          <a :href="agent.href">$[agent.agentName]$</a>
                        </li>
                      </ul>
                    </uim-dropdown>
                  </div>
                  <div v-else-if="currentDlType === 'SS/SSD'" class="dl-link" key="ss">
                    <uim-dropdown
                      v-for="(value,key) in downloads[1].agent"
                      class="pure-u-1-3 btn-user"
                      :key="key"
                    >
                      <span slot="dpbtn-content">$[key]$</span>
                      <ul slot="dp-menu">
                        <li v-for="agent in value" :key="agent.id">
                          <a :href="agent.href">$[agent.agentName]$</a>
                        </li>
                      </ul>
                    </uim-dropdown>
                  </div>
                  <div v-else-if="currentDlType === 'V2RAY'" class="dl-link" key="v2ray">
                    <uim-dropdown
                      v-for="(value,key) in downloads[2].agent"
                      class="pure-u-1-3 btn-user"
                      :key="key"
                    >
                      <span slot="dpbtn-content">$[key]$</span>
                      <ul slot="dp-menu">
                        <li v-for="agent in value" :key="agent.id">
                          <a :href="agent.href">$[agent.agentName]$</a>
                        </li>
                      </ul>
                    </uim-dropdown>
                  </div>
                </transition>
                <h5 class="pure-u-1 flex align-center space-between">
                  <span>订阅链接</span>
                  <span class="link-reset relative flex justify-center text-center">
                    <uim-tooltip
                      v-show="toolTips.resetConfirm"
                      class="uim-tooltip-top flex justify-center"
                    >
                      <button @click="showToolTip('resetConfirm')" class="tips tips-red">
                        <span class="fa fa-refresh"></span> 重置链接
                      </button>
                      <div slot="tooltip-inner">
                        <span>确定要重置订阅链接？</span>
                        <div>
                          <button @click="resetSubscribLink" class="tips tips-green">
                            <span class="fa fa-fw fa-check"></span>
                          </button>
                          <button @click="hideToolTip('resetConfirm')" class="tips tips-red">
                            <span class="fa fa-fw fa-remove"></span>
                          </button>
                        </div>
                      </div>
                    </uim-tooltip>
                  </span>
                </h5>
                <transition name="rotate-fade" mode="out-in">
                  <div class="input-copy" v-if="currentDlType === 'SSR'" key="ssrsub">
                    <div class="pure-g align-center relative">
                      <span class="pure-u-6-24">普通端口:</span>
                      <span class="pure-u-18-24 pure-g relative flex justify-center text-center">
                        <input
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="suburlMu0"
                          @mouseenter="showToolTip('mu0')"
                          @mouseleave="hideToolTip('mu0')"
                          :class="{ 'sublink-reset':subLinkTrans }"
                          class="tips tips-blue pure-u-1"
                          type="text"
                          name
                          id
                          :value="suburlMu0"
                          readonly
                        >
                        <uim-tooltip
                          v-show="toolTips.mu0"
                          class="uim-tooltip-top flex justify-center"
                        >
                          <div class="sublink" slot="tooltip-inner">
                            <span>$[suburlMu0]$</span>
                          </div>
                        </uim-tooltip>
                      </span>
                    </div>
                    <div v-if="mergeSub !== 'true'" class="pure-g align-center relative">
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
                          <div class="sublink" slot="tooltip-inner">
                            <span>$[suburlMu1]$</span>
                          </div>
                        </uim-tooltip>
                      </span>
                    </div>
                  </div>
                  <div
                    class="pure-g input-copy relative flex justify-center text-center"
                    v-else-if="currentDlType === 'V2RAY'"
                    key="sssub"
                  >
                    <input
                      v-uimclip="{ onSuccess:successCopied }"
                      :data-uimclip="suburlMu2"
                      @mouseenter="showToolTip('mu2')"
                      @mouseleave="hideToolTip('mu2')"
                      :class="{ 'sublink-reset':subLinkTrans }"
                      class="tips tips-blue"
                      type="text"
                      name
                      id
                      :value="suburlMu2"
                      readonly
                    >
                    <uim-tooltip
                      v-show="toolTips.mu2"
                      class="pure-u-1 uim-tooltip-top flex justify-center"
                    >
                      <div class="sublink" slot="tooltip-inner">
                        <span>$[suburlMu2]$</span>
                      </div>
                    </uim-tooltip>
                  </div>
                  <div
                    class="pure-g input-copy relative flex justify-center text-center"
                    v-else-if="currentDlType === 'SS/SSD'"
                    key="v2sub"
                  >
                    <input
                      v-uimclip="{ onSuccess:successCopied }"
                      :data-uimclip="suburlMu3"
                      @mouseenter="showToolTip('mu3')"
                      @mouseleave="hideToolTip('mu3')"
                      :class="{ 'sublink-reset':subLinkTrans }"
                      class="tips tips-blue"
                      type="text"
                      name
                      id
                      :value="suburlMu3"
                      readonly
                    >
                    <uim-tooltip
                      v-show="toolTips.mu3"
                      class="pure-u-1 uim-tooltip-top flex justify-center"
                    >
                      <div class="sublink" slot="tooltip-inner">
                        <span>$[suburlMu3]$</span>
                      </div>
                    </uim-tooltip>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </div>
        <div class="pure-u-1 pure-u-sm-17-24">
          <div class="card relative">
            <uim-anchor>
              <ul slot="uim-anchor-inner">
                <li
                  v-for="(page,index) in userSettings.pages"
                  @click="changeUserSetPage(index)"
                  :class="{ 'uim-anchor-active':userSettings.currentPage === page.id }"
                  :data-page="page.id"
                  :key="page.id"
                ></li>
              </ul>
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
            <div class="pure-u-1-2 pure-u-sm-16-24">
              <uim-dropdown>
                <span slot="dpbtn-content">栏目导航</span>
                <ul slot="dp-menu">
                  <li
                    @click="componentChange"
                    v-for="menu in menuOptions"
                    :data-component="menu.id"
                    :key="menu.id"
                  >$[menu.name]$</li>
                </ul>
              </uim-dropdown>
              <a v-if="userCon.is_admin === true" class="btn-user" href="/admin">运营中心</a>
            </div>
            <div class="pure-u-1-2 pure-u-sm-8-24 text-right">
              <a href="/user" class="btn-user">管理面板</a>
              <button @click="logout" class="btn-user">账号登出</button>
            </div>
          </div>
          <transition name="fade" mode="out-in">
            <component
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
export default {
  delimiters: ["$[", "]$"],
  mixins: [storeMap],
  components: {
    "user-announcement": UserAnnouncement,
    "user-invite": UserInvite,
    "user-shop": UserShop,
    "user-guide": UserGuide,
    "user-resourse": UserResourse,
    "user-settings": UserSettings
  },
  props: ["routermsg"],
  computed: {
    suburlBase: function() {
      return this.subUrl + this.ssrSubToken;
    },
    suburlMu0: function() {
      return this.suburlBase + "?mu=0";
    },
    suburlMu1: function() {
      return this.suburlBase + "?mu=1";
    },
    suburlMu3: function() {
      return this.suburlBase + "?mu=3";
    },
    suburlMu2: function() {
      return this.suburlBase + "?mu=2";
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
      subUrl: "",
      ssrSubToken: "",
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
          name: "套餐购买",
          id: "user-shop"
        }
      ],
      currentCardComponent: "user-announcement",
      downloads: [
        {
          type: "SSR",
          agent: {
            Windows: [
              {
                agentName: "SSR",
                href: "/ssr-download/ssr-win.7z",
                id: "AGENT_1_1_1"
              },
              {
                agentName: "SSTAP",
                href: "/ssr-download/SSTap.7z",
                id: "AGENT_1_1_2"
              }
            ],
            Macos: [
              {
                agentName: "SSX",
                href: "/ssr-download/ssr-mac.dmg",
                id: "AGENT_1_2_1"
              }
            ],
            Linux: [
              {
                agentName: "SS-qt5",
                href: "#",
                id: "AGENT_1_3_1"
              }
            ],
            Ios: [
              {
                agentName: "Potatso Lite",
                href: "#",
                id: "AGENT_1_4_1"
              },
              {
                agentName: "Shadowrocket",
                href: "#",
                id: "AGENT_1_4_2"
              }
            ],
            Android: [
              {
                agentName: "SSR",
                href: "/ssr-download/ssr-android.apk",
                id: "AGENT_1_5_1"
              },
              {
                agentName: "SSRR",
                href: "/ssr-download/ssrr-android.apk",
                id: "AGENT_1_5_2"
              }
            ],
            Router: [
              {
                agentName: "FancySS",
                href: "https://github.com/hq450/fancyss_history_package",
                id: "AGENT_1_6_1"
              }
            ]
          }
        },
        {
          type: "SS/SSD",
          agent: {
            Windows: [
              {
                agentName: "SSD",
                href: "/ssr-download/ssd-win.7z",
                id: "AGENT_2_1_1"
              }
            ],
            Macos: [
              {
                agentName: "SSXG",
                href: "/ssr-download/ss-mac.zip",
                id: "AGENT_2_2_1"
              }
            ],
            Linux: [
              {
                agentName: "/",
                href: "#",
                id: "AGENT_2_3_1"
              }
            ],
            Ios: [
              {
                agentName: "Potatso Lite",
                href: "#",
                id: "AGENT_2_4_1"
              },
              {
                agentName: "Shadowrocket",
                href: "#",
                id: "AGENT_2_4_2"
              }
            ],
            Android: [
              {
                agentName: "SSD",
                href: "/ssr-download/ssd-android.apk",
                id: "AGENT_2_5_1"
              },
              {
                agentName: "混淆插件",
                href: "/ssr-download/ss-android-obfs.apk",
                id: "AGENT_2_5_2"
              }
            ],
            Router: [
              {
                agentName: "FancySS",
                href: "https://github.com/hq450/fancyss_history_package",
                id: "AGENT_2_6_1"
              }
            ]
          }
        },
        {
          type: "V2RAY",
          agent: {
            Windows: [
              {
                agentName: "V2RayN",
                href: "/ssr-download/v2rayn.zip",
                id: "AGENT_3_1_1"
              }
            ],
            Macos: [
              {
                agentName: "/",
                href: "#",
                id: "AGENT_3_2_1"
              }
            ],
            Linux: [
              {
                agentName: "/",
                href: "#",
                id: "AGENT_3_3_1"
              }
            ],
            Ios: [
              {
                agentName: "Shadowrocket",
                href: "#",
                id: "AGENT_3_4_1"
              }
            ],
            Android: [
              {
                agentName: "V2RayN",
                href: "/ssr-download/v2rayng.apk",
                id: "AGENT_3_5_1"
              }
            ],
            Router: [
              {
                agentName: "FancySS",
                href: "https://github.com/hq450/fancyss_history_package",
                id: "AGENT_3_6_1"
              }
            ]
          }
        }
      ],
      currentDlType: "SSR"
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
          callConfig.icon += "fa-check-square-o";
          this.callMsgr(callConfig);
          window.setTimeout(() => {
            this.setLoginToken(0);
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
    changeAgentType(e) {
      this.currentDlType = e.target.dataset.type;
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
        this.ssrSubToken = r.arr.ssr_sub_token;
        this.hideToolTip("resetConfirm");
        this.showTransition("subLinkTrans");
        let callConfig = {
          msg: "已重置您的订阅链接，请变更或添加您的订阅链接！",
          icon: "fa-bell",
          time: 1500
        };
        this.callMsgr(callConfig);
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
    }
  },
  mounted() {
    let self = this;
    this.userLoadState = "loading";

    _get("/getuserinfo", "include")
      .then(r => {
        if (r.ret === 1) {
          console.log(r.info);
          this.setUserCon(r.info.user);
          this.setUserSettings(this.userCon);
          console.log(this.userCon);
          if (r.info.ann) {
            this.ann = r.info.ann;
          }
          this.baseUrl = r.info.baseUrl;
          this.subUrl = r.info.subUrl;
          this.ssrSubToken = r.info.ssrSubToken;
          this.mergeSub = r.info.mergeSub;
        }
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
      next();
    }
  }
};
</script>
