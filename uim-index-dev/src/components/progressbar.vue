<template>
  <div class="uim-progressbar">
    <div class="uim-progressbar-label">
      <slot name="uim-progressbar-label"></slot>
    </div>
    <div class="uim-progressbar-inner">
      <slot name="progress"></slot>
      <slot name="progress-fold"></slot>
      <div class="uim-progress-text">
        <slot name="progress-text"></slot>
      </div>
    </div>
    <span class="uim-progress-sign">
      <slot name="progress-sign"></slot>
    </span>
  </div>
</template>

<script>
export default {
  delimiters: ["$[", "]$"]
};
</script>
