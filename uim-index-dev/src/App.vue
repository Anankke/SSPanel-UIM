<template>
  <div id="app">
    <transition name="loading-fade" mode="out-in">
      <div class="loading flex align-center" v-if="isLoading === 'loading'" key="loading">
        <div class="spinner"></div>
      </div>

      <div v-cloak v-else-if="isLoading === 'loaded'" class="flex wrap" key="loaded">
        <div class="nav pure-g">
          <div class="pure-u-1-2 logo-sm flex align-center">
            <a href="/indexold" class="flex align-center">
              <img class="logo" src="/images/logo_white.png" alt="logo">
              <div class="info">
                <div class="name">$[globalConfig.indexMsg.appname]$</div>
                <div class="sign">$[globalConfig.indexMsg.jinrishici]$</div>
              </div>
            </a>
          </div>
          <div class="pure-u-1-2 auth-sm flex align-center">
            <transition name="fade" mode="out-in">
              <router-link v-if="routerN === 'index'" class="button-index" to="/" key="index">
                <span key="toindex">
                  <i class="fa fa-home"></i>
                  <span class="hide-sm">回到首页</span>
                </span>
              </router-link>
              <router-link
                v-else-if="routerN === 'auth'"
                class="button-index"
                to="/auth/login"
                key="auth"
              >
                <span key="toindex">
                  <i class="fa fa-key"></i>
                  <span class="hide-sm">登录/注册</span>
                </span>
              </router-link>
              <router-link v-else to="/user/panel" class="button-index" key="user">
                <i class="fa fa-user"></i>
                <span class="hide-sm">用户中心</span>
              </router-link>
            </transition>
          </div>
        </div>
        <div class="main pure-g">
          <transition :name="transType" mode="out-in">
            <router-view :routermsg="globalConfig.indexMsg"></router-view>
          </transition>
        </div>
        <div class="footer pure-g">
          <div class="pure-u-1 pure-u-sm-1-2 staff">
            POWERED BY
            <a href="./staff">SSPANEL-UIM</a>
          </div>
          <div
            class="pure-u-1 pure-u-sm-1-2 time"
            :class="{ enableCrisp:globalConfig.crisp === 'true' }"
          >&copy;$[globalConfig.indexMsg.date]$ $[globalConfig.indexMsg.appname]$</div>
        </div>

        <transition name="slide-fade" mode="out-in">
          <uim-messager v-show="msgrCon.isShow">
            <i slot="icon" :class="msgrCon.icon"></i>
            <span slot="msg">$[msgrCon.msg]$</span>
            <div v-if="msgrCon.html !== ''" slot="html" v-html="msgrCon.html"></div>
          </uim-messager>
        </transition>
      </div>
    </transition>
  </div>
</template>

<script>
/**
 * A wrapper of window.Fetch API
 * @author Sukka (https://skk.moe)

/**
 * A Request Helper of Fetch
 * @function _request
 * @param {string} url
 * @param {string} body
 * @param {string} method
 * @returns {function} - A Promise Object
 */
const _request = (url, body, method,credentials) => 
    fetch(url, {
        method: method,
        body: body,
        headers: {
            'content-type': 'application/json'
        },
        credentials: credentials,
    }).then(resp => {
        return Promise.all([resp.ok, resp.status, resp.json()]);
    }).then(([ok, status, json]) => {
        if (ok) {
            return json;
        } else {
            throw new Error(JSON.stringify(json.error));
        }
    }).catch(error => {
        throw error;
    });

    

/**
 * A Wrapper of Fetch GET Method
 * @function _get
 * @param {string} url
 * @returns {function} - A Promise Object
 * @example
 * get('https://example.com').then(resp => { console.log(resp) })
 */
const _get = (url,credentials) => 
    fetch(url, {
        method: 'GET',
        credentials,
    }).then(resp => {
        return Promise.all([resp.ok, resp.status, resp.json(), resp.headers])
    })
    .then(([ok, status, json, headers]) => {
        if (ok) {
            return json;
        } else {
            throw new Error(JSON.stringify(json.error));
        }
    }).catch(error => {
        console.log(error);
        throw error; 
    });

    
/**
 * A Wrapper of Fetch POST Method
 * @function _post
 * @param {string} url
 * @param {string} json - The POST Body in JSON Format
 * @returns {function} - A Promise Object
 * @example
 * _post('https://example.com', JSON.stringify(data)).then(resp => { console.log(resp) })
 */

const _post = (url, body, credentials) => _request(url, body, 'POST', credentials);

let validate,captcha;

let globalConfig;
</script>


<style>
.slide-fade-enter-active,
.fade-enter-active,
.loading-fade-enter-active,
.rotate-fade-enter-active,
.loading-fadex-enter-active {
  transition: all 0.3s ease;
}
.slide-fade-leave-active,
.fade-leave-active,
.loading-fade-leave-active,
.rotate-fade-leave-active,
.loading-fadex-leave-active {
  transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
}
.loading-fade-enter {
  transform: scaleY(0.75);
  opacity: 0;
}
.loading-fadex-enter {
  transform: scaleX(0.75);
  opacity: 0;
}
.slide-fade-enter {
  transform: translateY(-20px);
  opacity: 0;
}
.rotate-fade-enter {
  transform: rotateY(90deg);
  -webkit-transform: rotateY(90deg);
  opacity: 0;
}
.slide-fade-leave-to {
  transform: translateY(20px);
  opacity: 0;
}
.rotate-fade-leave-to {
  transform: rotateY(90deg);
  -webkit-transform: rotateY(90deg);
  opacity: 0;
}
.fade-enter,
.fade-leave-to,
.loading-fade-leave-to,
.loading-fadex-leave-to {
  opacity: 0;
}
</style>
