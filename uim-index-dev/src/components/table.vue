<template>
    <table class="uim-table">
        <tr class="uim-tr-head">
            <slot name="uim-th"></slot>
        </tr>
        <slot name="uim-tr"></slot>
    </table>
</template>

<script>
export default {
    
}
</script>

<style>
.uim-table {
    width: 100%;
    table-layout: fixed;
    text-align: center;
}
.uim-table tr {
    border: 1px solid #434857;
    border-left: 0;
    border-right: 0;
}
.uim-table th {
    padding: .7rem 0;
    font-size: 15px;
}
.uim-table td {
    padding: .3rem 0;
    color: #cfd2da;
    font-size: 14px;
}
</style>

