<template>
    <div class="user pure-g">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    delimiters: ['$[',']$'],
    props: ['routermsg'],
}
</script>
