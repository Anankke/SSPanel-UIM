<template>
  <div>
    <div class="card-title">配置指南</div>
    <div class="card-body">
      <div class="user-guide"></div>
    </div>
  </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'
import userMixin from '@/mixins/userMixin'

export default {
  mixins: [userMixin, storeMap]
};
</script>
