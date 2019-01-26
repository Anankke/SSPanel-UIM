<template>
    <div class="pw pure-g">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    delimiters: ['$[',']$'],
    mixins: [storeMap],
}
</script>
