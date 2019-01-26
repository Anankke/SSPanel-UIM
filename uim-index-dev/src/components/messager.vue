<template>
  <div class="uim-messager">
    <div>
      <slot name="icon"></slot>
      <slot name="msg"></slot>
      <slot name="html"></slot>
    </div>
  </div>
</template>

<script>
export default {
  delimiters: ["$[", "]$"]
};
</script>
