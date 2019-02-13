<template>
  <transition name="fade" mode="out-in">
    <div class="uim-tooltip">
      <div class="tooltip-inner">
        <slot name="tooltip-inner"></slot>
      </div>
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

.tooltip-inner {
  font-size: 14px;
  padding: .3rem 1rem;
  word-break: break-all;
}
</style>
