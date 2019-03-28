<template>
  <div class="uim-modal">
    <transition name="fade" mode="out-in">
      <div v-show="bindMask" class="uim-modal-mask"></div>
    </transition>
    <transition name="slide-fade" mode="out-in">
      <div v-show="bindCard" class="uim-modal-card">
        <div @click="$emit('closeModal')" class="uim-modal-close">
          <font-awesome-icon icon="times" />
        </div>
        <div class="uim-modal-title">
          <slot name="uim-modal-title"></slot>
        </div>
        <div class="uim-modal-body">
          <slot name="uim-modal-body"></slot>
        </div>
        <div class="uim-modal-footer">
          <div>
            <slot name="uim-modal-footer"></slot>
          </div>
          <div>
            <button @click="$emit('callOrderChecker')" class="uim-modal-confirm">确认</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import storeMap from '@/mixins/storeMap'

export default {
  mixins: [storeMap],
  props: ['bindMask', 'bindCard']
}
</script>

<style>
.uim-modal-mask,
.uim-modal {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  width: 100vw;
  background-color: black;
  opacity: 0.4;
  z-index: 20;
}

.uim-modal {
  opacity: 1;
  background-color: unset;
  display: flex;
  justify-content: center;
}

.uim-modal-card {
  z-index: 21;
  margin-top: 6rem;
  background-color: white;
  width: 450px;
  border-radius: 7px;
  color: #434857;
  margin-bottom: auto;
  box-shadow: 0 0 5px 2px gray;
  position: relative;
}

.uim-modal-title {
  border-bottom: 1px solid;
}

.uim-modal-title h3 {
  padding: 0.22rem 1.5rem;
  margin: 1rem 0;
}

.uim-modal-body {
  font-size: 18px;
  padding: 1rem 0 1rem 2rem;
}

.uim-modal-body div {
  flex-basis: 100%;
}

.uim-modal-footer {
  display: flex;
  padding: 0.75rem 0;
  border-top: 1px solid;
  align-items: center;
}

.uim-modal-footer div {
  flex-basis: 50%;
}

.uim-modal-footer > div:first-of-type {
  padding-left: 1.5rem;
}

.uim-modal-footer > div:last-of-type {
  text-align: right;
}

button.uim-modal-confirm {
  margin-right: 1.5rem;
  padding: 0.5rem 0.4rem;
  min-width: 80px;
  background-color: #2d8cf0;
  border-radius: 4px;
  border: 1px solid;
  color: white;
}

.uim-modal-close {
  position: absolute;
  right: 0.75rem;
  top: 0.45rem;
  font-size: 1.5rem;
  padding: 0.5rem 0.75rem;
  cursor: pointer;
}
</style>
