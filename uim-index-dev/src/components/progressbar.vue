<template>
  <div class="uim-progressbar">
    <div class="uim-progressbar-label">
      <slot name="uim-progressbar-label"></slot>
    </div>
    <div class="uim-progressbar-inner">
      <slot name="progress"></slot>
      <slot name="progress-fold"></slot>
      <div class="uim-progress-text">
        <slot name="progress-text"></slot>
      </div>
    </div>
    <span class="uim-progress-sign">
      <slot name="progress-sign"></slot>
    </span>
  </div>
</template>

<script>
export default {}
</script>

<style>
.uim-progressbar {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
}

.uim-progressbar-inner,
.uim-progressbar-progress,
.uim-progressbar-progress:before {
  display: inline-block;
  height: 8px;
  width: 100%;
  background-color: #434857;
  border-radius: 5px;
  position: relative;
  transition: all 0.3s;
}

.uim-progressbar-inner {
  width: 80%;
}

.uim-progressbar-progress,
.uim-progressbar-progress:before {
  position: absolute;
  left: 0;
  top: 0;
  width: 50%;
  z-index: 3;
}

.uim-progressbar-progress:before {
  content: "";
  opacity: 0;
  width: unset;
  z-index: 4;
  background-color: #fff;
  border-radius: 5px;
  -webkit-animation: uim-progress-active 2s ease-in-out infinite;
  animation: uim-progress-active 2s ease-in-out infinite;
}

.uim-progressbar .uim-progressbar-blue {
  background-color: #2d8cf0;
  box-shadow: 0 0 7px 0 #13c2c2;
}

.uim-progressbar .uim-progressbar-gold {
  background-color: #e8a114;
  box-shadow: 0 0 7px 0 #d4c00c;
}

.uim-progressbar .uim-progressbar-red {
  background-color: #d1335b;
  box-shadow: 0 0 7px 0 rgb(177, 15, 56);
}

.uim-progressbar-progress.uim-progressbar-fold {
  z-index: 2;
}

.uim-progressbar-progress.uim-progressbar-fold.uim-progressbar-red {
  box-shadow: 0 0 7px 1px indianred;
}

.uim-progressbar-sub .uim-progressbar-inner,
.uim-progressbar-sub .uim-progressbar-progress,
.uim-progressbar-sub .uim-progressbar-progress:before {
  height: 5px;
}

.uim-progressbar-label {
  font-size: 12px;
  margin-bottom: 0.2rem;
  width: 100%;
}

.uim-progress-text {
  z-index: 5;
  width: 100%;
  position: relative;
  display: flex;
  justify-content: center;
  font-size: 12px;
  top: -50%;
}

.uim-progressbar-sub .uim-progress-text {
  top: -115%;
}

.uim-progress-sign {
  font-size: 12px;
  width: 20%;
  text-align: center;
}

@keyframes uim-progress-active {
  from {
    opacity: 0.3;
    width: 0;
  }

  to {
    opacity: 0;
    width: 100%;
  }
}
</style>
