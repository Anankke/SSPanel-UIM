<template>
  <div class="user pure-g">
    <transition name="slide-fade" mode="out-in">
      <router-view></router-view>
    </transition>
  </div>
</template>

<script>
export default {
  props: ["routermsg"]
};
</script>
