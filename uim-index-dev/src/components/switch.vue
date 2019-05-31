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
  model: {
    prop: 'isBtnActive',
    event: 'change'
  },
  props: ['isBtnActive'],
  data: function () {
    return {
      switchStatus: this.isBtnActive
    }
  },
  methods: {
    btnSwitch () {
      if (this.switchStatus === false) {
        this.switchStatus = true
      } else {
        this.switchStatus = false
      }
    }
  }
}
</script>

<style>
.uim-switch-body {
  background-color: #ccc;
  border-radius: 25px;
  cursor: pointer;
}

.uim-switch-body:after {
  content: "";
  height: 20px;
  width: 20px;
  border-radius: 50%;
  position: absolute;
  background-color: white;
  top: 2px;
  left: 2px;
}

.uim-switch-body,
.uim-switch-body:after {
  transition: all 0.3s;
  display: inline-block;
}

.uim-switch-body input,
.uim-switch-body {
  height: 24px;
  width: 46px;
  position: relative;
}

.uim-switch-body input {
  z-index: 1;
  opacity: 0;
}

.uim-switch-body-active:after {
  transform: translateX(22px);
}

.uim-switch-body-active {
  background-color: #2d8cf0;
}

.switch-text {
  font-size: 14px;
  margin-left: .25rem;
}
</style>
