<template>
  <div @mousewheel="wheelChange" class="user-settings">
    <div class="card-title">连接信息</div>
    <div class="card-body">
      <div class="pure-g wrap">
        <div v-for="tip in userSettings.tipsLink" class="pure-u-1-2 pure-u-lg-4-24" :key="tip.name">
          <p class="tips tips-blue">{{tip.name}}</p>
          <p class="font-light">{{tip.content}}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'
import userMixin from '@/mixins/userMixin'
import userSetMixin from '@/mixins/userSetMixin'

export default {
  mixins: [userMixin, storeMap, userSetMixin]
}
</script>
