<template>
  <div class="relative" style="overflow-x:hidden">
    <div
      @click.stop
      class="userguide-bookmark-container pure-u-1-2 pure-u-sm-4-24 flex align-center absolute"
      :class="{ 'userguide-bookmark-drawer-active':isBookmarkShow }"
    >
      <button @click="bookmarkTrigger" class="userguide-bookmark-drawer absolute">
        <div>
          <font-awesome-icon
            icon="chevron-left"
            :style="{ transition: 'all .3s' }"
            :class="{ 'bookmark-arrow-rotate':isBookmarkShow }"
          />
        </div>
        <div>平</div>
        <div>台</div>
        <div>选</div>
        <div>择</div>
      </button>
      <div class="userguide-bookmark flex align-center wrap" :key="agentToken.markKey">
        <button
          v-for="mark in agentToken.tips"
          @click="setCurrentPlantformType(mark.type),setBookmarkState(mark.type);"
          :key="mark.id"
          :class="{ 'bookmark-active': mark.isActive }"
        >
          <span class="btn-anchor"></span>
          {{mark.type}}
        </button>
      </div>
    </div>
    <div class="flex space-between align-center">
      <div class="card-title">配置指南</div>
      <div class="card-title-right">
        <uim-dropdown show-arrow>
          <template #dpbtn-content>
            <transition name="fade" mode="out-in">
              <div :key="agentToken.menuKey">{{currentDlType}}</div>
            </transition>
          </template>
          <template #dp-menu>
            <li
              @click="changeAgentType"
              v-for="dl in downloads"
              :data-type="dl.type"
              :key="dl.type"
            >{{dl.type}}</li>
          </template>
        </uim-dropdown>
      </div>
    </div>
    <div class="card-body">
      <div class="user-guide pure-g relative">
        <div class="pure-u-1 pure-u-sm-19-24 relative">
          <transition name="slide-fadex">
            <div class="absolute guide-area" :key="currentDlType">
              <transition-group name="list" class="relative guide-area">
                <p v-for="step in currentSteps" :key="step.id">
                  <span class="tips tips-blue">{{step.num}}</span>
                  {{step.content}}
                  <span v-if="step.extra">
                    <p v-if="currentDlType === 'SSR'">
                      <span v-if="currentPlantformType === ('WINDOWS' || 'ANDROID')">
                        <button
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="userCon.ssr_url_all"
                          class="tips tips-cyan"
                        >
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;普通端口链接
                        </button>
                        <button
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="userCon.ssr_url_all_mu"
                          class="tips tips-cyan"
                          v-if="mergeSub !== 'true'"
                        >
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;单端口多用户链接
                        </button>
                      </span>
                      <span v-if="currentPlantformType === 'IOS'">
                        <button class="tips tips-cyan" @click="oneKeySub(suburlMu0)">
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;小火箭一键普通端口订阅
                        </button>
                        <button class="tips tips-cyan" @click="oneKeySub(suburlMu1)">
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;小火箭一键单端口订阅
                        </button>
                      </span>
                    </p>
                    <p v-if="currentDlType === 'SS/SSD'">
                      <span v-if="currentPlantformType === 'WINDOWS'">
                        <button
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="userCon.ssd_url_all"
                          class="tips tips-cyan"
                        >
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;复制节点链接
                        </button>
                      </span>
                      <span v-if="currentPlantformType === 'MACOS'">
                        <button
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="userCon.ss_url_all"
                          class="tips tips-cyan"
                        >
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;复制节点链接
                        </button>
                      </span>
                    </p>
                    <p v-if="currentDlType === 'V2RAY'">
                      <span v-if="currentPlantformType === 'IOS'">
                        <button @click="oneKeySub(suburlMu2)" class="tips tips-cyan">
                          <font-awesome-icon :icon="['far','copy']"/>&nbsp;小火箭一键订阅
                        </button>
                      </span>
                    </p>
                    <p v-if="currentPlantformType === 'IOS' && parseInt(displayIosClass) >= 0">
                      <span v-if="userCon.class >= parseInt(displayIosClass)">
                        <label for="iosAccount">公共IOS账号</label>
                        <input
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="iosAccount"
                          type="text"
                          name="iosAccount"
                          readonly="readonly"
                          class="tips tips-blue"
                          :value="iosAccount"
                        >
                        <label for="iosPass">公共IOS密码</label>
                        <input
                          v-uimclip="{ onSuccess:successCopied }"
                          :data-uimclip="iosPassword"
                          type="text"
                          name="iosPass"
                          readonly="readonly"
                          class="tips tips-blue"
                          :value="iosPassword"
                        >
                      </span>
                      <span v-else>
                        IOS公共账号等级至少为{{displayIosClass}}可见，如需升级请
                        <button
                          @click="$emit('guideToShop',$event)"
                          data-component="user-shop"
                          class="tips tips-gold"
                        >点击这里</button>升级套餐。
                      </span>
                    </p>
                  </span>
                </p>
              </transition-group>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.user-guide .tips {
  margin-bottom: 0.4rem;
}
.userguide-bookmark-container {
  z-index: 1;
  top: 20%;
  padding: 0 0.5rem 1rem 0.5rem;
  right: -50%;
}
.userguide-bookmark-container,
.userguide-bookmark-container > button span {
  transition: all 0.4s;
}
.userguide-bookmark-drawer-active {
  right: 0;
  background: white;
  box-shadow: 0 0 5px 0 #b4b4b4;
}
.userguide-bookmark-drawer-active > button {
  background: white;
  color: black;
  border-color: white;
}
.userguide-bookmark-drawer-active > button span {
  transform: rotateZ(180deg);
}
.userguide-bookmark-drawer-active .userguide-bookmark button {
  border: 1px solid;
  background: #4a4a4a;
}
.userguide-bookmark {
  justify-content: flex-end;
}
.guide-area p {
  margin-top: 0;
}
.guide-area > p:last-of-type {
  margin-bottom: 1rem;
}
.guide-area {
  padding-right: 1rem;
  width: 100%;
}
.userguide-bookmark button {
  background-color: #c8c8c81f;
  border: none;
  padding: 0.6rem 0;
  outline: none;
  display: block;
  margin-top: 1rem;
  font-size: 12px;
  text-align: left;
  padding-left: 1rem;
  width: 100%;
  border-radius: 20px;
}
.userguide-bookmark button,
.userguide-bookmark button:hover span:first-of-type {
  transition: all 0.3s;
}
.userguide-bookmark > div {
  width: 100%;
}
.userguide-bookmark-drawer,
.userguide-bookmark-container {
  border: 1px solid;
  border-right: 0;
  border-radius: 5px 0 0 5px;
}
.userguide-bookmark-drawer {
  right: 100%;
  width: 30px;
  text-align: center;
  padding: 0.3rem;
  font-size: 13px;
  background: transparent;
  outline: none;
}
.bookmark-arrow-rotate {
  transform: rotateZ(180deg);
}
button.bookmark-active,
.userguide-bookmark button:hover,
.userguide-bookmark-drawer-active .userguide-bookmark button.bookmark-active {
  border: 1px solid #e1e1e1;
  background-color: #e1e1e1;
  color: black;
}
button.bookmark-active span:first-of-type,
.userguide-bookmark button:hover span:first-of-type {
  background-color: #868686;
}
@media screen and (min-width: 35.5em) {
  .user-guide .tips {
    margin-right: 0.5rem;
  }
  .userguide-bookmark-container {
    right: 5%;
    padding: 0;
    border: 0;
  }
  .userguide-bookmark {
    justify-content: unset;
  }
  .userguide-bookmark-drawer {
    display: none;
  }
  button.bookmark-active,
  .userguide-bookmark button:hover {
    border: 0;
    background-color: white;
    color: black;
  }
}
</style>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";
import agentMixin from "@/mixins/agentMixin";

