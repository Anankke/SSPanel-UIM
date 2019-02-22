<template>
  <table class="uim-table">
    <tr class="uim-tr-head">
      <slot name="uim-th"></slot>
    </tr>
    <transition-group name="list-fade" class="relative" tag="tbody">
      <slot name="uim-tbd"></slot>
    </transition-group>
  </table>
</template>

<script>
export default {}
</script>

<style>
.uim-table {
  width: 100%;
  table-layout: fixed;
  text-align: center;
  position: relative;
}
.uim-table tr {
  border: 1px solid #434857;
  border-left: 0;
  border-right: 0;
}
.uim-table th {
  padding: 0.7rem 0;
  font-size: 15px;
}
.uim-table td {
  padding: 0.3rem 0;
  color: #cfd2da;
  font-size: 14px;
  white-space: nowrap;
  overflow-x: hidden;
  text-overflow: ellipsis;
}
</style>
