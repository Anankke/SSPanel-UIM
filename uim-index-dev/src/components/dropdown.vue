<template>
  <div class="uim-dropdown">
    <button @click.stop="show" class="uim-dropdown-btn">
      <slot name="dpbtn-content"></slot>
    </button>
    <transition name="dropdown-fade" mode="out-in">
      <div v-show="isDropdown" @click.stop="hide" class="uim-dropdown-menu">
        <slot name="dp-menu"></slot>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  delimiters: ["$[", "]$"],
  data: function() {
    return {
      isDropdown: false
    };
  },
  methods: {
    show() {
      if (this.isDropdown === false) {
        this.isDropdown = true;
      } else {
        this.isDropdown = false;
      }
    },
    hide() {
      if (this.isDropdown === true) {
        this.isDropdown = false;
      }
    }
  },
  mounted() {
    document.addEventListener("click", () => {
      this.hide();
    });
  }
};
</script>
