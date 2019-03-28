<template>
  <label for="remember_me" class="flex align-center">
    <span class="uim-check" :class="{ uimchecked:boxChecked }">
      <font-awesome-icon icon="check" :class="'uim-checkbox-icon'" />
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
  model: {
    prop: 'isChecked',
    event: 'change'
  },
  props: ['isChecked'],
  data: function () {
    return {
      boxChecked: false
    }
  },
  methods: {
    setClass () {
      if (this.boxChecked === false) {
        this.boxChecked = true
      } else {
        this.boxChecked = false
      }
    }
  }
}
</script>

<style>
input[type="checkbox"].uim-checkbox {
  width: 16px;
  height: 16px;
  opacity: 0;
  position: relative;
  bottom: 0.1rem;
}

.uim-check-content {
  font-size: 0.9rem;
  position: relative;
  left: 0.2rem;
}

.uim-checkbox-icon {
  position: absolute;
  bottom: 1px;
  font-size: 14px;
}

.uim-check {
  position: relative;
  top: 1px;
  height: 14px;
  width: 15px;
  background: aliceblue;
  transition: all 0.3s;
}

.uimchecked {
  background: #d1335b;
  box-shadow: 0 0 5px 1px gray;
}
</style>
