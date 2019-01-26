<template>
  <div class="uim-modal">
    <transition name="fade" mode="out-in">
      <div v-show="bindMask" class="uim-modal-mask"></div>
    </transition>
    <transition name="slide-fade" mode="out-in">
      <div v-show="bindCard" class="uim-modal-card">
        <div @click="$emit('closeModal')" class="uim-modal-close">
          <span class="fa fa-close"></span>
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
export default {
  delimiters: ["$[", "]$"],
  mixins: [storeMap],
  props: ["bindMask", "bindCard"]
};
</script>

