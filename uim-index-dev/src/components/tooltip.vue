<template>
  <transition name="fade" mode="out-in">
    <div class="uim-tooltip">
      <slot name="tooltip-inner"></slot>
    </div>
  </transition>
</template>

<script>
export default {};
</script>

<style>
.uim-tooltip {
  position: absolute;
  z-index: 2;
  box-shadow: 0 0 5px 1px gray;
  border-radius: 4px;
  background: white;
  color: #434857;
}

.uim-tooltip-top {
  bottom: 125%;
  padding: 0.3rem;
}

.uim-tooltip.uim-tooltip-top:after {
  content: "";
  display: inline-block;
  position: absolute;
  bottom: -7px;
  width: 15px;
  height: 15px;
  background-color: white;
  transform: rotateZ(45deg);
  z-index: -1;
}
</style>

