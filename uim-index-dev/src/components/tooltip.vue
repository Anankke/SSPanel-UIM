<template>
  <transition name="fade" mode="out-in">
    <div class="uim-tooltip">
      <slot name="tooltip-inner"></slot>
    </div>
  </transition>
</template>

<script>
export default {
  delimiters: ["$[", "]$"]
};
</script>
