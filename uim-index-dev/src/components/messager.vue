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
export default {};
</script>

<style>
.uim-messager {
  position: fixed;
  top: 6rem;
  left: 0;
  right: 0;
  margin-right: auto;
  margin-left: auto;
  display: inline-block;
  text-align: center;
  border-radius: 3px;
  z-index: 10;
  padding: 7px 10px;
  color: black;
  background: white;
  width: 200px;
  min-height: 36px;
  box-shadow: 0px 0px 5px 1px gray;
}

.uim-messager span {
  margin-left: 0.6rem;
}

.uim-messager a {
  color: #d1335b;
}
</style>