import Dropdown from "@/components/dropdown.vue";

export default {
  mixins: [userMixin, storeMap, agentMixin],
  components: {
    "uim-dropdown": Dropdown
  },
  computed: {
    agentToken: function() {
      switch (this.currentDlType) {
        case "SSR":
          return {
            menuKey: "guide-nemu-ssr",
            contentKey: "guide-content-ssr",
            markKey: "guide-mark-ssr",
            tips: this.agentContent["SSR"]
          };
        case "SS/SSD":
          return {
            menuKey: "guide-nemu-ss",
            contentKey: "guide-content-ss",
            markKey: "guide-mark-ss",
            tips: this.agentContent["SS/SSD"]
          };
        case "V2RAY":
          return {
            menuKey: "guide-nemu-v2",
            contentKey: "guide-content-v2",
            markKey: "guide-mark-v2",
            tips: this.agentContent["V2RAY"]
          };
      }
    },
    currentSteps: function() {
      let arr = this.agentContent[this.currentDlType];
      switch (this.currentPlantformType) {
        case "WINDOWS":
          return arr[0].steps;
        case "MACOS":
          return arr[1].steps;
        case "LINUX":
          return arr[2].steps;
        case "IOS":
          return arr[3].steps;
        case "ANDROID":
          return arr[4].steps;
        case "ROUTER":
          return arr[5].steps;
      }
    }
  },
  methods: {
    oneKeySub(url) {
      let urlStr = window.btoa(url);
      urlStr = urlStr.substring(0, urlStr.length);
      let newUrl = "sub://" + urlStr + "#";
      window.location.href = newUrl;
    },
    bookmarkTrigger() {
      if (this.isBookmarkShow === false) {
        this.isBookmarkShow = true;
      } else {
        this.isBookmarkShow = false;
      }
    },
    hideBookmark() {
      this.isBookmarkShow = false;
    },
    setBookmarkState(type) {
      let tips = this.agentToken.tips;
      for (let i = 0; i < tips.length; i++) {
        if (tips[i].type === type) {
          tips[i].isActive = true;
        } else {
          tips[i].isActive = false;
        }
      }
    }
  },
  mounted() {
    let app = document.getElementById("app");
    app.addEventListener("click", this.hideBookmark, false);
    this.setBookmarkState(this.currentPlantformType);
  },
  beforeDestroy() {
    let app = document.getElementById("app");
    app.removeEventListener("click", this.hideBookmark, false);
  },
  data: function() {
    return {
      isBookmarkShow: false,
      agentContent: {
        SSR: [
          {
            id: "GT_W_0",
            type: "WINDOWS",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载客户端解压至任意磁盘并运行",
                id: "GT_W_0_1"
              },
              {
                num: 2,
                content:
                  "任务栏右下角右键纸飞机图标->服务器订阅->SSR服务器订阅设置，将订阅链接设置为下面的地址，确定之后再更新SSR服务器订阅（绕过代理）",
                id: "GT_W_0_2"
              },
              {
                num: 3,
                content:
                  "选择一个合适的服务器，代理规则选“绕过局域网和大陆”，即可上网",
                id: "GT_W_0_3"
              },
              {
                num: "备用",
                content:
                  "点击复制普通端口链接或者单端口多用户链接，然后右键小飞机->从剪贴板复制地址",
                id: "GT_W_0_4",
                extra: true
              },
              {
                num: "SSTAP游戏端",
                content: "",
                id: "GT_W_0_5"
              },
              {
                num: 1,
                content:
                  "下载SSTap，并安装，期间会安装虚拟网卡，请点击允许或确认",
                id: "GT_W_0_6"
              },
              {
                num: 2,
                content: "打开桌面程序SSTAP",
                id: "GT_W_0_7"
              },
              {
                num: 3,
                content: "齿轮图标-SSR订阅-SSR订阅管理添加以下订阅链接即可",
                id: "GT_W_0_8"
              },
              {
                num: 4,
                content:
                  "更新后选择其中一个节点闪电图标测试节点-测试UDP转发...通过!（UDP通过即可连接并开始游戏）",
                id: "GT_W_0_9"
              }
            ]
          },
          {
            id: "GT_M_0",
            type: "MACOS",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载客户端，安装并启动",
                id: "GT_M_0_1"
              },
              {
                num: 2,
                content: "右击托盘纸飞机图标->服务器->服务器订阅，填入订阅地址",
                id: "GT_M_0_2"
              },
              {
                num: 3,
                content: "更新订阅成功后服务器列表即可出现节点，选择一个节点",
                id: "GT_M_0_3"
              },
              {
                num: 4,
                content:
                  "再次右击托盘纸飞机图标，如果shadowsocks还未打开，则需要点击打开",
                id: "GT_M_0_4"
              }
            ]
          },
          {
            id: "GT_L_0",
            type: "LINUX",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "安装shadowsocks-qt5",
                id: "GT_L_0_1"
              },
              {
                num: 2,
                content:
                  "按win键搜索找到软件，填写对应的服务器IP、端口、密码、加密方式，并配置系统代理模式",
                id: "GT_L_0_2"
              },
              {
                num: 3,
                content: "配置浏览器代理模式",
                id: "GT_L_0_3"
              },
              {
                num: 4,
                content: "点击connect连接",
                id: "GT_L_0_4"
              }
            ]
          },
          {
            id: "GT_I_0",
            type: "IOS",
            isActive: false,
            steps: [
              {
                num: 1,
                content:
                  "在非国区AppStore中搜索Shadowrocket或Potatso Lite下载安装",
                id: "GT_I_0_1"
              },
              {
                num: 2,
                content:
                  "打开 Potatso Lite，点击添加代理，点击右上角的 + 号，选择“订阅”，名字任意填写，开启自动更新，URL填写订阅地址并保存即可",
                id: "GT_I_0_2"
              },
              {
                num: 3,
                content:
                  "如果使用shadowrocket，打开 Shadowrocket，点击右上角的 + 号，类型选择“Subscribe”，URL填写订阅地址并点击右上角完成即可",
                id: "GT_I_0_3"
              },
              {
                num: "备用",
                content: "点击按钮，使用小火箭一键订阅",
                id: "GT_I_0_4",
                extra: true
              }
            ]
          },
          {
            id: "GT_A_0",
            type: "ANDROID",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载 SSR 或  SSRR 并安装",
                id: "GT_A_0_1"
              },
              {
                num: 2,
                content:
                  "打开App，左滑删除默认节点，点击右下角的add号图标，添加/升级 SSR订阅，左滑删除默认订阅，填入订阅地址，输入下方订阅地址，点击确定并升级",
                id: "GT_A_0_2"
              },
              {
                num: 3,
                content:
                  "点击选择任意节点， 路由选择：略过区域网路以及中国大陆",
                id: "GT_A_0_3"
              },
              {
                num: 4,
                content: "点击右上角的纸飞机图标即可连接",
                id: "GT_A_0_4"
              },
              {
                num: "备用",
                content:
                  "在手机上默认浏览器中点击普通端口链接或者单端口多用户链接，然后点击确定",
                id: "GT_A_0_5",
                extra: true
              }
            ]
          },
          {
            id: "GT_R_0",
            type: "ROUTER",
            isActive: false,
            steps: [
              {
                num: "梅林",
                content: "",
                id: "GT_R_0_0"
              },
              {
                num: 1,
                content: "打开下载页面下载“科学上网”插件",
                id: "GT_R_0_1"
              },
              {
                num: 2,
                content:
                  "进入路由器管理页面->系统管理->勾选“Format JFFS partition at next boot”和“Enable JFFS custom scripts and configs”->应用本页面设置，重启路由器",
                id: "GT_R_0_2"
              },
              {
                num: 3,
                content:
                  " 进入路由器管理页面->软件中心->离线安装，上传插件文件进行安装",
                id: "GT_R_0_3"
              },
              {
                num: 4,
                content:
                  "进入“科学上网”插件->更新管理，将下方的订阅地址复制粘贴进去，点击“保存并订阅”",
                id: "GT_R_0_4"
              },
              {
                num: 5,
                content:
                  "账号设置->节点选择，选择一个节点，打开“科学上网”开关->保存&应用",
                id: "GT_R_0_5"
              },
              {
                num: "padavan",
                content: "",
                id: "GT_R_0_6"
              },
              {
                num: 1,
                content: "进入路由器管理页面->扩展功能->Shadowsocks",
                id: "GT_R_0_7"
              },
              {
                num: 2,
                content: "将下方的订阅地址填入“ssr服务器订阅”，点击“更新”",
                id: "GT_R_0_8"
              },
              {
                num: 3,
                content: "选择需要的节点（右方勾选）->应用主SS->打开上方的开关",
                id: "GT_R_0_9"
              }
            ]
          }
        ],
        "SS/SSD": [
          {
            id: "GT_W_1",
            type: "WINDOWS",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载客户端解压至任意磁盘并运行",
                id: "GT_W_1_1"
              },
              {
                num: 2,
                content:
                  "任务栏右下角右键纸飞机图标->服务器订阅->SSD服务器订阅设置，将订阅链接设置为下面的地址，确定之后再更新SSD服务器订阅",
                id: "GT_W_1_2"
              },
              {
                num: 3,
                content:
                  "选择一个合适的服务器，代理规则选“绕过局域网和大陆”，即可上网",
                id: "GT_W_1_3"
              },
              {
                num: "备用",
                content: "点击复制链接，然后右键小飞机->从剪贴板复制地址",
                id: "GT_W_1_4",
                extra: true
              }
            ]
          },
          {
            id: "GT_M_1",
            type: "MACOS",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载 ShadowsocksX-NG，并安装",
                id: "GT_M_1_1"
              },
              {
                num: 2,
                content:
                  "点击按钮复制链接,然后右击托盘小飞机图标->从剪贴板导入服务器配置链接",
                id: "GT_M_1_2",
                extra: true
              },
              {
                num: 3,
                content:
                  "再次右击托盘小飞机图标->服务器，选择一个服务器即可上网",
                id: "GT_M_1_3"
              }
            ]
          },
          {
            id: "GT_L_1",
            type: "LINUX",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "安装shadowsocks-qt5",
                id: "GT_L_1_1"
              },
              {
                num: 2,
                content:
                  "按win键搜索找到软件，填写对应的服务器IP、端口、密码、加密方式，并配置系统代理模式",
                id: "GT_L_1_2"
              },
              {
                num: 3,
                content: "配置浏览器代理模式",
                id: "GT_L_1_3"
              },
              {
                num: 4,
                content: "点击connect连接",
                id: "GT_L_1_4"
              }
            ]
          },
          {
            id: "GT_I_1",
            type: "IOS",
            isActive: false,
            steps: [
              {
                num: 1,
                content:
                  "在非国区AppStore中搜索Shadowrocket或Potatso Lite下载安装",
                id: "GT_I_1_1"
              },
              {
                num: 2,
                content: " 打开节点列表，点开自己需要的节点详情，自行导入节点",
                id: "GT_I_1_2",
                extra: true
              }
            ]
          },
          {
            id: "GT_A_1",
            type: "ANDROID",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载客户端，如有需要可下载混淆插件",
                id: "GT_A_1_1"
              },
              {
                num: 2,
                content: "安装后，在手机上点击订阅链接复制",
                id: "GT_A_1_2"
              },
              {
                num: 3,
                content:
                  "打开 ShadowsocksD ，点击右上角的“加号”，选择“添加订阅”，将剪贴板中的内容粘贴进去，点击“OK”，稍等片刻即可看见订阅的节点",
                id: "GT_A_1_3",
                extra: true
              }
            ]
          },
          {
            id: "GT_R_1",
            type: "ROUTER",
            isActive: false,
            steps: [
              {
                num: "梅林",
                content: "",
                id: "GT_R_1_0"
              },
              {
                num: 1,
                content: "进入下载页面 下载“科学上网”插件",
                id: "GT_R_1_1"
              },
              {
                num: 2,
                content:
                  "进入路由器管理页面->系统管理->勾选“Format JFFS partition at next boot”和“Enable JFFS custom scripts and configs”->应用本页面设置，重启路由器",
                id: "GT_R_1_2"
              },
              {
                num: 3,
                content:
                  "进入路由器管理页面->软件中心->离线安装，上传插件文件进行安装",
                id: "GT_R_1_3"
              },
              {
                num: 4,
                content:
                  "进入“科学上网”插件->节点管理，手动添加节点，打开“科学上网”开关->保存&应用",
                id: "GT_R_1_4"
              },
              {
                num: "padavan",
                content: "",
                id: "GT_R_1_5"
              },
              {
                num: 5,
                content: "进入路由器管理页面->扩展功能->Shadowsocks",
                id: "GT_R_1_6"
              },
              {
                num: 6,
                content: "手动添加需要的节点并勾选->应用主SS->打开上方的开关",
                id: "GT_R_1_7"
              }
            ]
          }
        ],
        V2RAY: [
          {
            id: "GT_W_2",
            type: "WINDOWS",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载 V2RayN，解压至任意磁盘并运行",
                id: "GT_W_2_1"
              },
              {
                num: 2,
                content:
                  "双击任务栏右下角V2RayN图标->订阅->订阅设置->添加->填入下方的地址，点击确定",
                id: "GT_W_2_2"
              },
              {
                num: 3,
                content:
                  "再次点击订阅->更新订阅，右击任务栏右下角V2RayN图标->启动Http代理",
                id: "GT_W_2_3"
              },
              {
                num: 4,
                content: "自行选择“Http代理模式”和“服务器”",
                id: "GT_W_2_4"
              }
            ]
          },
          {
            id: "GT_M_2",
            type: "MACOS",
            isActive: false,
            steps: []
          },
          {
            id: "GT_L_2",
            type: "LINUX",
            isActive: false,
            steps: []
          },
          {
            id: "GT_I_2",
            type: "IOS",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "在非国区AppStore中搜索Shadowrocket下载安装",
                id: "GT_I_2_1"
              },
              {
                num: 2,
                content:
                  "打开 Shadowrocket，点击右上角的 + 号，类型选择“Subscribe”，URL填写以下地址并点击右上角完成即可。",
                id: "GT_I_2_2"
              },
              {
                num: "备用",
                content: "使用shadowrocket一键订阅",
                id: "GT_I_2_3",
                extra: true
              }
            ]
          },
          {
            id: "GT_A_2",
            type: "ANDROID",
            isActive: false,
            steps: [
              {
                num: 1,
                content: "下载 V2RayNG并安装",
                id: "GT_A_2_1"
              },
              {
                num: 2,
                content:
                  "点击左上角菜单按钮展开菜单->订阅设置->点击右上角“+”，URL填写以下地址并点击右上角“√”保存",
                id: "GT_A_2_2"
              },
              {
                num: 3,
                content: "回到软件主界面->点击右上角“更多”按钮->更新订阅",
                id: "GT_A_2_3"
              },
              {
                num: 4,
                content: "选择一个节点，点击右下角按钮订阅",
                id: "GT_A_2_4"
              }
            ]
          },
          {
            id: "GT_R_2",
            type: "ROUTER",
            isActive: false,
            steps: [
              {
                num: "梅林",
                content: "",
                id: "GT_R_2_0"
              },
              {
                num: 1,
                content: "进入下载页面 下载“科学上网”插件",
                id: "GT_R_2_1"
              },
              {
                num: 2,
                content:
                  "进入路由器管理页面->系统管理->勾选“Format JFFS partition at next boot”和“Enable JFFS custom scripts and configs”->应用本页面设置，重启路由器",
                id: "GT_R_2_2"
              },
              {
                num: 3,
                content:
                  "进入路由器管理页面->软件中心->离线安装，上传插件文件进行安装",
                id: "GT_R_2_3"
              },
              {
                num: 4,
                content:
                  "进入“科学上网”插件->节点管理，手动添加节点，打开“科学上网”开关->保存&应用",
                id: "GT_R_2_4"
              }
            ]
          }
        ]
      }
    };
  }
};
</script>
