<template>
  <div @mousewheel="wheelChange" class="user-resourse">
    <div class="flex align-baseline">
      <div class="card-title">可用资源</div>
      <span>
        <button @click="dataRefresh" class="tips tips-green">
          <font-awesome-icon icon="sync-alt"/>&nbsp;刷新
        </button>
      </span>
    </div>
    <div class="card-body">
      <div class="pure-g wrap">
        <div v-for="tip in calcResourse" class="pure-u-1-2 pure-u-lg-4-24" :key="tip.name">
          <p class="tips tips-blue">{{tip.name}}</p>
          <p
            class="font-light user-config"
            :class="{ 'font-gold-trans':resourseTrans,'font-green-trans':isDataRefreshed }"
          >
            <span class="user-config"></span>
            {{tip.content}}
          </p>
        </div>
        <div class="pure-u-1 pure-u-lg-8-24">
          <uim-progressbar class="uim-progressbar-sub">
            <span slot="uim-progressbar-label">已用流量/今日已用</span>
            <div
              slot="progress"
              class="uim-progressbar-gold uim-progressbar-progress"
              :style="{ width:transferObj.usedtotal + '%' }"
            ></div>
            <div
              slot="progress-fold"
              class="uim-progressbar-red uim-progressbar-progress uim-progressbar-fold"
              :style="{ width:transferObj.usedtoday + '%' }"
            ></div>
            <span
              class="user-config"
              :class="{ 'font-green-trans':isDataRefreshed,'font-gold-trans':sign.isTrsfficRefreshed }"
              slot="progress-text"
            >{{userCon.lastUsedTraffic + '/' + userCon.todayUsedTraffic}}</span>
            <span
              slot="progress-sign"
              class="user-config"
              :class="{ 'font-green-trans':isDataRefreshed,'font-gold-trans':sign.isTrsfficRefreshed }"
            >{{transferObj.usedtoday.toFixed(1) + '%'}}</span>
          </uim-progressbar>
          <uim-progressbar>
            <span slot="uim-progressbar-label">可用流量</span>
            <div
              slot="progress"
              class="uim-progressbar-blue uim-progressbar-progress"
              :style="{ width:transferObj.remain + '%' }"
            ></div>
            <span
              :class="{ 'font-green-trans':isDataRefreshed,'font-gold-trans':sign.isTrsfficRefreshed }"
              slot="progress-text"
            >{{userCon.unUsedTraffic}}</span>
            <span
              slot="progress-sign"
              class="user-config"
              :class="{ 'font-green-trans':isDataRefreshed,'font-gold-trans':sign.isTrsfficRefreshed }"
            >{{transferObj.remain.toFixed(1) + '%'}}</span>
          </uim-progressbar>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";
import userSetMixin from "@/mixins/userSetMixin";

import Progressbar from "@/components/progressbar.vue";

import { _get } from "../../js/fetch";

export default {
  mixins: [userMixin, storeMap, userSetMixin],
  components: {
    "uim-progressbar": Progressbar
  },
  computed: {
    calcResourse: function() {
      let resourse = this.userSettings.resourse;
      for (let i = 0; i < resourse.length; i++) {
        switch (resourse[i].name) {
          case "在线设备数":
            if (this.userCon.node_connector !== 0) {
              this.setReasourse({
                index: i,
                content:
                  this.userCon.online_ip_count +
                  " / " +
                  this.userCon.node_connector
              });
            } else {
              this.setReasourse({
                index: i,
                content: this.userCon.online_ip_count + " / 无限制"
              });
            }
            break;
          case "端口速率":
            if (this.userCon.node_speedlimit !== 0) {
              this.setReasourse({
                index: i,
                content: this.userCon.node_speedlimit + " Mbps"
              });
            } else {
              this.setReasourse({ index: i, content: "无限制" });
            }
            break;
          default:
            break;
        }
      }
      return resourse;
    },
    transferObj: function() {
      let enable = this.userCon.transfer_enable;
      let upload = this.userCon.u;
      let download = this.userCon.d;
      let lastdayTransfer = this.userCon.last_day_t;
      let obj = {
        remain:
          enable === 0 ? 0 : ((enable - upload - download) / enable) * 100,
        usedtoday: enable === 0 ? 0 : ((upload + download) / enable) * 100,
        usedtotal: enable === 0 ? 0 : (lastdayTransfer / enable) * 100
      };
      return obj;
    }
  },
  data: function() {
    return {
      isDataRefreshed: false
    };
  },
  methods: {
    DateParse(str_date) {
      let str_date_splited = str_date.split(/[^0-9]/);
      return new Date(
        str_date_splited[0],
        str_date_splited[1] - 1,
        str_date_splited[2],
        str_date_splited[3],
        str_date_splited[4],
        str_date_splited[5]
      );
    },
    calcExpireDays(classExpire, userExpireIn) {
      let levelExpire = this.DateParse(classExpire);
      let accountExpire = this.DateParse(userExpireIn);
      let nowDate = new Date();
      let a = nowDate.getTime();
      let b = levelExpire - a;
      let c = accountExpire - a;
      let levelExpireDays = Math.floor(b / (24 * 3600 * 1000));
      let accountExpireDays = Math.floor(c / (24 * 3600 * 1000));
      if (levelExpireDays < 0 || levelExpireDays > 315360000000) {
        this.addNewUserCon({ levelExpireDays: "无限期" });
        this.setReasourse({ index: 0, content: this.userCon.levelExpireDays });
      } else {
        this.addNewUserCon({ levelExpireDays: levelExpireDays });
        this.setReasourse({
          index: 0,
          content: this.userCon.levelExpireDays + " 天"
        });
      }
      if (accountExpireDays < 0 || accountExpireDays > 315360000000) {
        this.addNewUserCon({ accountExpireDays: "无限期" });
        this.setReasourse({
          index: 1,
          content: this.userCon.accountExpireDays
        });
      } else {
        this.addNewUserCon({ accountExpireDays: accountExpireDays });
        this.setReasourse({
          index: 1,
          content: this.userCon.accountExpireDays + " 天"
        });
      }
    },
    dataRefresh() {
      _get("/gettransfer", "include").then(r => {
        if (r.ret === 1) {
          this.addNewUserCon(r.arr);
          this.reConfigResourse();
          this.showTransition("isDataRefreshed");
        } else if (r.ret === 0) {
          this.ajaxNotLogin();
        }
      });
    },
    showTransition(key) {
      this[key] = true;
      setTimeout(() => {
        this[key] = false;
      }, 500);
    }
  },
  created() {
    this.calcExpireDays(this.userCon.class_expire, this.userCon.expire_in);
    _get("/gettransfer", "include").then(r => {
      if (r.ret === 1) {
        this.addNewUserCon(r.arr);
      } else if (r.ret === -1) {
        this.ajaxNotLogin();
      }
    });
  }
};
</script>
