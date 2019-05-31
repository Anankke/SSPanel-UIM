<template>
  <div>
    <div class="card-title">公告栏</div>
    <div class="card-body">
      <div class="ann" v-html="ann.content"></div>
    </div>
  </div>
</template>

<script>
import storeMap from "@/mixins/storeMap";
import userMixin from "@/mixins/userMixin";

export default {
  mixins: [userMixin, storeMap]
};
</script>
