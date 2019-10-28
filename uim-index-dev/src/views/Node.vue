<template>
  <div class="page-user pure-u-1">
    <div class="title-back flex align-center">NODELIST</div>

    <transition name="loading-fadex" mode="out-in">
      <div class="loading flex align-center" v-if="userLoadState === 'beforeload'">NODELIST</div>

      <div class="loading flex align-center" v-else-if="userLoadState === 'loading'" key="loading">
        <div class="spinnercube">
          <div class="cube1"></div>
          <div class="cube2"></div>
        </div>
      </div>

      <div class="usrcenter text-left pure-g space-around" v-else-if="userLoadState === 'loaded'">
        <div class="pure-u-1 pure-u-xl-6-24 pure-g usrcenter-left">
          <div class="card pure-u-1">
            <div class="flex space-between align-center">
              <div class="card-title">节点详情</div>
            </div>
            <div class="card-body">
              <div>节点名称：{{currentNode.name}}</div>
              <div class="node-data">节点地址：{{currentNode.server}}</div>
              <div class="node-data">{{currentNode.info}}</div>
              <div class="node-data">
                <span class="tips tips-blue">流量</span>
                <span>{{currentNode.traffic_used}}</span>
                <span v-if="currentNode.traffic_limit>0">{{'/ ' + currentNode.traffic_limit}}</span> GB
              </div>

              <div>
                <div class="node-data">
                  <span class="tips tips-blue">在线</span>
                  <span v-if="currentNode.online_user>0">{{currentNode.online_user}}</span>
                  <span v-else>0</span>
                </div>
                <div class="node-data">
                  <span class="tips tips-blue">倍率</span>
                  <span>{{currentNode.traffic_rate}}</span>
                </div>
                <div class="node-data">
                  <span class="tips tips-blue">速率</span>
                  <span v-if="currentNode.bandwidth>0">{{currentNode.bandwidth}}</span>
                  <span v-else>无限制</span>
                </div>
                <div class="node-data">
                  <span class="tips tips-blue">负载</span>
                  <span v-if="currentNode.latest_load>0">{{currentNode.latest_load}} %</span>
                  <span v-else>0</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="pure-u-1 pure-u-xl-17-24">
          <div class="card margin-nobottom-sm">
            <div class="flex space-between align-center">
              <div class="card-title">节点列表</div>
              <div class="card-title-right">
                <uim-dropdown>
                  <template #dpbtn-content>
                    <transition name="fade" mode="out-in">
                      <div
                        :key="currentNodeClass"
                      >{{currentNodeClass===0 ? '普通节点' : "VIP " + currentNodeClass}}</div>
                    </transition>
                  </template>
                  <template #dp-menu>
                    <li
                      @click="nodeClassChange(item.class)"
                      v-for="item in classDrop"
                      :key="item.class"
                    >{{item.name}}</li>
                  </template>
                </uim-dropdown>
              </div>
            </div>
            <div class="card-body">
              <div class="nodelist flex space-between">
                <div
                  v-for="(node, index) in nodeFilter"
                  :class="{ 'nodeitem-avtive':currentNode.id === node.id }"
                  class="nodeitem"
                  :key="node.id"
                  @click="setCurrentNode(index)"
                >
                  <div class="flex space-between align-center">
                    <div class="nodename">
                      <img
                        v-if="globalConfig.enableFlag === 'true'"
                        class="flag"
                        :src="'/images/prefix/' + node.flag"
                        alt
                      />
                      {{node.name | tailFilter}}
                    </div>
                    <div
                      class="node-online-status"
                      :class="[{'node-online':node.online === 1},{'node-unset':node.online === 0},{'node-offline':node.online !== 0 && node.online !==1}]"
                    ></div>
                  </div>
                </div>
                <div class="nodeitem-fix"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import nodeMap from "@/mixins/nodeMap";
import { _get } from "../js/fetch";

import Dropdown from "@/components/dropdown.vue";

