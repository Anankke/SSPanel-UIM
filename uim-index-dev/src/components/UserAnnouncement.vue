<template>
  <div>
    <div class="card-title">公告栏</div>
    <div class="card-body">
      <div class="ann" v-html="annC.content"></div>
    </div>
  </div>
</template>

<script>
export default {
  mixins: [userMixin, storeMap]
};
</script>
