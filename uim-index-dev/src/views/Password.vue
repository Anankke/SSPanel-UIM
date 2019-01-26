<template>
    <div class="pw pure-g">
        <router-view></router-view>
    </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'

export default {
    delimiters: ['$[',']$'],
    mixins: [storeMap],
}
</script>
