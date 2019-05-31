<template>
  <div class="uim-dropdown">
    <button @click.stop="show" class="uim-dropdown-btn">
      <span>
        <slot name="dpbtn-content"></slot>
      </span>
      <font-awesome-icon v-if="showArrow" icon="caret-down"/>
    </button>
    <transition name="dropdown-fade" mode="out-in">
      <div v-show="isDropdown" @click.stop="hide" class="uim-dropdown-menu">
        <ul>
          <slot name="dp-menu"></slot>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  props: {
    showArrow: {
      type: Boolean,
      default: false
    }
  },
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

<style>
.uim-dropdown-btn {
  font-size: 1rem;
  padding: 0.5rem 0.5rem;
  display: inline-block;
  border: 1px solid #434857;
  min-width: 80px;
  text-align: center;
  transition: all 0.3s;
  background: inherit;
  outline: none;
  position: relative;
}

.uim-dropdown-btn:hover {
  border: 1px solid white;
  box-shadow: 0 0 5px 1px gray;
}

.uim-dropdown-btn svg {
  position: absolute;
  right: 0.6rem;
  top: 0.75rem;
}

.uim-dropdown {
  position: relative;
  display: inline-block;
}

.uim-dropdown-menu {
  position: absolute;
  top: 100%;
  min-width: 120px;
  cursor: pointer;
  background: #1d1d1d;
  z-index: 2;
}

.uim-dropdown-menu ul {
  margin: 0;
  width: 100%;
  padding: 0;
  text-align: center;
  border: 1px solid #1997c6;
  box-shadow: 0 0 5px 1px gray;
}

.uim-dropdown-menu li {
  list-style: none;
  width: 100%;
  padding: 0.2rem 0;
  transition: all 0.3s;
}

.uim-dropdown-menu li a {
  display: block;
  width: 100%;
  height: 100%;
}

.uim-dropdown-menu li:hover {
  background: #1997c6;
  transition: all 0.3s;
}

.uim-dropdown-btn {
  min-width: 120px;
}

.dropdown-fade-enter-active,
.dropdown-fade-leave-active {
  transition: all 0.2s ease;
}

.dropdown-fade-enter,
.dropdown-fade-leave-to {
  transform-origin: top;
  transform: scaleY(0);
  opacity: 0;
}

.dl-link .uim-dropdown-btn,
.dl-link .uim-dropdown-menu {
  min-width: unset;
  width: 100%;
}
@media screen and (min-width: 35.5em) {
  .uim-dropdown-btn {
    padding: 0.5rem 0.5rem;
  }
}
</style>