export default {
  mixins: [storeMap, nodeMap],
  components: {
    "uim-dropdown": Dropdown
  },
  filters: {
    tailFilter: function(value) {
      let index = value.indexOf(" - ");
      value = value.slice(0, index);
      return value;
    }
  },
  computed: {
    classDrop: function() {
      let result = [];
      let firstClass = this.nodeList[0].class;
      if (firstClass === 0) {
        result.push({
          name: "普通节点",
          class: 0
        });
      } else {
        result.push({
          name: "VIP " + firstClass,
          class: firstClass
        });
      }
      for (let i = 1; i <= this.nodeList.length - 1; i++) {
        let curClass = this.nodeList[i].class;
        let prevClass = this.nodeList[i - 1].class;
        if (curClass !== prevClass) {
          result.push({
            name: "VIP " + curClass,
            class: curClass
          });
        }
      }
      return result;
    },
    nodeFilter: function() {
      return this.nodeList.filter(node => {
        return node.class === this.currentNodeClass;
      });
    }
  },
  data: function() {
    return {
      userLoadState: "beforeload",
      currentNodeClass: 0,
      currentNode: {}
    };
  },
  methods: {
    nodeClassChange(num) {
      this.currentNodeClass = num;
    },
    setCurrentNode(index) {
      this.currentNode = this.nodeFilter[index];
    }
  },
  mounted() {
    let self = this;
    this.userLoadState = "loading";

    if (this.userCon === "") {
      _get("/getuserinfo", "include")
        .then(r => {
          if (r.ret === 1) {
            this.setUserCon(r.info.user);
            this.setUserSettings(this.userCon);
            window.console.log(this.userCon);
            if (r.info.ann) {
              this.setAnn(r.info.ann);
            }
            this.setAllBaseCon({
              subUrl: r.info.subUrl,
              ssrSubToken: r.info.ssrSubToken,
              iosAccount: r.info.iosAccount,
              iosPassword: r.info.iosPassword,
              displayIosClass: r.info.displayIosClass
            });
            this.setBaseUrl(r.info.baseUrl);
            this.setMergeSub(r.info.mergeSub);
          } else if (r.ret === -1) {
            this.ajaxNotLogin();
          }
        })
        .then(r => {});
    }

    if (this.nodeList === "") {
      _get("/getnodelist", "include")
        .then(r => {
          if (r.ret === 1) {
            window.console.log(r);

            this.setNodeList(r.nodeinfo.nodes);

            window.console.log(this.nodeList);

            this.currentNodeClass = this.nodeList[0].class;

            this.setCurrentNode(0);
          } else if (r.ret === -1) {
            this.ajaxNotLogin();
          }
        })
        .then(r => {
          self.userLoadState = "loaded";
        });
    } else {
      self.userLoadState = "loaded";
      this.setCurrentNode(0);
    }
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
.nodename {
  display: flex;
  align-items: center;
}
.nodeitem {
  border: 1px solid #434857;
  margin-bottom: 0.5rem;
  padding: 0.6rem;
  transition: 0.3s all;
  flex-basis: 100%;
}
.nodeitem:hover,
.nodeitem-avtive {
  border: 1px solid #fff;
  box-shadow: 0 0 5px 1px grey;
}
.nodeitem-fix {
  flex-basis: 0;
}
.nodelist {
  overflow-y: auto;
  max-height: 625px;
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}
.node-online-status {
  text-align: end;
  width: 15px;
  border-radius: 50%;
  height: 15px;
}
.node-online {
  background-color: #52c41a;
  box-shadow: 0px 0px 5px #8fe663;
}
.node-unset {
  background-color: grey;
  box-shadow: 0px 0px 5px white;
}
.node-offline {
  background-color: #d1335b;
  box-shadow: 0px 0px 5px #e2738f;
}
.node-data {
  margin-top: 0.5rem;
}
.node-data .tips {
  margin-right: 1rem;
}
.flag {
  width: 35px;
  margin-right: .5rem;
}

@media screen and (min-width: 35.5em) {
}

@media screen and (min-width: 48em) {
  .nodeitem {
    flex-basis: 49%;
  }
}

@media screen and (min-width: 64em) {
  .nodeitem {
    flex-basis: 32%;
  }
  .nodeitem-fix {
    flex-basis: 32%;
  }
}

@media screen and (min-width: 80em) {
  .nodeitem {
    flex-basis: 32%;
  }
  .nodeitem-fix {
    flex-basis: 32%;
  }
}
</style>
