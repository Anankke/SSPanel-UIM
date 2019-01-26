<template>
  <label for="remember_me" class="flex align-center">
    <span class="uim-check" :class="{ uimchecked:boxChecked }">
      <i class="fa fa-check uim-checkbox-icon"></i>
      <input
        :checked="isChecked"
        @click="setClass"
        @change="$emit('change',$event.target.checked)"
        class="uim-checkbox"
        type="checkbox"
      >
    </span>
    <span class="uim-check-content">
      <slot name="content"></slot>
    </span>
  </label>
</template>

<script>
export default {
  delimiters: ["$[", "]$"],
  model: {
    prop: "isChecked",
    event: "change"
  },
  props: ["isChecked"],
  data: function() {
    return {
      boxChecked: false
    };
  },
  methods: {
    setClass() {
      if (this.boxChecked == false) {
        this.boxChecked = true;
      } else {
        this.boxChecked = false;
      }
    }
  }
};
</script>
