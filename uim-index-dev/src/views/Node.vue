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
          <div class="card">
            <div class="flex space-between align-center">
              <div class="card-title">节点详情</div>
            </div>
            <div class="card-body">
              <div>节点地址：{{currentNode.server}}</div>
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
              <div class="nodelist">
                <div
                  v-for="(node, index) in nodeFilter"
                  :class="{ 'nodeitem-avtive':currentNode.id === node.id }"
                  class="nodeitem"
                  :key="node.id"
                  @click="setCurrentNode(index)"
                >
                  <div class="nodename">{{node.name}}</div>
                </div>
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
import { _get } from "../js/fetch";

import Dropdown from "@/components/dropdown.vue";

export default {
  mixins: [storeMap],
  components: {
    "uim-dropdown": Dropdown
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
      nodeList: [],
      user: {},
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

    _get("/getnodelist", "include")
      .then(r => {
        window.console.log(r);

        this.nodeList = r.nodeinfo.nodes;
        this.user = r.nodeinfo.user;

        this.currentNodeClass = this.nodeList[0].class;
        this.setCurrentNode(0);
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
.nodeitem {
  border: 1px solid #434857;
  margin-bottom: 0.5rem;
  padding: 0.6rem;
  transition: 0.3s all;
}
.nodeitem:hover,
.nodeitem-avtive {
  border: 1px solid #fff;
  box-shadow: 0 0 5px 1px grey;
}
.nodelist {
  overflow-y: auto;
  max-height: 625px;
}
</style>
