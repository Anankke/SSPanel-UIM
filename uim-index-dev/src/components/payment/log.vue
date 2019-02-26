<template>
  <div>
    <div class="user-table-container">
      <uim-table>
        <template #uim-th>
          <th>充值方式</th>
          <th>操作</th>
          <th>时间</th>
        </template>

        <template #uim-tbd>
          <tr class="uim-tr-body" v-for="(item,key) in codes.data" :key="key+item.id">
            <td>{{item.code}}</td>
            <td>￥{{item.number}}</td>
            <td>{{item.usedatetime}}</td>
          </tr>
        </template>
      </uim-table>
    </div>
    <div class="uim-pagenation-container">
      <uim-pagenation ref="pagenation" @turnPage="turnChargeLogPage" :pageinfo="pagenation"></uim-pagenation>
    </div>
  </div>
</template>

<script>
import Table from "@/components/table.vue";
import Pagenation from "@/components/pagenation.vue";

import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";
import { _post } from "../../js/fetch";

export default {
  mixins: [userMixin, storeMap],
  components: {
    "uim-table": Table,
    "uim-pagenation": Pagenation
  },
  data: function() {
    return {
      codes: "",
      pagenation: {
        lastPage: 1,
        currentPage: 1
      }
    };
  },
  methods: {
    turnChargeLogPage(current) {
      let body = { current: current };
      _post("/getChargeLog", JSON.stringify(body), "include").then(r => {
        this.codes = r.codes;
        this.pagenation.currentPage = r.codes.currentPage;
      });
    }
  },
  created() {
    let body = { current: 1 };
    _post("/getChargeLog", JSON.stringify(body), "include").then(r => {
      this.codes = r.codes;
      this.pagenation.lastPage = r.codes.last_page;
      this.$refs.pagenation.getButtonList();
    });
  }
};
</script>
