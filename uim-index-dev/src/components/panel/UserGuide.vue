<template>
  <div>
    <div class="flex space-between align-center">
      <div class="card-title">配置指南</div>
      <div class="card-title-right">
        <uim-dropdown>
          <span slot="dpbtn-content">
            <transition name="fade" mode="out-in">
              <div :key="agentToken.menuKey">{{currentDlType}}</div>
            </transition>
          </span>
          <ul slot="dp-menu">
            <li
              @click="changeAgentType"
              v-for="dl in downloads"
              :data-type="dl.type"
              :key="dl.type"
            >{{dl.type}}</li>
          </ul>
        </uim-dropdown>
      </div>
    </div>
    <div class="card-body">
      <div class="user-guide pure-g relative">
        <div class="pure-u-20-24 relative">
          <transition name="slide-fadex" mode="in-out">
            <div class="absolute guide-area" :key="currentAgentType">
              <p v-for="step in currentSteps" :key="step.id">
                <span class="tips tips-blue">{{step.num}}</span>
                {{step.content}}
              </p>
            </div>
          </transition>
        </div>
        <div class="pure-u-4-24 flex align-center">
          <div class="userguide-bookmark flex align-center wrap" :key="agentToken.markKey">
            <button v-for="mark in agentToken.tips" @click="setCurrentAgentType(mark.type)" :key="mark.id"><span class="btn-anchor"></span>{{mark.type}}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

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
            tips: this.agentContent['SSR']
          };
          break;
        case "SS/SSD":
          return {
            menuKey: "guide-nemu-ss",
            contentKey: "guide-content-ss",
            markKey: "guide-mark-ss",
            tips: this.agentContent['SS/SSD']
          };
          break;
        case "V2RAY":
          return {
            menuKey: "guide-nemu-v2",
            contentKey: "guide-content-v2",
            markKey: "guide-mark-v2",
            tips: this.agentContent['V2RAY']
          };
          break;
      }
    },
    currentSteps: function() {
      switch (this.currentAgentType) {
        case 'WINDOWS':
          return this.agentContent[this.currentDlType][0].steps;
          break;
        case 'MACOS':
          return this.agentContent[this.currentDlType][1].steps;
          break;
        case 'LINUX':
          return this.agentContent[this.currentDlType][2].steps;
          break;
        case 'IOS':
          return this.agentContent[this.currentDlType][3].steps;
          break;
        case 'ANDROID':
          return this.agentContent[this.currentDlType][4].steps;
          break;
        case 'ROUTER':
          return this.agentContent[this.currentDlType][5].steps;
          break;
      }
    },
  },
  data: function() {
    return {
      agentContent: {
        SSR: [
          {
            id: "GT_W_0",
            type: "WINDOWS",
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
              }
            ]
          },
          {
            id: "GT_M_0",
            type: "MACOS",
            steps: [
              {
                num: 1,
                content: "下载客户端，安装并启动",
                id: "GT_M_0_1"
              },
              {
                num: 2,
                content:
                  "右击托盘纸飞机图标->服务器->服务器订阅，填入订阅地址",
                id: "GT_M_0_2"
              },
              {
                num: 3,
                content:
                  "更新订阅成功后服务器列表即可出现节点，选择一个节点",
                id: "GT_M_0_3"
              },
              {
                num: 4,
                content:
                  "再次右击托盘纸飞机图标，如果shadowsocks还未打开，则需要点击打开",
                id: "GT_M_0_4"
              },
            ]
          },
          {
            id: "GT_L_0",
            type: "LINUX",
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
                content:
                  "配置浏览器代理模式",
                id: "GT_L_0_3"
              },
              {
                num: 4,
                content:
                  "点击connect连接",
                id: "GT_L_0_4"
              },
            ]
          },
          {
            id: "GT_I_0",
            type: "IOS",
            steps: [
              {
                num: 1,
                content: "在非国区AppStore中搜索Shadowrocket或Potatso Lite下载安装",
                id: "GT_I_0_1"
              },
              {
                num: 2,
                content:
                  "打开 Potatso Lite，点击添加代理，点击右上角的 + 号，选择“订阅”，名字任意填写，开启自动更新，URL填写以下地址并保存即可",
                id: "GT_I_0_2"
              },
              {
                num: 3,
                content:
                  "如果使用shadowrocket,打开 Shadowrocket，点击右上角的 + 号，类型选择“Subscribe”，URL填写以下地址并点击右上角完成即可",
                id: "GT_I_0_3"
              },
            ]
          },
          {
            id: "GT_A_0",
            type: "ANDROID",
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
                content:
                  "点击右上角的纸飞机图标即可连接",
                id: "GT_A_0_4"
              },
            ]
          },
        ],
        'SS/SSD': [
          {
            id: "GT_W_1",
            type: "windows",
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
                id: "GT_W_1_2"
              }
            ]
          }
        ],
        V2RAY: [
          {
            id: "GT_W_2",
            type: "windows",
            steps: [
              {
                num: 1,
                content: "下载客户端解压至任意磁盘并运行",
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
          }
        ]
      }
    };
  }
};
</script>
