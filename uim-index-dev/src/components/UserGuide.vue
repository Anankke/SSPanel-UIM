<template>
  <div>
    <div class="flex space-between">
      <div class="card-title">配置指南</div>
      <uim-dropdown>
        <span slot="dpbtn-content">栏目导航</span>
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
    <div class="card-body">
      <div class="user-guide">
        <transition name="rotate-fade" mode="out-in">
          <div v-if="currentDlType === 'SSR'">
            <p>
              <span class="tips tips-blue">1.</span>下载客户端解压至任意磁盘并运行
            </p>
          </div>
          <div v-else-if="currentDlType === 'SS/SSD'">
            <p>
              <span class="tips tips-blue">1</span>下载客户端解压至任意磁盘并运行
            </p>
          </div>
          <div v-else-if="currentDlType === 'V2RAY'">
            <p>
              <span class="tips tips-blue">1.</span>下载客户端解压至任意磁盘并运行
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
  data: function() {
    return {};
  }
};
</script>
