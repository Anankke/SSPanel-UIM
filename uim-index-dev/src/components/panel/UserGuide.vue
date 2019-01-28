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
      <div class="user-guide relative">
        <transition name="slide-fadex" mode="in-out">
          <div class="absolute" :key="agentToken.contentKey">
            <p v-for="tip in agentToken.tipsArr" :key="tip.id">
              <span class="tips tips-blue">{{tip.num}}</span>{{tip.content}}
            </p>
          </div>
        </transition>
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
      switch(this.currentDlType) {
        case 'SSR':
          return {
            menuKey: 'guide-nemu-ssr',
            contentKey: 'guide-content-ssr',
            tipsArr: this.agentContent.ssrTip,
          };
          break;
        case 'SS/SSD':
          return {
            menuKey: 'guide-nemu-ss',
            contentKey: 'guide-content-ss',
            tipsArr: this.agentContent.ssTip,
          };
          break;
        case 'V2RAY':
          return {
            menuKey: 'guide-nemu-v2',
            contentKey: 'guide-content-v2',
            tipsArr: this.agentContent.v2Tip,
          };
          break;
      }
    }
  },
  data: function() {
    return {
      agentContent: {
        ssrTip: [
          {
            num: 1,
            content: '下载客户端解压至任意磁盘并运行',
            id: 'GT_0_1',
          },
        ],
        ssTip: [
          {
            num: 1,
            content: '下载客户端解压至任意磁盘并运行',
            id: 'GT_1_1',
          },
        ],
        v2Tip: [
          {
            num: 1,
            content: '下载客户端解压至任意磁盘并运行',
            id: 'GT_2_1',
          },
        ],
      },
    };
  }
};
</script>
