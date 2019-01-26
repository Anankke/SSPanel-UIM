<template>
  <span
    @click="btnSwitch"
    :class="{ 'uim-switch-body-active':switchStatus }"
    class="uim-switch-body"
  >
    <input
      :checked="isBtnActive"
      v-on:change="$emit('change',$event.target.checked)"
      type="checkbox"
    >
  </span>
</template>

<script>
export default {
  delimiters: ["$[", "]$"],
  model: {
    prop: "isBtnActive",
    event: "change"
  },
  props: ["isBtnActive"],
  data: function() {
    return {
      switchStatus: this.isBtnActive
    };
  },
  methods: {
    btnSwitch() {
      if (this.switchStatus === false) {
        this.switchStatus = true;
      } else {
        this.switchStatus = false;
      }
    }
  }
};
</script>
