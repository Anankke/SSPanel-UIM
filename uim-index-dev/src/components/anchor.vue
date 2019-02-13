<template>
  <div class="uim-anchor">
    <ul class="uim-anchor-inner">
      <slot name="uim-anchor-inner"></slot>
    </ul>
  </div>
</template>

<script>
export default {
  name: "uim-anchor"
};
</script>

<style>
.uim-anchor {
  position: absolute;
  right: 1rem;
}

.uim-anchor ul {
  background-color: transparent;
  margin: 0;
}

.uim-anchor li {
  list-style: none;
  width: 1.6rem;
  height: 1.75rem;
  display: block;
  background-color: transparent;
  border: 2px solid #434857;
  border-top-color: transparent;
  transition: all 0.3s;
  cursor: pointer;
  z-index: 1;
  outline: none;
}

.uim-anchor li:hover {
  border-color: #7cb7f7;
  box-shadow: 0 0 5px 1px #a1e2e2;
  border-top-color: #7cb7f7;
}

.uim-anchor li.uim-anchor-active {
  background-color: #1997c6;
  border-color: #7cb7f7;
  box-shadow: 0 0 5px 0 #a1e2e2;
  border-top-color: #7cb7f7;
}

.uim-anchor li:last-of-type {
  border-radius: 0 0 12px 12px;
}
</style>
