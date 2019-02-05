<template>
    <div class="pw pure-g">
        <router-view></router-view>
    </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'

export default {
  mixins: [storeMap]
}
</script>
