<template>
  <div class="uim-pagenation">
    <button @click="setCurrentPage(currentPage-1)" class="uim-pagination-btn">
      <span class="fa fa-angle-left"></span>
    </button>
    <button
      v-for="(item,index) in buttonList"
      @click="setCurrentPage(item.num)"
      class="uim-pagination-btn"
      :class="{ 'uim-pagination-btn-active':item.isActive }"
      :key="item"
    >{{item.num}}</button>
    <button @click="setCurrentPage(currentPage+1)" class="uim-pagination-btn">
      <span class="fa fa-angle-right"></span>
    </button>
  </div>
</template>

<script>
export default {
  props: ["pageinfo"],
  data: function() {
    return {
      buttonList: "",
      currentPage: 1,
      prevPage: 1
    };
  },
  methods: {
    getButtonList() {
      let lastPage = this.pageinfo.lastPage;
      let currentPage = this.currentPage;
      if (lastPage <= 6) {
        let arr = new Array(last_page);
        this.buttonList = Array.from(arr).map((value, index) => {
          return {
            num: index + 1,
            isActive: false
          };
        });
      } else {
        if (currentPage < 6) {
          this.buttonList = [1, 2, 3, 4, 5, 6, "···", lastPage].map(value => {
            return {
              num: value,
              isActive: false
            };
          });
        } else if (currentPage >= 6 && currentPage <= lastPage - 5) {
          this.buttonList = [
            1,
            "···",
            currentPage - 2,
            currentPage - 1,
            currentPage,
            currentPage + 1,
            currentPage + 2,
            "···",
            lastPage
          ].map(value => {
            return {
              num: value,
              isActive: false
            };
          });
        } else if (currentPage > lastPage - 5) {
          let arr = new Array(6);
          arr = Array.from(arr).map((value, index) => lastPage - 5 + index);
          this.buttonList = [1, "···"].concat(arr).map(value => {
            return {
              num: value,
              isActive: false
            };
          });
        }
      }
    },
    setCurrentPage(num) {
      if (num !== "···") {
        this.currentPage = num;
        this.getButtonList();
        for (let i = 0; i <= this.buttonList.length - 1; i++) {
          let item = this.buttonList[i];
          if (item.num === this.currentPage) {
            item.isActive = true;
          }
        }
        this.$emit("turnPage", this.currentPage);
      }
    }
  },
  mounted() {
    this.getButtonList();
    this.buttonList[0].isActive = true;
  }
};
</script>

<style>
button.uim-pagination-btn {
  color: white;
  background: transparent;
  border: none;
  outline: none;
  margin-right: 0.3rem;
  border-radius: 4px;
  min-width: 24px;
  transition: all 0.3s;
}
button.uim-pagination-btn:hover {
  color: black;
  background: white;
}
button.uim-pagination-btn-active {
  color: black;
  background: white;
}
</style>
