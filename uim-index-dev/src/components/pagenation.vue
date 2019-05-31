<template>
  <div class="uim-pagenation">
    <button @click="setCurrentPage(prev)" class="uim-pagination-btn">
      <font-awesome-icon icon="angle-left"/>
    </button>
    <button
      v-for="item in buttonList"
      @click="setCurrentPage(item.num)"
      class="uim-pagination-btn"
      :class="{ 'uim-pagination-btn-active':item.isActive }"
      :key="item"
    >{{item.num}}</button>
    <button @click="setCurrentPage(next)" class="uim-pagination-btn">
      <font-awesome-icon icon="angle-right"/>
    </button>
  </div>
</template>

<script>
export default {
  name: "uim-pagenation",
  props: ["pageinfo"],
  computed: {
    prev: function() {
      return this.currentPage - 1 > 0 ? this.currentPage - 1 : 1;
    },
    next: function() {
      return this.currentPage + 1 <= this.pageinfo.lastPage
        ? this.currentPage + 1
        : this.pageinfo.lastPage;
    }
  },
  data: function() {
    return {
      buttonList: "",
      currentPage: 1,
      prevPage: 1
    };
  },
  methods: {
    getButtonList() {
      let lastPage = this.pageinfo.lastPage ? this.pageinfo.lastPage : 1;
      let currentPage = this.currentPage;
      if (lastPage <= 6) {
        let arr = new Array(lastPage);
        this.buttonList = Array.from(arr).map((value, index) => {
          let obj = {
            num: index + 1,
            isActive: false
          };
          if (this.currentPage === obj.num) {
            obj.isActive = true;
          }
          return obj;
        });
      } else {
        let promise = new Promise((resolve, reject) => {
          if (currentPage < 6) {
            this.buttonList = [1, 2, 3, 4, 5, 6, "···", lastPage];
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
            ];
          } else if (currentPage > lastPage - 5) {
            let arr = new Array(6);
            arr = Array.from(arr).map((value, index) => lastPage - 5 + index);
            this.buttonList = [1, "···"].concat(arr);
          }
          resolve("done");
        });
        promise.then(r => {
          window.console.log(r);
          this.buttonList = this.buttonList.map(value => {
            let obj = {
              num: value,
              isActive: false
            };
            if (this.currentPage === obj.num) {
              obj.isActive = true;
            }
            return obj;
          });
        });
      }
    },
    setCurrentPage(num) {
      if (num !== "···") {
        this.currentPage = num || 1;
        this.getButtonList();
        this.$emit("turnPage", this.currentPage);
      }
    }
  },
  mounted() {
    this.currentPage = this.pageinfo.currentPage;
    this.setCurrentPage(this.pageinfo.currentPage);
  }
};
</script>

<style>
.uim-pagenation-container {
  margin-top: 0.75rem;
}
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
