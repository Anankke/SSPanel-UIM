<template>
  <div>
    <h5>在下方输入充值码进行充值：</h5>
    <input type="text" v-model="code" class="tips tips-blue" placeholder="输入充值码">
    <button @click="charge" class="tips tips-gold">充值</button>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";
import { _post } from "../../js/fetch";

export default {
  mixins: [userMixin, storeMap],
  data: function() {
    return {
      code: ""
    };
  },
  methods: {
    charge() {
      let body = { code: this.code };
      _post("/user/code", JSON.stringify(body), "include").then(r => {
        if (r.ret) {
          this.reConfigResourse();
          let callConfig = {
            msg: r.msg,
            icon: "check-circle",
            time: 1500
          };
          this.callMsgr(callConfig);
          this.code = "";
        } else {
          let callConfig = {
            msg: r.msg,
            icon: "times-circle",
            time: 1500
          };
          this.callMsgr(callConfig);
          this.code = "";
        }
      });
    }
  }
};
</script>
