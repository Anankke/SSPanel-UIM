<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <title>{$config["appName"]}</title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="bookmark" href="/favicon.ico"/>
    <link rel="stylesheet" href="/theme/material/css/index_base.css">
    <link rel="stylesheet" href="/theme/material/css/index.css">
    {if $config["enable_crisp"] == 'true'}
    <literal><script type="text/javascript"> 
    window.$crisp=[];
    window.CRISP_WEBSITE_ID="{$config["crisp_id"]}";
    $crisp.push(["set", "user:email", "{$user->email}"]);
    $crisp.push(["config", "color:theme", "grey"]);
    (function(){ d=document;s=d.createElement("script"); s.src="https://client.crisp.chat/l.js"; s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
    </script></literal>
    {/if}
</head>

<style>
.slide-fade-enter-active,.fade-enter-active,.loading-fade-enter-active,.rotate-fade-enter-active,.loading-fadex-enter-active {
    transition: all .3s ease;
}
.slide-fade-leave-active,.fade-leave-active,.loading-fade-leave-active,.rotate-fade-leave-active,.loading-fadex-leave-active {
    transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.loading-fade-enter {
    transform: scaleY(.75);
    opacity: 0;
}
.loading-fadex-enter {
    transform: scaleX(.75);
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
.fade-enter,.fade-leave-to,.loading-fade-leave-to,.loading-fadex-leave-to {
    opacity: 0;
}
</style>

<body>
    {*<script src="/assets/js/fuck.js"></script>*}
    <div id="index" >
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
                            <span key="toindex"><i class="fa fa-home"></i> <span class="hide-sm">回到首页</span></span>
                        </router-link>
                        <router-link v-else-if="routerN === 'auth'" class="button-index" to="/auth/login" key="auth">
                            <span key="toindex"><i class="fa fa-key"></i> <span class="hide-sm">登录/注册</span></span>
                        </router-link>
                        <router-link v-else to="/user/panel" class="button-index" key="user"><i class="fa fa-user"></i> <span class="hide-sm">用户中心</span></router-link>
                        </transition>
                    </div>
                </div>
                <div class="main pure-g">
                    <transition :name="transType" mode="out-in">
                    <router-view :routermsg="globalConfig.indexMsg"></router-view>
                    </transition>
                </div>
                <div class="footer pure-g">
                    <div class="pure-u-1 pure-u-sm-1-2 staff">POWERED BY <a href="./staff">SSPANEL-UIM</a></div>
                    <div class="pure-u-1 pure-u-sm-1-2 time" :class="{ enableCrisp:globalConfig.crisp === 'true' }">&copy;$[globalConfig.indexMsg.date]$ $[globalConfig.indexMsg.appname]$</div>
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
       
    {if $recaptcha_sitekey != null}
    <script src="https://recaptcha.net/recaptcha/api.js?render=explicit" async defer></script>
    {/if}
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.21"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuex@3.0.1/dist/vuex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-router@3.0.2"></script>
    {if isset($geetest_html)}
	<script src="//static.geetest.com/static/tools/gt.js"></script>
    {/if}
    {if $config['enable_telegram'] == 'true'}
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    {/if}
    <script>
        ;(function(){
            if (!window.Vuex) {
                window.location = '/indexold';
            }
        })();      
    </script> 
</body>

</html>

<script>
{*/**
 * A wrapper of window.Fetch API
 * @author Sukka (https://skk.moe)

/**
 * A Request Helper of Fetch
 * @function _request
 * @param {string} url
 * @param {string} body
 * @param {string} method
 * @returns {function} - A Promise Object
 */*}
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

    

{*/**
 * A Wrapper of Fetch GET Method
 * @function _get
 * @param {string} url
 * @returns {function} - A Promise Object
 * @example
 * get('https://example.com').then(resp => { console.log(resp) })
 */*}
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

    
{*/**
 * A Wrapper of Fetch POST Method
 * @function _post
 * @param {string} url
 * @param {string} json - The POST Body in JSON Format
 * @returns {function} - A Promise Object
 * @example
 * _post('https://example.com', JSON.stringify(data)).then(resp => { console.log(resp) })
 */*}

const _post = (url, body, credentials) => _request(url, body, 'POST', credentials);

let validate,captcha;

let globalConfig;

const UserTmp = {
    state: {
        userCon: '',
        userSettings: {
            pages: [
                {
                    id: 'user-resourse',
                },
                {
                    id: 'user-settings',
                },
            ],
            currentPage: 'user-resourse',
            currentPageIndex: 0,
            resourse: [
                {
                    name: '等级有效期',
                    content: '',
                },
                {
                    name: '账号有效期',
                    content: '',
                },
                {
                    name: '在线设备数',
                    content: '',
                },
                {
                    name: '端口速率',
                    content: '',
                },
            ],
            tipsLink: [
                {
                    name: '端口',
                    content: '',
                },
                {
                    name: '密码',
                    content: '',
                },
                {
                    name: '加密',
                    content: '',
                },
                {
                    name: '协议',
                    content: '',
                },
                {
                    name: '混淆',
                    content: '',
                },
                {
                    name: '混淆参数',
                    content: '',
                },
            ],
        },
    },
    mutations: {
        SET_USERCON (state,config) {
            state.userCon = config;
        },
        SET_USERMONEY (state,number) {
            state.userCon.money = number;
        },
        SET_INVITE_NUM (state,number) {
            state.userCon.invite_num = number;
        },
        SET_USERSETTINGS (state,config) {
            state.userSettings.tipsLink[0].content = config.port;
            state.userSettings.tipsLink[1].content = config.passwd;
            state.userSettings.tipsLink[2].content = config.method;
            state.userSettings.tipsLink[3].content = config.protocol;
            state.userSettings.tipsLink[4].content = config.obfs;
            state.userSettings.tipsLink[5].content = config.obfs_param;
        },
        ADD_NEWUSERCON (state,config) {
            for (let key in config) {
                Vue.set(state.userCon,key,config[key]);
            }
        },
        SET_RESOURSE (state,config) {
            state.userSettings.resourse[config.index].content = config.content;
        },
        SET_ALLURESOURSE (state,config) {
            for (let key in config) {
                state.userCon[key] = config[key];
            }
        }
    },
    actions: {
        
    },
}

const tmp = new Vuex.Store({
    state: {
        isLoading: 'loading',
        wait: 60,
        logintoken: false,
        msgrCon: {
            msg: '操作成功',
            html: '',
            icon: ['fa','fa-check-square-o'],
            isShow: false,
        },
        modalCon: {
            isMaskShow: false,
            isCardShow: false,
            title: '订单确认',
            bodyContent: '',
        },
        globalConfig: {
            captchaProvider: '',
            recaptchaSiteKey: '',
            jumpDelay: '',
            isGetestSuccess: '',
            registMode: '',
            crisp: '',
            base_url: '',
            isEmailVeryify: '',
            login_token: '',
            login_number: '',
            telegram_bot: '',
            enable_telegram: '',
            enableLoginCaptcha: '',
            enableRegCaptcha: '',
            indexMsg: {
                appname: '',
                hitokoto: '',
                date: '',
                jinrishici: '',
            },
        },   
    },
    mutations: {
        SET_LOADSTATE (state) {
            state.isLoading = 'loaded';
        },
        SET_LOGINTOKEN (state,n) {
            state.logintoken = n;
        },
        SET_MSGRCON (state,config) {
            if (state.msgrCon.html !== '') {
                state.msgrCon.html = '';
            }
            state.msgrCon.msg = config.msg;
            state.msgrCon.icon[1] = config.icon;
            state.msgrCon.html = config.html;
        },
        ISSHOW_MSGR (state,boolean) {
            state.msgrCon.isShow = boolean;
        },
        ISSHOW_MODAL_MASK (state,boolean) {
            state.modalCon.isMaskShow = boolean;
        },
        ISSHOW_MODAL_CARD (state,boolean) {
            state.modalCon.isCardShow = boolean;
        },
        SET_GLOBALCONFIG (state,config) {
            state.logintoken = config.isLogin
            state.globalConfig.base_url = config.base_url;
            state.globalConfig.captchaProvider = config.captcha_provider;
            state.globalConfig.recaptchaSiteKey = config.recaptcha_sitekey;
            state.globalConfig.jumpDelay = config.jump_delay;
            state.globalConfig.isGetestSuccess = config.isGetestSuccess;
            state.globalConfig.registMode = config.register_mode;
            state.globalConfig.isEmailVeryify = config.enable_email_verify;
            state.globalConfig.enableLoginCaptcha = config.enable_logincaptcha;
            state.globalConfig.enableRegCaptcha = config.enable_regcaptcha;
            state.globalConfig.login_token = config.login_token;
            state.globalConfig.login_number = config.login_number;
            state.globalConfig.telegram_bot = config.telegram_bot;
            state.globalConfig.crisp = config.enable_crisp;
            state.globalConfig.enable_telegram = config.enable_telegram;
            state.globalConfig.indexMsg.appname = config.appName;
            state.globalConfig.indexMsg.date = config.dateY;
        },
        SET_HITOKOTO (state,content) {
            state.globalConfig.indexMsg.hitokoto = content;
        },
        SET_JINRISHICI (state,content) {
            state.globalConfig.indexMsg.jinrishici = content;
        },
    },
    actions: {
        CALL_MSGR ({ dispatch,commit,state },config) {
            if (state.msgrCon.isShow === true) {
                commit('ISSHOW_MSGR',false);
                setTimeout(() => {
                    dispatch('CALL_MSGR',config);                    
                }, 300);
            } else {
                commit('SET_MSGRCON',config);
                commit('ISSHOW_MSGR',true);
                window.setTimeout(function() {
                    commit('ISSHOW_MSGR',false);
                },config.time)
            }
        },
        CALL_MODAL ({ commit,state },config) {
            if (state.modalCon.isMaskShow === false) {
                commit('ISSHOW_MODAL_MASK',true);
                window.setTimeout(() => {
                    commit('ISSHOW_MODAL_CARD',true)
                }, 300);
            } else {
                commit('ISSHOW_MODAL_CARD',false);
                window.setTimeout(() => {
                    commit('ISSHOW_MODAL_MASK',false)
                }, 300);
            }
        },
    },
    modules: {
        userState: UserTmp,
    }
});

Vue.directive('uimclip',{
    inserted: function(el, binding) {
        el.addEventListener('click',(e)=>{
            let copy = new Promise((resolve,reject)=>{
                let input = document.createElement('input');
                let body = document.getElementsByTagName('body')[0];
                let value = el.dataset.uimclip;
                input.setAttribute('type','text');
                input.setAttribute('value',value);
                body.appendChild(input);
                input.focus();
                input.setSelectionRange(0, value.length);
                document.execCommand('copy',true);
                resolve(input);
            })
            copy.then((r)=>{
                r.remove();
                binding.value.onSuccess();
            })
        })
    },
})

var methodsMixin = {
    methods: {
        successCopied() {
            let callConfig = {
                msg: '复制成功！,已将链接复制到剪贴板',
                icon: 'fa-check-square-o',
                time: '1500',
            }
            this.callMsgr(callConfig);
        },
    }
}

var mutationMap = {
    methods: Vuex.mapMutations({
        setGlobalConfig: 'SET_GLOBALCONFIG',
        setHitokoto: 'SET_HITOKOTO',
        setJinRiShiCi: 'SET_JINRISHICI',
        setLoadState: 'SET_LOADSTATE',
        setLoginToken: 'SET_LOGINTOKEN',
        setUserMoney: 'SET_USERMONEY',
        setInviteNum: 'SET_INVITE_NUM',
        setReasourse: 'SET_RESOURSE',
        setUserCon: 'SET_USERCON',
        setUserSettings: 'SET_USERSETTINGS',
        addNewUserCon: 'ADD_NEWUSERCON',
        setAllResourse: 'SET_ALLURESOURSE',
    }),
}

var storeMap = {
    store: tmp,
    mixins: [mutationMap,methodsMixin],
    computed: Vuex.mapState({
        msgrCon: 'msgrCon',
        modalCon: 'modalCon',
        globalConfig: 'globalConfig',
        logintoken: 'logintoken',
        isLoading: 'isLoading',
        userCon: state => state.userState.userCon,
        userSettings: state => state.userState.userSettings,
    }),
    methods: Vuex.mapActions({
        callMsgr: 'CALL_MSGR',
        callModal: 'CALL_MODAL',
    }),
    
}

var storeAuth = {
    methods: {
        loadCaptcha(id) {
            if (this.globalConfig.recaptchaSiteKey !== null ) {
                this.$nextTick(function(){
                    this.grecaptchaRender(id);                    
                })
            }
        },
        loadGT(id) {
            if (this.globalConfig.captchaProvider === 'geetest') {
                this.$nextTick(function(){

                _get('/auth/login_getCaptcha','include')
                    .then((r) => {
                        let GeConfig = {
                            gt: r.GtSdk.gt,
                            challenge: r.GtSdk.challenge,
                            product: "embed",
                        }

                        if (parseInt(this.globalConfig.isGetestSuccess)) {
                            GeConfig.offline = 0;
                        } else {
                            GeConfig.offline = 1;
                        }
                        
                        initGeetest(GeConfig, function(captchaObj) {
                            captchaObj.appendTo(id);
                            captchaObj.onSuccess(function () {
                                validate = captchaObj.getValidate();
                            });
                            captcha = captchaObj;
                        });

                    });

                });
            }
        },
        //加载完成的时间很谜
        grecaptchaRender(id) {
            setTimeout(() => {
                if (!grecaptcha || !grecaptcha.render) {
                    this.grecaptchaRender(id);
                } else {
                    grecaptcha.render(id);
                }
            }, 300)
        }
    },
};

const Root = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="title pure-g">
        <div class="pure-u-1 pure-u-xl-1-2 title-left">
            <h1>$[routermsg.appname]$</h1>
            <span>$[routermsg.hitokoto]$</span>
            <router-link class="button-index" to="/auth/login">登录</router-link>
            <router-link class="button-index" to="/auth/register">注册</router-link>
            <router-link class="button-index" to="/user/panel">用户中心</router-link>
        </div>
        <div class="pure-u-xl-1-2 logo-bg">
            <img src="/images/logo_white.png" alt="" class="logo">
        </div>
    </div>
    `,
    props: ['routermsg'],
};

const Auth = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="auth pure-g align-center">
        <div class="pure-u-1 pure-u-sm-4-24 flex wrap space-around auth-links">
            <router-link v-for="(links,key) in routerLinks" @click.native="setButtonState" :class="{ active:links.isActive }" class="button-round flex align-center" :to="links.href" :key="links.id">
                <span class="fa-stack"><i class="fa fa-circle fa-stack-2x"></i><i :class="links.icon"></i></span><span>$[links.content]$</span> 
            </router-link>
        </div>
        <transition name="slide-fade" mode="out-in">
        <router-view></router-view>
        </transition>
    </div>
    `,
    props: ['routermsg'],
    data: function() {
        return {
            routerLinks: {
                login: {
                    id: 'R_AUTH_0',
                    href: '/auth/login',
                    content: '登录',
                    icon: ['fa','fa-sign-in','fa-stack-1x','fa-inverse'],
                    isActive: false,
                },
                register: {
                    id: 'R_AUTH_1',
                    href: '/auth/register',
                    content: '注册',
                    icon: ['fa','fa-user-plus','fa-stack-1x','fa-inverse'],
                    isActive: false,
                },
                reset: {
                    id: 'R_PW_0',
                    href: '/password/reset',
                    content: '密码重置',
                    icon: ['fa','fa-unlock-alt','fa-stack-1x','fa-inverse'],
                    isActive: false,
                },
            },
        }
    },
    methods: {
        setButtonState() {
            for (let key in this.routerLinks) {
                if (this.$route.path == this.routerLinks[key].href) {
                    this.routerLinks[key].isActive = true;
                } else {
                    this.routerLinks[key].isActive = false;
                }
            }
        },
    },
    watch: {
        $route: 'setButtonState',
    },
    beforeRouteEnter (to,from,next) {
        next(vm=>{
            vm.setButtonState();
        });
    },
    beforeRouteLeave (to,from,next) {
        this.setButtonState();
        next();
    }
};

const Login = {
    delimiters: ['$[',']$'],
    mixins: [storeMap,storeAuth],
    computed: Vuex.mapState({
        telegramHref: function() {
            return 'https://t.me/' + this.globalConfig.telegram_bot;
        },
        isTgEnabled: function() {
            return this.globalConfig.enable_telegram === 'true';
        }
    }),
    template: /*html*/ `
    <div class="page-auth pure-g pure-u-1 pure-u-sm-20-24 wrap">
        <div class="title-back flex align-center">LOGIN</div>
        <h1>登录</h1>
        <div class="pure-u-1 basis-max" :class="[ isTgEnabled ? 'pure-u-sm-11-24' : 'pure-u-sm-1-2' ]">
            <div class="input-control flex wrap">
                <label for="Email">邮箱</label>
                <input v-model="email" type="text" name="Email">        
            </div>
            <div class="input-control flex wrap">
                <label for="Password">密码</label>
                <input v-model="passwd" type="password" name="Password">        
            </div>
            <div class="input-control flex wrap">
                <uim-checkbox v-model="remember_me">
                    <span slot="content">记住我</span>
                </uim-checkbox>
            </div>
            <div class="input-control flex wrap">
                <div v-if="globalConfig.captchaProvider === 'geetest'" id="embed-captcha-login"></div>
                <form action="?" method="POST">    
                <div v-if="globalConfig.recaptchaSiteKey" id="g-recaptcha-login" class="g-recaptcha" data-theme="dark" :data-sitekey="globalConfig.recaptchaSiteKey"></div>
                </form>
            </div>
            <button @click.prevent="login" @keyup.13.native="login" class="auth-submit" id="login" type="submit" :disabled="isDisabled">
                确认登录
            </button>
        </div>
        <div v-if="globalConfig.enable_telegram === 'true'" class="pure-u-1 pure-u-sm-11-24 pure-g auth-tg">
            <h3>Telegram登录</h3>
            <div>
                <p>Telegram OAuth一键登陆</p>
            </div>
            <p id="telegram-alert">正在载入 Telegram，如果长时间未显示请刷新页面或检查代理</p>
            <div class="text-center" id="telegram-login-box"></div>
            <p>或者添加机器人账号 <a :href="telegramHref">@$[globalConfig.telegram_bot]$</a>，发送下面的数字/二维码验证码给它
            </p>
            <transition name="fade" mode="out-in">
            <div v-if="!isTgtimeout" class="pure-g pure-u-20-24" key="notTimeout">
                <div class="text-center qr-center pure-u-11-24">
                    <div id="telegram-qr" class="flex space-around"></div>
                </div>
                <div class="pure-u-11-24">
                    <div class="auth-submit" id="code_number">$[globalConfig.login_number]$</div>
                </div>
            </div>
            <div v-else class="pure-g space-around" key="timeout">
                <div class="auth-submit pure-u-18-24 tg-timeout">验证方式已过期，请刷新页面后重试</div>
            </div>
            </transition>
        </div>  
    </div>
    `,
    data: function () {
        return {
            email: '',
            passwd: '',
            remember_me: false,
            isDisabled: false,
            isTgtimeout: false,
        }
    },
    methods: {
        login() {
           
            this.isDisabled = true;

            let ajaxCon = {
                email: this.email,
                passwd: this.passwd,
                remember_me: this.remember_me,
            };

            let callConfig = {
                msg: '',
                icon: '',
                time: 1000,
            };

            if (this.globalConfig.enableLoginCaptcha !== 'false') {
                switch(this.globalConfig.captchaProvider) {
                    case 'recaptcha':
                        ajaxCon.recaptcha = grecaptcha.getResponse();
                        break;
                    case 'geetest':
                        if (validate) {
                            ajaxCon.geetest_challenge = validate.geetest_challenge;
                            ajaxCon.geetest_validate = validate.geetest_validate;
                            ajaxCon.geetest_seccode = validate.geetest_seccode;
                        } else {
                            callConfig.msg += '请滑动验证码来完成验证。'
                        }
                        break;
                }
            }

            _post('/auth/login', JSON.stringify(ajaxCon),'include').then((r) => {
                if (r.ret === 1) {
                    callConfig.msg += '登录成功Kira~';
                    callConfig.icon += 'fa-check-square-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(() => {
                        this.setLoginToken(1);
                        this.$router.replace('/user/panel');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg = `登录失败Boommm,${ r.msg }`;
                    callConfig.icon += 'fa-times-circle-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(()=>{
                        this.isDisabled = false;
                    }, 3000)
                }
            });

        },
        telegramRender() {
            let el = document.createElement('script');
            document.getElementById('telegram-login-box').append(el);
            el.onload = function () {
                document.getElementById('telegram-alert').outerHTML = '';
            }
            el.src = 'https://telegram.org/js/telegram-widget.js?4';
            el.setAttribute('data-size', 'large');
            el.setAttribute('data-telegram-login', this.globalConfig.telegram_bot);
            el.setAttribute('data-auth-url', this.globalConfig.base_url + '/auth/telegram_oauth');
            el.setAttribute('data-request-access', 'write');

            let telegram_qrcode = 'mod://login/' + this.globalConfig.login_token;
            let qrcode = new QRCode(document.getElementById("telegram-qr"));
            qrcode.clear();
            qrcode.makeCode(telegram_qrcode);
        },
        tgAuthTrigger(tid) {
            if (this.logintoken === 1) {
                return;
            }
            let callConfig = {
                msg: '',
                icon: '',
                time: 1000,
            };
            _post('/auth/qrcode_check', JSON.stringify({
                token: this.globalConfig.login_token,
                number: this.globalConfig.login_number,
            }),'include').then((r) => {
                if(r.ret > 0) {
                    clearTimeout(tid);
                    
                    _post('/auth/qrcode_login',JSON.stringify({
                        token: this.globalConfig.login_token,
                        number: this.globalConfig.login_number,
                    }),'include').then(r=>{
                        if (r.ret) {
                            callConfig.msg += '登录成功Kira~';
                            callConfig.icon += 'fa-check-square-o';
                            this.callMsgr(callConfig);
                            window.setTimeout(()=>{
                                this.setLoginToken(1);
                                this.$router.replace('/user/panel');
                            }, this.globalConfig.jumpDelay);
                        }
                    })
                } else if (r.ret == -1) {
                    this.isTgtimeout = true;
                }
            });
            tid = setTimeout(()=>{
                this.tgAuthTrigger(tid);
            }, 2500);
        },
        loginBindEnter(e) {
            if (this.$route.path === '/auth/login' && e.keyCode == 13) {
                this.login();
            }
        },
    },
    mounted() {
        document.addEventListener('keyup',this.loginBindEnter,false);

        if (this.globalConfig.enable_telegram === 'true') {
            this.telegramRender();
            let tid = setTimeout(() => {
                this.tgAuthTrigger(tid);
            }, 2500);
        }

        if (this.globalConfig.enableLoginCaptcha === 'false') {
            return;
        }
        this.loadCaptcha('g-recaptcha-login');
        this.loadGT('#embed-captcha-login');
    },
    beforeRouteLeave(to, from , next) {
        document.removeEventListener('keyup',this.loginBindEnter,false);
        next();
    },
};

const Register = {
    delimiters: ['$[',']$'],
    mixins: [storeMap,storeAuth],
    template: /*html*/ `
    <div class="page-auth pure-g pure-u-1 pure-u-sm-20-24">
        <div class="title-back flex align-center">REGISTER</div>
        <h1>账号注册</h1>
        <div class="flex space-around reg">
            <div class="input-control flex wrap">
                <label for="usrname">昵称</label>
                <input v-model="usrname" type="text" name="usrname">        
            </div>
            <div class="input-control flex wrap">
                <label for="email">邮箱(唯一凭证请认真对待)</label>
                <input v-model="email" type="text" name="email">        
            </div>
            <div class="input-control flex wrap">
                <label for="password">密码</label>
                <input v-model="passwd" type="password" name="password">        
            </div>
            <div class="input-control flex wrap">
                <label for="repasswd">重复密码</label>
                <input v-model="repasswd" type="password" name="repasswd">        
            </div>
            <div class="input-control flex wrap">
                <label for="imtype">选择您的联络方式</label>
                <select v-model="imtype" name="imtype" id="imtype">
                    <option value="1">微信</option>
                    <option value="2">QQ</option>
                    <option value="3">Facebook</option>
                    <option value="4">Telegram</option>
                </select>        
            </div>
            <div class="input-control flex wrap">
                <label for="contect">联络方式账号</label>
                <input v-model="contect" type="text" name="contect">        
            </div>
            <div v-if="globalConfig.registMode === 'invite'" class="input-control flex">
                <label for="code">邀请码(必填)</label>
                <input v-model="code" type="text" name="code">        
            </div>
            <div v-if="globalConfig.isEmailVeryify === 'true'" class="input-control flex twin">
                <div class="input-control-inner flex">
                    <label for="email_code">邮箱验证码</label>
                    <input v-model="email_code" type="text" name="email_code"></input>
                </div>
                
                <button class="auth-submit" @click="sendVerifyMail" :disabled="isVmDisabled">$[vmText]$</button>    
            </div>
            <div class="input-control wrap flex align-center">
            <div v-if="globalConfig.captchaProvider === 'geetest'" id="embed-captcha-reg"></div>
                <form action="?" method="POST">    
                <div v-if="globalConfig.recaptchaSiteKey" id="g-recaptcha-reg" class="g-recaptcha" data-theme="dark" :data-sitekey="globalConfig.recaptchaSiteKey"></div>
                </form>
            </div>
        </div>
        
        <button @click="register" class="auth-submit" id="register" type="submit" :disabled="isDisabled">
            确认注册
        </button>
    </div>
    `,
    data: function() {
        return {
            usrname: '',
            email: '',
            passwd: '',
            repasswd: '',
            contect: '',
            code: '',
            imtype: '',
            email_code: '',
            isDisabled: false,
            vmText: '获取邮箱验证码',
            isVmDisabled: false,
        }
    },
    methods: {
        register() {

            this.isDisabled = true;

            let ajaxCon = {
                    email: this.email,
                    name: this.usrname,
                    passwd: this.passwd,
                    repasswd: this.repasswd,
                    wechat: this.contect,
                    imtype: this.imtype,
                    code: this.code,
                };

            let callConfig = {
                msg: '',
                icon: '',
                time: 1000,
            };

            if (this.globalConfig.isEmailVeryify === 'true') {
                ajaxCon.emailcode = this.email_code;
            }

            if (this.globalConfig.registMode !== 'invite') {
                ajaxCon.code = 0;
                if ((this.getCookie('code')) !== '') {
                    ajaxCon.code = this.getCookie('code');
                }
            }

            if (this.globalConfig.enableRegCaptcha !== 'false') {
                switch(this.globalConfig.captchaProvider) {
                    case 'recaptcha':
                        ajaxCon.recaptcha = grecaptcha.getResponse();
                        break;
                    case 'geetest':
                        if (validate) {
                            ajaxCon.geetest_challenge = validate.geetest_challenge;
                            ajaxCon.geetest_validate = validate.geetest_validate;
                            ajaxCon.geetest_seccode = validate.geetest_seccode;
                        } else {
                            callConfig.msg += '请滑动验证码来完成验证。'
                        }      
                        break;
                }
            }

            _post('/auth/register', JSON.stringify(ajaxCon),'include').then((r)=>{
                if (r.ret == 1) {
                    callConfig.msg = '注册成功meow~';
                    callConfig.icon = 'fa-check-square-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(()=>{
                        this.$router.replace('/auth/login');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg = `WTF……注册失败,${ r.msg }`;
                    callConfig.icon += 'fa-times-circle-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(()=>{
                        this.isDisabled = false;
                    },3000)
                }
            });
        },
        //dumplin：轮子1.js读取url参数//nymph: 重拼字符串
        getQueryVariable(variable) {
            var query = window.location.hash.substring(1).split("?")[1];
            if (typeof query === 'undefined') {
                return "";
            }
            var vars = query.split("&");
            for (var i=0;i<vars.length;i++) {
                        var pair = vars[i].split("=");
                        if(pair[0] == variable){
                            return pair[1];
                        }
            }
            return "";
        },
        //dumplin:轮子2.js写入cookie
        setCookie(cname,cvalue,exdays) {
            var d = new Date();
            d.setTime(d.getTime()+(exdays*24*60*60*1000));
            var expires = "expires="+d.toGMTString();
            document.cookie = cname + "=" + cvalue + "; " + expires;
        },
        //dumplin:轮子3.js读取cookie
        getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i=0; i<ca.length; i++) 
            {
                var c = ca[i].trim();
                if (c.indexOf(name)==0) return c.substring(name.length,c.length);
            }
            return "";
        },
        time(time) {
            if (time == 0) {
                this.isVmDisabled = false;
                this.vmText = "获取验证码";
                time = 60;
            } else {
                this.isVmDisabled = true;
                this.vmText = '重新发送(' +  time + ')';
                time = time -1;
                setTimeout(()=> {
                    this.time(time);
                },
                1000);
            }
        },
        sendVerifyMail() {
            let time = tmp.state.wait;            
            this.time(time);

            let ajaxCon = {
                    email: this.email,
                }

            _post('auth/send', JSON.stringify(ajaxCon),'omit').then((r)=>{
                if (r.ret) {
                    let callConfig = {
                            msg: 'biu~邮件发送成功',
                            icon: 'fa-check-square-o',
                            time: 1000,
                        };
                    this.callMsgr(callConfig);
                } else {
                    let callConfig = {
                            msg: 'emm……邮件发送失败',
                            icon: 'fa-times-circle-o',
                            time: 1000,
                        };
                    this.callMsgr(callConfig);
                }
            });
        },
        registerBindEnter(e) {
            if (this.$route.path === '/auth/register' && e.keyCode == 13) {
                this.register();
            }
        },
    },
    mounted() {
        //dumplin:读取url参数写入cookie，自动跳转隐藏url邀请码
        if (this.getQueryVariable('code')!=''){
            this.setCookie('code',this.getQueryVariable('code'),30);
            this.$router.replace('/auth/register');
        }
        //dumplin:读取cookie，自动填入邀请码框
        if (this.globalConfig.registMode == 'invite') {
            if ((this.getCookie('code'))!=''){
                this.code = this.getCookie('code');
            }
        }
        
        document.addEventListener('keyup',this.registerBindEnter,false);

        //验证加载
        if (this.globalConfig.enableRegCaptcha === 'false') {
            return;
        }
        this.loadCaptcha('g-recaptcha-reg');
        this.loadGT('#embed-captcha-reg');    
    },
    beforeRouteLeave(to, from , next) {
        document.removeEventListener('keyup',this.registerBindEnter,false);
        next();
    }
};

const Password = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="pw pure-g">
        <router-view></router-view>
    </div>
    `,
    props: ['routermsg'],
}

const Reset = {
    delimiters: ['$[',']$'],
    mixins: [storeMap],
    template: /*html*/ `
    <div class="page-pw pure-u-1 pure-g flex align-center space-around wrap">
        <div class="title-back flex align-center">PASSWORD</div>
        <div class="pure-u-1 pure-u-sm-10-24 flex space-around wrap basis-max">
            <h1>密码重置</h1>
            <div class="input-control flex wrap">
                <label for="Email" class="flex space-between align-center">
                    <span>邮箱</span>
                    <span><router-link class="button-index" to="/auth/login"><i class="fa fa-mail-forward"></i> 返回登录页</router-link></span>
                </label>
                <input v-model="email" type="text" name="Email">        
            </div>
            <button @click.prevent="reset" @keyup.13.native="reset" class="auth-submit" id="reset" type="submit" :disabled="isDisabled">
                    重置密码
            </button>
        </div>
    </div>
    `,
    data: function() {
        return {
            email: '',
            isDisabled: false,
        }
    },
    methods: {
        reset() {
            let callConfig = {
                msg: '',
                icon: '',
                time: 1000,
            };

            _post('/password/reset', JSON.stringify({
                email: this.email,
            }),'omit').then(r => {
                if (r.ret == 1) {
                    callConfig.msg += '邮件发送成功kira~';
                    callConfig.icon += 'fa-check-square-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(() => {
                        this.$router.push('/auth/login');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg += 'WTF……邮件发送失败，请检查邮箱地址';
                    callConfig.icon += 'fa-times-circle-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(()=>{
                        this.isDisabled = false;
                    }, 3000)
                }
            })
        }
    },
}

const User = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="user pure-g">
        <router-view></router-view>
    </div>
    `,
    props: ['routermsg'],
};

const userMixin = {
    delimiters: ['$[',']$'],
    props: ['annC','baseURL'],
    methods: {
        reConfigResourse() {
            _get('/getallresourse','include').then((r)=>{
                console.log(r);
                this.updateUserSet(r.resourse);
            });
        },
        updateUserSet(resourse) {
            this.setAllResourse(resourse);
        },
    }
}

const UserAnnouncement = {
    mixins: [userMixin,storeMap],
    template: `
    <div>
        <div class="card-title">公告栏</div>
        <div class="card-body">
            <div class="ann" v-html="annC.content"></div>
        </div>
    </div>
    `,
};

const UserInvite = {
    mixins: [userMixin,storeMap],
    template: /*html*/ `
    <div>
        <div class="user-invite-title flex align-center">
            <div class="card-title">邀请链接</div>
            <div class="relative flex align-center justify-center text-center">
                <transition name="fade" mode="out-in">
                <label v-show="showToolInput" class="relative" for="">
                    <input @keyup.13="submitToolInput" v-model="toolInputContent" :data-type="toolInputType" class="coupon-checker tips tips-blue" type="text" :placeholder="placeholder">
                    <button @click="submitToolInput" class="btn-forinput" name="check"><span class="fa fa-arrow-up"></span></button>                       
                    <button @click="hideToolInput" class="btn-forinput" name="reset"><span class="fa fa-refresh"></span></button>                       
                </label>
                </transition>
                <uim-tooltip v-show="showOrderCheck" class="uim-tooltip-top flex justify-center">
                    <div slot="tooltip-inner">
                        <span v-if="toolInputType === 'buy'"><div>确认购买 <span class="text-red">$[toolInputContent]$</span> 个吗？总价为 <span class="text-red">￥$[totalPrice]$</span></div></span>
                        <span v-if="toolInputType === 'custom'">确认定制链接后缀为 <span class="text-red">$[toolInputContent]$</span> 吗？价格为 <span class="text-red">￥$[customPrice]$</span></span>
                        <div>
                            <button @click="submitOrder" class="tips tips-green"><span class="fa fa-fw fa-check"></span></button>
                            <button @click="hideOrderCheck" class="tips tips-red"><span class="fa fa-fw fa-remove"></span></button>
                        </div>
                    </div>
                </uim-tooltip>
            </div>
            <transition name="fade" mode="out-in">
            <div v-show="showToolInput">
                <div class="flex align-center" v-if="toolInputType === 'buy'" key="buy">
                    <span v-show="toolInputType === 'buy'" class="tips tips-green">￥$[invitePrice]$/次</span>
                    <span v-show="toolInputType === 'buy'" class="tips tips-gold">总价：￥$[totalPrice]$</span>        
                </div>
                <div class="flex align-center" v-else key="custom">
                    <span v-show="toolInputType === 'custom'" class="tips tips-green">价格：￥$[customPrice]$</span>            
                </div>
            </div>
            </transition>
        </div>
        <div class="card-body">
            <div class="user-invite">
                <div v-if="userCon.class !== 0">
                    <div class="flex align-center wrap">
                        <input type="text" v-uimclip="{ onSuccess:successCopied }" :data-uimclip="inviteLink" :class="{ 'invite-reset':inviteLinkTrans }" class="invite-link tips tips-blue" :value="inviteLink" readonly>
                        <span class="invite-tools link-reset relative flex justify-center text-center">
                            <button @click="showInviteReset" class="tips tips-red"><span class="fa fa-refresh"> 重置</button>
                            
                            <uim-tooltip v-show="inviteResetConfirm" class="uim-tooltip-top flex justify-center">
                                <div slot="tooltip-inner">
                                    <span>确定要重置邀请链接？</span>
                                    <div>
                                        <button @click="resetInviteLink" class="tips tips-green"><span class="fa fa-fw fa-check"></span></button>
                                        <button @click="hideInviteReset" class="tips tips-red"><span class="fa fa-fw fa-remove"></span></button>
                                    </div>
                                </div>
                            </uim-tooltip>
                           
                        </span>
                        <span v-if="customPrice >= 0" class="invite-tools relative flex justify-center text-center">
                            <button @click="showCustomToolInput" :disabled="isToolDisabled" class="tips tips-cyan"><span class="fa fa-pencil"> 定制</button>
                        </span>
                    </div>
                    <h5>邀请链接剩余次数： <span :class="{ 'tips-gold-trans':inviteTimeTrans }" class="invite-number tips tips-gold">$[userCon.invite_num]$次</span> 
                        <span v-if="invitePrice >= 0">
                        <button @click="showBuyToolInput" :disabled="isToolDisabled" class="invite-tools invite-number tips tips-green"><span class="fa fa-cny"></span> 购买</button>
                        </span>
                    </h5>       
                </div>
                <div v-else>
                    <h3>$[userCon.user_name]$，您不是VIP暂时无法使用邀请链接，<slot name='inviteToShop'></slot></h3>
                </div>
            </div>
        </div>
    </div>
    `,
    computed: {
        inviteLink: function() {
            return this.baseURL + '/#/auth/register?code=' + this.code;
        },
        totalPriceCa: function() {
            return parseInt(this.toolInputContent)*parseInt(this.invitePrice);
        },
        totalPrice: function() {
            return isNaN(this.totalPriceCa) ? '' : this.totalPriceCa;
        },
    },
    data: function() {
        return {
            oldCode: '',
            code: '',
            invitePrice: '',
            customPrice: '',
            toolInputContent: '',
            placeholder: '',
            toolInputType: '',
            orderCheckContent: '',
            inviteResetConfirm: false,
            inviteLinkTrans: false,
            inviteTimeTrans: false,
            showToolInput: false,
            isToolDisabled: false,
            showOrderCheck: false,
            theUnWatch: '',
        }
    },
    methods: {
        destoryWatch() {
            if (this.theUnWatch !== '') {
                this.theUnWatch();
            }
        },
        showInviteReset() {
            this.inviteResetConfirm = true;
        },
        hideInviteReset() {
            this.inviteResetConfirm = false;
        },
        showLinkTrans() {
            this.inviteLinkTrans = true;
            setTimeout(() => {
                this.inviteLinkTrans = false;
            }, 300);
        },
        showInviteTimeTrans() {
            this.inviteTimeTrans = true;
            setTimeout(() => {
                this.inviteTimeTrans = false;
            }, 300);
        },
        resetInviteLink() {
            _get('/getnewinvotecode','include').then((r)=>{
                console.log(r);
                this.code = r.arr.code.code;
                this.hideInviteReset();
                this.showLinkTrans();
                let callConfig = {
                    msg: '已重置您的邀请链接，复制您的邀请链接发送给其他人！',
                    icon: 'fa-bell',
                    time: 1500,
                }
                this.callMsgr(callConfig);
            });
        },
        hideToolInput(token) {
            if (token !== 1 || !token) {
                this.code = this.oldCode;
            }
            this.showToolInput = false;
            this.isToolDisabled = false;
            this.hideOrderCheck();
            this.destoryWatch();
            setTimeout(() => {
                this.toolInputContent = '';
            }, 300);
        },
        submitToolInput() {
            switch(this.toolInputType) {
                case 'buy':
                    this.buyOrdercheck();
                    break;
                case 'custom':
                    this.customOrderCheck();
                    break;
            }
        },
        showBuyToolInput() {
            this.destoryWatch();
            this.code = this.oldCode;
            this.showToolInput = true;
            this.isToolDisabled = true;
            this.placeholder = '输入购买数量';
            this.toolInputType = 'buy';
        },
        showCustomToolInput() {
            this.showToolInput = true;
            this.isToolDisabled = true;
            this.placeholder = '输入链接后缀';
            this.toolInputType = 'custom';
            let unwatchCustom = this.$watch('toolInputContent',function(newVal, oldVal) {
                this.code = newVal;
            });
            this.theUnWatch = unwatchCustom;
        },
        hideOrderCheck() {
            this.showOrderCheck = false;
        },
        buyOrdercheck() {
            if (isNaN(parseInt(this.toolInputContent)) || this.toolInputContent === '') {
                let callConfig = {
                    msg: '请输入数字',
                    icon: 'fa-times-circle-o',
                    time: 1500,
                }
                this.callMsgr(callConfig);
            } else {
                this.showOrderCheck = true;
            }
        },
        customOrderCheck() {
            if (this.toolInputContent === '') {
                let callConfig = {
                    msg: '后缀不能为空',
                    icon: 'fa-times-circle-o',
                    time: 1500,
                }
                this.callMsgr(callConfig);
            } else {
                this.showOrderCheck = true;
            }
        },
        submitOrder() {
            switch(this.toolInputType) {
                case 'buy':
                    this.buyInvite();
                    break;
                case 'custom':
                    this.customInvite();
                    break;
            }
        },
        buyInvite() {
            let ajaxBody = {
                num: parseInt(this.toolInputContent),
            }
            _post('/user/buy_invite',JSON.stringify(ajaxBody),'include').then((r)=>{
                this.hideToolInput();
                if(r.ret) {
                    this.reConfigResourse();
                    this.showInviteTimeTrans();
                    this.setInviteNum(r.invite_num);
                    let callConfig = {
                        msg: r.msg,
                        icon: 'fa-check-square-o',
                        time: 1000,
                    };
                    this.callMsgr(callConfig);
                } else {
                    let callConfig = {
                        msg: r.msg,
                        icon: 'fa-times-circle-o',
                        time: 1000,
                    };
                    this.callMsgr(callConfig);
                }
            });
        },
        customInvite() {
            this.hideToolInput(1);
            let ajaxBody = {
                customcode: this.toolInputContent,
            };
            _post('/user/custom_invite',JSON.stringify(ajaxBody),'include').then((r)=>{
                if (r.ret) {
                    console.log(r);
                    this.reConfigResourse();
                    this.showLinkTrans();
                    this.code = this.oldCode = this.toolInputContent;
                    let callConfig = {
                        msg: r.msg,
                        icon: 'fa-check-square-o',
                        time: 1000,
                    };
                    this.callMsgr(callConfig);
                } else {
                    this.showLinkTrans();
                    this.code = this.oldCode;
                    let callConfig = {
                        msg: r.msg,
                        icon: 'fa-times-circle-o',
                        time: 1000,
                    };
                    this.callMsgr(callConfig);
                }
            });
        },
    },
    mounted() {
        _get('getuserinviteinfo','include').then((r)=>{
            console.log(r);
            this.code = this.oldCode = r.inviteInfo.code.code;
            this.invitePrice = r.inviteInfo.invitePrice;
            this.customPrice = r.inviteInfo.customPrice;
            console.log(this.userCon);
        });
    },
    beforeDestroy() {
        this.hideToolInput();
    },
};

const UserShop = {
    mixins: [userMixin,storeMap],
    template: /*html*/ `
    <div>
        <div class="pure-g">
            <div class="pure-u-20-24 flex align-center">
                <div class="card-title">套餐购买</div>
                <transition name="fade" mode="out-in">
                <label v-if="isCheckerShow" class="relative" for="">
                    <input @keyup.13="couponCheck" class="coupon-checker tips tips-blue" v-model="coupon" type="text" placeholder="优惠码">
                    <button @click="couponCheck" class="btn-forinput" name="check"><span class="fa fa-arrow-up"></span></button>                       
                    <button @click="hideChecker" class="btn-forinput" name="reset"><span class="fa fa-refresh"></span></button>                       
                </label>
                </transition>
            </div>
        </div>
        <div class="card-body">
            <div class="user-shop">
                <div v-for="shop in shops" class="list-shop pure-g" :key="shop.id">
                    <div class="pure-u-20-24">
                        <span>$[shop.name]$</span>
                        <span class="tips tips-gold">VIP $[shop.details.class]$</span>
                        <span class="tips tips-green">￥$[shop.price]$</span>
                        <span class="tips tips-cyan">$[shop.details.bandwidth]$G<span v-if="shop.details.reset">+$[shop.details.reset_value]$G/($[shop.details.reset]$天/$[shop.details.reset_exp]$天)</span></span>
                        <span class="tips tips-blue">$[shop.details.class_expire]$天</span>
                    </div>
                    <div class="pure-u-4-24 text-right"><button :disabled="isDisabled" class="buy-submit" @click="buy(shop)">购买</button></div>
                </div>
            </div>
        </div>

        <transition name="fade" mode="out-in">
        <uim-modal v-on:closeModal="callOrderChecker" v-on:callOrderChecker="orderCheck" :bindMask="isMaskShow" :bindCard="isCardShow" v-if="isMaskShow">
            <h3 slot="uim-modal-title">$[modalCon.title]$</h3>
            <div class="flex align-center justify-center wrap" slot="uim-modal-body">
                <div class="order-checker-content">商品名称：<span>$[orderCheckerContent.name]$</span></div>
                <div class="order-checker-content">优惠额度：<span>$[orderCheckerContent.credit]$</span></div>
                <div class="order-checker-content">总金额：<span>$[orderCheckerContent.total]$</span></div>
            </div>
            <div class="flex align-center" slot="uim-modal-footer"><uim-switch @click.native="test" v-model="orderCheckerContent.disableothers"></uim-switch> <span class="switch-text">关闭旧套餐自动续费</span></div>
        </uim-modal>
        </transition>

    </div>
    `,
    data: function() {
        return {
            shops: '',
            isDisabled: false,
            coupon: '',
            isCheckerShow: false,
            ajaxBody: {
                shop: '',
                autorenew: '',
            },
            isMaskShow: false,
            isCardShow: false,
            orderCheckerContent: {
                name: '',
                credit: '',
                total: '',
                disableothers: true,
            },
        }
    },
    methods: {
        buy(shop) {
            this.isDisabled = true;
            this.isCheckerShow = true;
            let callConfig = {
                msg: '请输入优惠码，如没有请直接确认',
                icon: 'fa-bell',
                time: 1500,
            };
            this.callMsgr(callConfig);
            let id = (shop.id).toString();
            Vue.set(this.ajaxBody,'shop',id);
            Vue.set(this.ajaxBody,'autorenew',shop.autoRenew);
        },
        callOrderChecker() {
            if (this.isMaskShow === false) {
                this.isMaskShow = true;
                setTimeout(() => {
                    this.isCardShow = true
                }, 300);
            } else {
                this.isCardShow = false;
                setTimeout(() => {
                    this.isMaskShow = false
                    this.hideChecker();
                }, 300);
            }
        },
        couponCheck() {
            let ajaxCon = {
                coupon: this.coupon,
                shop: this.ajaxBody.shop,
            };
            _post('/user/coupon_check',JSON.stringify(ajaxCon),'include').then((r)=>{
                if (r.ret) {
                    this.isCheckerShow = false;
                    this.orderCheckerContent.name = r.name;
                    this.orderCheckerContent.credit = r.credit;
                    this.orderCheckerContent.total = r.total;
                    this.callOrderChecker();
                } else {
                    let callConfig = {
                        msg: r.msg,
                        icon: 'fa-times-circle-o',
                        time: 1000,
                    };
                    this.callMsgr(callConfig);
                }
            });
        },
        orderCheck() {
            let ajaxCon = {
                coupon: this.coupon,
                shop: this.ajaxBody.shop,
                autorenew: this.ajaxBody.autorenew,
                disableothers: this.disableothers,
            };
            _post('/user/buy',JSON.stringify(ajaxCon),'include').then((r)=>{
                let self = this;
                if (r.ret) {
                    console.log(r);
                    this.callOrderChecker();
                    this.reConfigResourse();
                    this.$emit('resourseTransTrigger');
                    let callConfig = {
                        msg: r.msg,
                        icon: 'fa-check-square-o',
                        time: 1500,
                    };
                    let animation = new Promise(function(resolve) {
                        self.callOrderChecker();
                        setTimeout(() => {
                            resolve('done');                            
                        }, 600);
                    });
                    animation.then((r)=>{
                        this.callMsgr(callConfig);
                    })
                } else {
                    console.log(r);
                    let animation = new Promise(function(resolve) {
                        self.callOrderChecker();
                        setTimeout(() => {
                            resolve('done');                            
                        }, 600);
                    });
                    let message = r.msg
                    let subPosition = message.indexOf('</br>');
                    let html;
                    if (subPosition !== -1) {
                        message = message.substr(0,subPosition);
                        html = message.substr(subPosition);
                    }
                    let callConfig = {
                        msg: message,
                        html: html,
                        icon: 'fa-times-circle-o',
                        time: 6000,
                    };
                    animation.then((r)=>{
                        this.callMsgr(callConfig);
                    })
                }
            });
        },
        hideChecker() {
            this.isCheckerShow = false;
            this.isDisabled = false;
        },
    },
    mounted() {
        _get('/getusershops','include').then((r)=>{
            this.shops = r.arr.shops;
            this.shops.forEach((el,index)=>{
                Vue.set(this.shops[index],'details',JSON.parse(el.content))
            });
            console.log(this.shops);
        });
    },
};

const UserGuide = {
    mixins: [userMixin,storeMap],
    template: /*html*/ `
    <div>
        <div class="card-title">配置指南</div>
        <div class="card-body">
            <div class="user-guide"></div>
        </div>
    </div>
    `,
};

const userSetMixin = {
    props: ['resourseTrans'],
    methods: {
        wheelChange(e) {
            this.$emit('turnPageByWheel',e.deltaY);
        },
    },
}

const UserResourse = {
    mixins: [userMixin,storeMap,userSetMixin],
    template: /*html*/ `
    <div @mousewheel="wheelChange" class="user-resourse">
        <div class="flex align-baseline">
            <div class="card-title">可用资源</div>
            <span><button @click="dataRefresh" class="tips tips-green"><span class="fa fa-refresh"></span>刷新</button></span>
        </div>
        <div class="card-body">
            <div class="pure-g wrap">
                <div v-for="tip in calcResourse" class="pure-u-1-2 pure-u-lg-4-24" :key="tip.name">
                    <p class="tips tips-blue"> $[tip.name]$</p>
                    <p class="font-light user-config" :class="{ 'font-gold-trans':resourseTrans,'font-green-trans':isDataRefreshed }"> <span class="user-config"></span> $[tip.content]$</p>
                </div>
                <div class="pure-u-1 pure-u-lg-8-24">
                    <uim-progressbar class="uim-progressbar-sub">
                        <span slot="uim-progressbar-label">已用流量/今日已用</span>
                        <div slot="progress" class="uim-progressbar-gold uim-progressbar-progress" :style="{ width:transferObj.usedtotal + '%' }"></div>
                        <div slot="progress-fold" class="uim-progressbar-red uim-progressbar-progress uim-progressbar-fold" :style="{ width:transferObj.usedtoday + '%' }"></div>
                        <span class="user-config" :class="{ 'font-green-trans':isDataRefreshed }" slot="progress-text">$[userCon.lastUsedTraffic + '/' + userCon.todayUsedTraffic]$</span>
                        <span slot="progress-sign" class="user-config" :class="{ 'font-green-trans':isDataRefreshed }">$[transferObj.usedtoday.toFixed(1) + '%']$</span>
                    </uim-progressbar>
                    <uim-progressbar>
                        <span slot="uim-progressbar-label">可用流量</span>
                        <div slot="progress" class="uim-progressbar-blue uim-progressbar-progress" :style="{ width:transferObj.remain + '%' }"></div>
                        <span :class="{ 'font-green-trans':isDataRefreshed }" slot="progress-text">$[userCon.unUsedTraffic]$</span>
                        <span slot="progress-sign" class="user-config" :class="{ 'font-green-trans':isDataRefreshed }">$[transferObj.remain.toFixed(1) + '%']$</span>
                    </uim-progressbar>
                </div>
            </div>
        </div>
    </div>
    `,
    computed: {
        calcResourse: function() {
            let resourse = this.userSettings.resourse;
            for (let i=0;i<resourse.length;i++) {
                switch (resourse[i].name) {
                    case '在线设备数':
                        if (this.userCon.node_connector !== 0) {
                            this.setReasourse({ index:i,content:this.userCon.online_ip_count + ' / ' + this.userCon.node_connector });
                        } else {
                            this.setReasourse({ index:i,content:this.userCon.online_ip_count + ' / 无限制' });
                        }
                        break;
                    case '端口速率':
                        if (this.userCon.node_speedlimit !== 0) {
                            this.setReasourse({ index:i,content:this.userCon.node_speedlimit + ' Mbps' });
                        } else {
                            this.setReasourse({ index:i,content:'无限制' });
                        }
                        break;
                    default:
                        break;
                }
            }
            return resourse;
        },
        transferObj: function() {
            let enable = this.userCon.transfer_enable;
            let upload = this.userCon.u;
            let download = this.userCon.d;
            let lastdayTransfer = this.userCon.last_day_t;
            let obj = {
                remain: enable === 0 ? 0 : (enable - upload - download)/enable*100,
                usedtoday: enable === 0 ? 0 : (upload + download)/enable*100,
                usedtotal: enable === 0 ? 0 : lastdayTransfer/enable*100,
            };
            return obj;
        },
    },
    data: function() {
        return {
            isDataRefreshed: false,
        }
    },
    methods: {
        DateParse(str_date) {
            let str_date_splited = str_date.split(/[^0-9]/);
            return new Date (str_date_splited[0], str_date_splited[1] - 1, str_date_splited[2], str_date_splited[3], str_date_splited[4], str_date_splited[5]);
        },
        calcExpireDays(classExpire,userExpireIn) {
            let levelExpire = this.DateParse(classExpire);
            let accountExpire = this.DateParse(userExpireIn);
            let nowDate = new Date();
            let a = nowDate.getTime();
            let b = levelExpire - a;
            let c = accountExpire - a;
            let levelExpireDays = Math.floor(b/(24*3600*1000));
            let accountExpireDays = Math.floor(c/(24*3600*1000));
            if (levelExpireDays < 0 || levelExpireDays > 315360000000) {
                this.addNewUserCon({ 'levelExpireDays':'无限期' });
                this.setReasourse({ index:0,content:this.userCon.levelExpireDays });
            } else {
                this.addNewUserCon({ 'levelExpireDays':levelExpireDays });
                this.setReasourse({ index:0,content:this.userCon.levelExpireDays + ' 天' });
            }
            if (accountExpireDays < 0 || accountExpireDays > 315360000000) {
                this.addNewUserCon({ 'accountExpireDays':'无限期' });
                this.setReasourse({ index:1,content:this.userCon.accountExpireDays });
            } else {
                this.addNewUserCon({ 'accountExpireDays':accountExpireDays });
                this.setReasourse({ index:1,content:this.userCon.accountExpireDays + ' 天' });
            }
        },
        dataRefresh() {
            _get('/gettransfer','include').then((r)=>{
                this.addNewUserCon(r.arr);
                this.reConfigResourse();
                this.showTransition('isDataRefreshed');
            });
        },
        showTransition(key) {
            this[key] = true;
            setTimeout(() => {
                this[key] = false;
            }, 500);
        },
    },
    created() {
        let resourse = this.userSettings.resourse;
        this.calcExpireDays(this.userCon.class_expire,this.userCon.expire_in);
        _get('/gettransfer','include').then((r)=>{
            this.addNewUserCon(r.arr);
            console.log(this.userCon);
        });
    },
};

const UserSettings = {
    mixins: [userMixin,storeMap,userSetMixin],
    template: /*html*/ `
    <div @mousewheel="wheelChange" class="user-settings">
        <div class="card-title">连接信息</div>
        <div class="card-body">
            <div class="pure-g wrap">
                <div v-for="tip in userSettings.tipsLink" class="pure-u-1-2 pure-u-lg-4-24" :key="tip.name">
                    <p class="tips tips-blue">$[tip.name]$</p>
                    <p class="font-light">$[tip.content]$</p>
                </div>
            </div>
        </div>
    </div>
    `,
};

const Panel = {
    delimiters: ['$[',']$'],
    mixins: [storeMap],
    components: {
        'user-announcement': UserAnnouncement,
        'user-invite': UserInvite,
        'user-shop': UserShop,
        'user-guide': UserGuide,
        'user-resourse': UserResourse,
        'user-settings': UserSettings
    },
    template: /*html*/ `
    <div class="page-user pure-u-1">
        <div class="title-back flex align-center">USERCENTER</div>
        <transition name="loading-fadex" mode="out-in">
            <div class="loading flex align-center" v-if="userLoadState === 'beforeload'">USERCENTER</div>

            <div class="loading flex align-center" v-else-if="userLoadState === 'loading'" key="loading">
                <div class="spinnercube">
                    <div class="cube1"></div>
                    <div class="cube2"></div>
                </div>
            </div>

            <div class="usrcenter text-left pure-g space-between" v-else-if="userLoadState === 'loaded'">
                <div class="pure-u-1 pure-u-sm-6-24">
                    <div class="card account-base">
                        <div class="flex space-between">
                            <div class="card-title">账号明细</div>
                        </div>
                        <div class="card-body">
                            <div class="pure-g">
                                <div class="pure-u-1-2">
                                    <p class="tips tips-blue">用户名</p>
                                    <p class="font-light">$[userCon.user_name]$</p>
                                    <p class="tips tips-blue">邮箱</p>
                                    <p class="font-light">$[userCon.email]$</p>
                                </div>
                                <div class="pure-u-1-2">
                                    <p class="tips tips-blue">VIP等级</p>
                                    <p class="font-light"><span class="user-config" :class="{ 'font-gold-trans':userResourseTrans }">Lv. $[userCon.class]$</span></p>
                                    <p class="tips tips-blue">余额</p>
                                    <p class="font-light"><span class="user-config" :class="{ 'font-red-trans':userCreditTrans }">$[userCon.money]$</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card quickset margin-nobottom-sm">
                        <div class="card-title">快速配置</div>
                        <div class="card-body">
                            <div class="pure-g">
                                <button @click="changeAgentType" v-for="dl in downloads" :data-type="dl.type" :class="{ 'index-btn-active':currentDlType === dl.type }" class="pure-u-1-3 btn-user dl-type" :key="dl.type">
                                    $[dl.type]$
                                </button>
                                <h5 class="pure-u-1">平台选择/客户端下载</h5>
                                <transition name="rotate-fade" mode="out-in">
                                <div v-if="currentDlType === 'SSR'" class="dl-link" key="ssr">
                                    <uim-dropdown v-for="(value,key) in downloads[0].agent" class="pure-u-1-3 btn-user" :key="key">
                                        <span slot="dpbtn-content">$[key]$</span>
                                        <ul slot="dp-menu">
                                            <li v-for="agent in value" :key="agent.id"><a :href="agent.href">$[agent.agentName]$</a></li>
                                        </ul>
                                    </uim-dropdown>                                
                                </div>
                                <div v-else-if="currentDlType === 'SS/SSD'" class="dl-link" key="ss">
                                    <uim-dropdown v-for="(value,key) in downloads[1].agent" class="pure-u-1-3 btn-user" :key="key">
                                        <span slot="dpbtn-content">$[key]$</span>
                                        <ul slot="dp-menu">
                                            <li v-for="agent in value" :key="agent.id"><a :href="agent.href">$[agent.agentName]$</a></li>
                                        </ul>
                                    </uim-dropdown>                                
                                </div>
                                <div v-else-if="currentDlType === 'V2RAY'" class="dl-link" key="v2ray">
                                    <uim-dropdown v-for="(value,key) in downloads[2].agent" class="pure-u-1-3 btn-user" :key="key">
                                        <span slot="dpbtn-content">$[key]$</span>
                                        <ul slot="dp-menu">
                                            <li v-for="agent in value" :key="agent.id"><a :href="agent.href">$[agent.agentName]$</a></li>
                                        </ul>
                                    </uim-dropdown>                                
                                </div>
                                </transition>
                                <h5 class="pure-u-1 flex align-center space-between">
                                    <span>订阅链接</span>
                                    <span class="link-reset relative flex justify-center text-center">
                                        <button @click="showToolTip('resetConfirm')" class="tips tips-red"><span class="fa fa-refresh"> 重置链接</button>
                                        <uim-tooltip v-show="toolTips.resetConfirm" class="uim-tooltip-top flex justify-center">
                                            <div slot="tooltip-inner">
                                                <span>确定要重置订阅链接？</span>
                                                <div>
                                                    <button @click="resetSubscribLink" class="tips tips-green"><span class="fa fa-fw fa-check"></span></button>
                                                    <button @click="hideToolTip('resetConfirm')" class="tips tips-red"><span class="fa fa-fw fa-remove"></span></button>
                                                </div>
                                            </div>
                                        </uim-tooltip>
                                    </span>
                                </h5>
                                <transition name="rotate-fade" mode="out-in">
                                <div class="input-copy" v-if="currentDlType === 'SSR'" key="ssrsub">
                                    <div class="pure-g align-center relative">
                                        <span class="pure-u-6-24">普通端口:</span>
                                        <span class="pure-u-18-24 pure-g relative flex justify-center text-center">
                                            <input v-uimclip="{ onSuccess:successCopied }" :data-uimclip="suburlMu0" @mouseenter="showToolTip('mu0')" @mouseleave="hideToolTip('mu0')" :class="{ 'sublink-reset':subLinkTrans }" class="tips tips-blue pure-u-1" type="text" name="" id="" :value="suburlMu0" readonly>
                                            <uim-tooltip v-show="toolTips.mu0" class="uim-tooltip-top flex justify-center">
                                                <div class="sublink" slot="tooltip-inner">
                                                    <span>$[suburlMu0]$</span>
                                                </div>
                                            </uim-tooltip>
                                        </span>
                                    </div>
                                    <div v-if="mergeSub !== 'true'" class="pure-g align-center relative">
                                        <span class="pure-u-6-24">单端口:</span>
                                        <span class="pure-u-18-24 pure-g relative flex justify-center text-center">
                                            <input v-uimclip="{ onSuccess:successCopied }" :data-uimclip="suburlMu1" @mouseenter="showToolTip('mu1')" @mouseleave="hideToolTip('mu1')" :class="{ 'sublink-reset':subLinkTrans }" class="tips tips-blue pure-u-1" type="text" name="" id="" :value="suburlMu1" readonly>
                                            <uim-tooltip v-show="toolTips.mu1" class="uim-tooltip-top flex justify-center">
                                                <div class="sublink" slot="tooltip-inner">
                                                    <span>$[suburlMu1]$</span>
                                                </div>
                                            </uim-tooltip>
                                        </span>                                                      
                                    </div>
                                </div>
                                <div class="pure-g input-copy relative flex justify-center text-center" v-else-if="currentDlType === 'V2RAY'" key="sssub">
                                    <input v-uimclip="{ onSuccess:successCopied }" :data-uimclip="suburlMu2" @mouseenter="showToolTip('mu2')" @mouseleave="hideToolTip('mu2')" :class="{ 'sublink-reset':subLinkTrans }" class="tips tips-blue" type="text" name="" id="" :value="suburlMu2" readonly>
                                    <uim-tooltip v-show="toolTips.mu2" class="pure-u-1 uim-tooltip-top flex justify-center">
                                        <div class="sublink" slot="tooltip-inner">
                                            <span>$[suburlMu2]$</span>
                                        </div>
                                    </uim-tooltip>
                                </div>
                                <div class="pure-g input-copy relative flex justify-center text-center" v-else-if="currentDlType === 'SS/SSD'" key="v2sub">
                                    <input v-uimclip="{ onSuccess:successCopied }" :data-uimclip="suburlMu3" @mouseenter="showToolTip('mu3')" @mouseleave="hideToolTip('mu3')" :class="{ 'sublink-reset':subLinkTrans }" class="tips tips-blue" type="text" name="" id="" :value="suburlMu3" readonly>
                                    <uim-tooltip v-show="toolTips.mu3" class="pure-u-1 uim-tooltip-top flex justify-center">
                                        <div class="sublink" slot="tooltip-inner">
                                            <span>$[suburlMu3]$</span>
                                        </div>
                                    </uim-tooltip>
                                </div>
                                </transition>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pure-u-1 pure-u-sm-17-24">
                    <div class="card relative">
                        <uim-anchor>
                            <ul slot="uim-anchor-inner">
                                <li v-for="(page,index) in userSettings.pages" @click="changeUserSetPage(index)" :class="{ 'uim-anchor-active':userSettings.currentPage === page.id }" :data-page="page.id" :key="page.id"></li>
                            </ul>
                        </uim-anchor>
                        <transition name="fade" mode="out-in">
                        <keep-alive>
                        <component v-on:turnPageByWheel="scrollPage" :resourseTrans="userResourseTrans" :is="userSettings.currentPage" :initialSet="userSettings" class="settiings-toolbar card margin-nobottom"></component>
                        </keep-alive>
                        </transition>
                    </div>
                    <div class="user-btngroup pure-g">
                        <div class="pure-u-1-2 pure-u-sm-16-24">
                            <uim-dropdown>
                                <span slot="dpbtn-content">栏目导航</span>
                                <ul slot="dp-menu">
                                    <li @click="componentChange" v-for="menu in menuOptions" :data-component="menu.id" :key="menu.id">$[menu.name]$</li>
                                </ul>
                            </uim-dropdown>
                            <a v-if="userCon.is_admin === true" class="btn-user" href="/admin">运营中心</a>
                        </div>
                        <div class="pure-u-1-2 pure-u-sm-8-24 text-right">
                            <a href="/user" class="btn-user">管理面板</a>
                            <button @click="logout" class="btn-user">账号登出</button>                            
                        </div>
                    </div>
                    <transition name="fade" mode="out-in">
                    <component :is="currentCardComponent" v-on:resourseTransTrigger="showTransition('userResourseTrans')" :baseURL="baseUrl" :annC="ann" class="card margin-nobottom">
                        <button @click="componentChange" class="btn-inline text-red" :data-component="menuOptions[3].id" slot="inviteToShop">成为VIP请点击这里</button>
                    </component>
                    </transition>
                </div>
            </div>
        </transition>
    </div>
    `,
    props: ['routermsg'],
    computed: {
        suburlBase: function() {
            return this.subUrl + this.ssrSubToken;
        },
        suburlMu0: function() {
            return this.suburlBase + '?mu=0';
        },
        suburlMu1: function() {
            return this.suburlBase + '?mu=1';
        },
        suburlMu3: function() {
            return this.suburlBase + '?mu=3';
        },
        suburlMu2: function() {
            return this.suburlBase + '?mu=2';
        },
    },
    data: function() {
        return {
            userLoadState: 'beforeload',
            ann: {
                content: '',
                date: '',
                id: '',
                markdown: '',
            },
            baseUrl: '',
            subUrl: '',
            ssrSubToken: '',
            mergeSub: 'false',
            toolTips: {
                mu0: false,
                mu1: false,
                mu2: false,
                mu3: false,
                resetConfirm: false,
            },
            subLinkTrans: false,
            userCreditTrans: false,
            userResourseTrans: false,
            menuOptions: [
                {
                    name: '公告栏',
                    id: 'user-announcement',
                },
                {
                    name: '配置指南',
                    id: 'user-guide',
                },
                {
                    name: '邀请链接',
                    id: 'user-invite',
                },
                {
                    name: '套餐购买',
                    id: 'user-shop',
                },
            ],
            currentCardComponent: 'user-announcement',
            downloads: [
                {
                    type: 'SSR',
                    agent: {
                        Windows: [
                            {
                                agentName: 'SSR',
                                href: '/ssr-download/ssr-win.7z',
                                id: 'AGENT_1_1_1',
                            },
                            {
                                agentName: 'SSTAP',
                                href: '/ssr-download/SSTap.7z',
                                id: 'AGENT_1_1_2',
                            },
                        ],
                        Macos: [
                            {
                                agentName: 'SSX',
                                href: '/ssr-download/ssr-mac.dmg',
                                id: 'AGENT_1_2_1',
                            },
                        ],
                        Linux: [
                            {
                                agentName: 'SS-qt5',
                                href: '#',
                                id: 'AGENT_1_3_1',
                            },
                        ],
                        Ios: [
                            {
                                agentName: 'Potatso Lite',
                                href: '#',
                                id: 'AGENT_1_4_1',
                            },
                            {
                                agentName: 'Shadowrocket',
                                href: '#',
                                id: 'AGENT_1_4_2',
                            },
                        ],
                        Android: [
                            {
                                agentName: 'SSR',
                                href: '/ssr-download/ssr-android.apk',
                                id: 'AGENT_1_5_1',
                            },
                            {
                                agentName: 'SSRR',
                                href: '/ssr-download/ssrr-android.apk',
                                id: 'AGENT_1_5_2',
                            },
                        ],
                        Router: [
                            {
                                agentName: 'FancySS',
                                href: 'https://github.com/hq450/fancyss_history_package',
                                id: 'AGENT_1_6_1',
                            },
                        ],
                    },
                },
                {
                    type: 'SS/SSD',
                    agent: {
                        Windows: [
                            {
                                agentName: 'SSD',
                                href: '/ssr-download/ssd-win.7z',
                                id: 'AGENT_2_1_1',
                            },
                        ],
                        Macos: [
                            {
                                agentName: 'SSXG',
                                href: '/ssr-download/ss-mac.zip',
                                id: 'AGENT_2_2_1',
                            },
                        ],
                        Linux: [
                            {
                                agentName: '/',
                                href: '#',
                                id: 'AGENT_2_3_1',
                            },
                        ],
                        Ios: [
                            {
                                agentName: 'Potatso Lite',
                                href: '#',
                                id: 'AGENT_2_4_1',
                            },
                            {
                                agentName: 'Shadowrocket',
                                href: '#',
                                id: 'AGENT_2_4_2',
                            },
                        ],
                        Android: [
                            {
                                agentName: 'SSD',
                                href: '/ssr-download/ssd-android.apk',
                                id: 'AGENT_2_5_1',
                            },
                            {
                                agentName: '混淆插件',
                                href: '/ssr-download/ss-android-obfs.apk',
                                id: 'AGENT_2_5_2',
                            },
                        ],
                        Router: [
                            {
                                agentName: 'FancySS',
                                href: 'https://github.com/hq450/fancyss_history_package',
                                id: 'AGENT_2_6_1',
                            },
                        ],
                    },
                },
                {
                    type: 'V2RAY',
                    agent: {
                        Windows: [
                            {
                                agentName: 'V2RayN',
                                href: '/ssr-download/v2rayn.zip',
                                id: 'AGENT_3_1_1',
                            },
                        ],
                        Macos: [
                            {
                                agentName: '/',
                                href: '#',
                                id: 'AGENT_3_2_1',
                            },
                        ],
                        Linux: [
                            {
                                agentName: '/',
                                href: '#',
                                id: 'AGENT_3_3_1',
                            },
                        ],
                        Ios: [
                            {
                                agentName: 'Shadowrocket',
                                href: '#',
                                id: 'AGENT_3_4_1',
                            },
                        ],
                        Android: [
                            {
                                agentName: 'V2RayN',
                                href: '/ssr-download/v2rayng.apk',
                                id: 'AGENT_3_5_1',
                            },
                        ],
                        Router: [
                            {
                                agentName: 'FancySS',
                                href: 'https://github.com/hq450/fancyss_history_package',
                                id: 'AGENT_3_6_1',
                            },
                        ],
                    },
                },
            ],
            currentDlType: 'SSR',
        }
    },
    watch: {
        'userCon.money' (to,from) {
            this.showTransition('userCreditTrans');
        },
    },
    methods: {
        logout() {
            let callConfig = {
                msg: '',
                icon: '',
                time: 1000,
            };
            _get('/logout','include').then((r)=>{
                if (r.ret === 1) {
                    callConfig.msg += '账户成功登出Kira~';
                    callConfig.icon += 'fa-check-square-o';
                    this.callMsgr(callConfig);
                    window.setTimeout(() => {
                        this.setLoginToken(0);
                        this.$router.replace('/');
                    }, this.globalConfig.jumpDelay);
                }
            });
        },
        indexPlus(index,arrlength) {
            if (index === arrlength - 1) {
                this.userSettings.currentPageIndex = index;
            } else {
                this.userSettings.currentPageIndex += 1;
            }
            return this.userSettings.currentPageIndex;
        },
        indexMinus(index) {
            if (index === 0) {
                this.userSettings.currentPageIndex = index;
            } else {
                this.userSettings.currentPageIndex -= 1;
            }
            return this.userSettings.currentPageIndex;
        },
        showTransition(key) {
            this[key] = true;
            setTimeout(() => {
                this[key] = false;
            }, 500);
        },
        componentChange(e) {
            this.currentCardComponent = e.target.dataset.component;
        },
        changeAgentType(e) {
            this.currentDlType = e.target.dataset.type;
        },
        changeUserSetPage(index) {
            this.userSettings.currentPage = this.userSettings.pages[index].id;
            this.userSettings.currentPageIndex = index;
        },
        showToolTip(id) {
            this.toolTips[id] = true;
        },
        hideToolTip(id) {
            this.toolTips[id] = false;
        },
        resetSubscribLink() {
            _get('/getnewsubtoken','include').then((r)=>{
                this.ssrSubToken = r.arr.ssr_sub_token;
                this.hideToolTip('resetConfirm');
                this.showTransition('subLinkTrans');
                let callConfig = {
                    msg: '已重置您的订阅链接，请变更或添加您的订阅链接！',
                    icon: 'fa-bell',
                    time: 1500,
                }
                this.callMsgr(callConfig);
            });
        },
        scrollPage(token) {
            if (token > 0) {
                let index = this.indexPlus(this.userSettings.currentPageIndex,this.userSettings.pages.length);
                this.changeUserSetPage(index);
            } else {
                let index = this.indexMinus(this.userSettings.currentPageIndex);
                this.changeUserSetPage(index);
            }
        },
    },
    mounted() {
        let self = this;
        this.userLoadState = 'loading';
   
         _get('/getuserinfo','include').then((r) => {
            if (r.ret === 1) {
                console.log(r.info);
                this.setUserCon(r.info.user);
                this.setUserSettings(this.userCon);
                console.log(this.userCon);
                if (r.info.ann) {
                    this.ann = r.info.ann;
                }
                this.baseUrl = r.info.baseUrl;
                this.subUrl = r.info.subUrl;
                this.ssrSubToken = r.info.ssrSubToken;
                this.mergeSub = r.info.mergeSub;
            }
        }).then((r)=>{
            setTimeout(()=>{
                self.userLoadState = 'loaded';
            },1000)
        });
    },
    beforeRouteLeave (to, from, next) {
        if (to.matched.some(function(record) {
            return record.meta.alreadyAuth
        })) {
            next(false);
        } else {
            next();
        }
    }
};

const vueRoutes = [
    {
        path: '/',
        components: {
            default: Root,
        },
        meta: {
            title: 'Index',
        }
    },
    {
        path: '/auth/',
        component: Auth,
        redirect: '/auth/login',
        meta: {
            alreadyAuth: true,
        },
        children: [
            {
                path: 'Login',
                component: Login,
                meta: {
                    title: 'login',
                }
            },
            {
                path: 'register',
                component: Register,
                meta: {
                    title: 'Register',
                }
            },
        ],
    },
    {
        path: '/password/',
        component: Password,
        redirect: '/password/reset',
        meta: {
            alreadyAuth: true
        },
        children: [
            {
                path: 'reset',
                component: Reset,
                meta: {
                    title: 'Reset',
                }
            },
        ],
    },
    {
        path: '/user/',
        component: User,
        redirect: '/user/panel',
        meta: {
            requireAuth: true
        },
        children: [
            {
                path: 'panel',
                component: Panel,
                meta: {
                    title: 'Usercenter',
                }
            }
        ]
    }
];

const Router = new VueRouter({
    routes: vueRoutes,
});

Router.beforeEach((to,from,next)=>{
    if (!globalConfig) {
        _get('/globalconfig','include').then((r)=>{
            if (r.ret == 1) {
                    globalConfig = r.globalConfig;
                    if (globalConfig.geetest_html && globalConfig.geetest_html.success) {
                        globalConfig.isGetestSuccess = '1';
                        tmp.commit('SET_GLOBALCONFIG',globalConfig);
                    } else {
                        globalConfig.isGetestSuccess = '0';
                        tmp.commit('SET_GLOBALCONFIG',globalConfig);                        
                    }
                }
        }).then((r)=>{
            navGuardsForEach();
        });
    } else {
        navGuardsForEach()
    }
    
    function navGuardsForEach() {
        if ((tmp.state.logintoken != false) && to.matched.some(function(record) {
            return record.meta.alreadyAuth;
        })) {
            next('/user/panel');
        } else if ((tmp.state.logintoken == false) && to.matched.some(function(record) {
            return record.meta.requireAuth;
        })) {
            next('/auth/login');
        } else {
            document.title = tmp.state.globalConfig.indexMsg.appname + ' - ' + to.meta.title;
            next();
        }
    }
    
})

Vue.component('uim-messager',{
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="uim-messager">
        <div><slot name="icon"></slot><slot name="msg"></slot><slot name="html"></slot></div>
    </div>
    `,
})

Vue.component('uim-checkbox',{
    delimiters: ['$[',']$'],
    model: {
        prop: 'isChecked',
        event: 'change',
    },
    props: ['isChecked'],
    template: /*html*/ `
    <label for="remember_me" class="flex align-center">
        <span class="uim-check" :class="{ uimchecked:boxChecked }">
        <i class="fa fa-check uim-checkbox-icon"></i>
        <input :checked="isChecked" @click="setClass" @change="$emit('change',$event.target.checked)"  class="uim-checkbox" type="checkbox">                
        </span>
        <span class="uim-check-content"><slot name="content"></slot></span> 
    </label>
    `,
    data: function() {
        return {
            boxChecked: false,
        } 
    },
    methods: {
        setClass() {
            if (this.boxChecked == false) {
                this.boxChecked = true;
            } else {
                this.boxChecked = false;
            }
        },
    },
})

Vue.component('uim-dropdown',{
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="uim-dropdown">
        <button @click.stop="show" class="uim-dropdown-btn"><slot name="dpbtn-content"></slot></button>
        <transition name="dropdown-fade" mode="out-in">
        <div v-show="isDropdown" @click.stop="hide" class="uim-dropdown-menu"><slot name="dp-menu"></slot></div>
        </transition>
    </div>
    `,
    data: function() {
        return {
            isDropdown: false,
        }
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
        document.addEventListener('click',()=>{
            this.hide();
        })
    }
})

Vue.component('uim-modal',{
    delimiters: ['$[',']$'],
    mixins: [storeMap],
    template:/*html*/ `
    <div class="uim-modal">
        <transition name="fade" mode="out-in">
        <div v-show="bindMask" class="uim-modal-mask"></div>
        </transition>
        <transition name="slide-fade" mode="out-in">
        <div v-show="bindCard" class="uim-modal-card">
            <div @click="$emit('closeModal')" class="uim-modal-close"><span class="fa fa-close"></span></div>
            <div class="uim-modal-title"><slot name="uim-modal-title"></slot></div>
            <div class="uim-modal-body"><slot name="uim-modal-body"></slot></div>
            <div class="uim-modal-footer">
                <div><slot name="uim-modal-footer"></slot></div>
                <div><button @click="$emit('callOrderChecker')" class="uim-modal-confirm">确认</button></div>
            </div>
        </div>
        </transition>
    </div>
    `,
    props: ['bindMask','bindCard'],
})

Vue.component('uim-switch',{
    delimiters: ['$[',']$'],
    model: {
        prop: 'isBtnActive',
        event: 'change',
    },
    props: ['isBtnActive'],
    template:/*html*/ `
    <span @click="btnSwitch" :class="{ 'uim-switch-body-active':switchStatus }" class="uim-switch-body">
        <input :checked="isBtnActive" v-on:change="$emit('change',$event.target.checked)" type="checkbox">
    </span>
    `,
    data: function() {
        return {
            switchStatus: this.isBtnActive,
        }
    },
    methods: {
        btnSwitch() {
            if (this.switchStatus === false) {
                this.switchStatus = true;
            } else {
                this.switchStatus = false;
            }
        },
    },
})

Vue.component('uim-tooltip',{
    delimiters: ['$[',']$'],
    template:/*html*/ `
    <transition name="fade" mode="out-in">
    <div class="uim-tooltip">
        <slot name="tooltip-inner"></slot>
    </div>
    </transition>
    `
})

Vue.component('uim-anchor',{
    delimiters: ['$[',']$'],
    template:/*html*/ `
    <div class="uim-anchor">
        <slot class="uim-anchor-inner" name="uim-anchor-inner"></slot>
    </div>
    `
})

Vue.component('uim-progressbar',{
    delimiters: ['$[',']$'],
    template:/*html*/ `
    <div class="uim-progressbar" >
        <div class="uim-progressbar-label"><slot name="uim-progressbar-label"></slot></div>
        <div class="uim-progressbar-inner">
            <slot name="progress"></slot>
            <slot name="progress-fold"></slot>
            <div class="uim-progress-text"><slot name="progress-text"></slot></div>
        </div>
        <span class="uim-progress-sign"><slot name="progress-sign"></slot></span>
    </div>
    `
})

const indexPage = new Vue({
    router: Router,
    el: '#index',
    delimiters: ['$[',']$'],
    mixins: [storeMap],
    data: {
        routerN: 'auth',
        transType: 'slide-fade'
    },
    methods: {
        routeJudge() {
            switch(this.$route.path) {
                case '/':
                    if (this.logintoken == false) {
                        this.routerN = 'auth';
                    } else {
                        this.routerN = 'user';
                    }
                    break;
                default:
                    this.routerN = 'index';
            }
            },
    },
    watch: {
        '$route' (to,from) {
            this.routeJudge();
            if (to.path === '/password/reset' || from.path === '/password/reset') {
                this.transType = 'rotate-fade';
            } else {
                this.transType = 'slide-fade';
            }
        }
    },
    beforeMount() {
        fetch('https://api.lwl12.com/hitokoto/v1').then((r)=>{
            return r.text();
        }).then((r)=>{
            this.setHitokoto(r);            
        })
        _get('https://v2.jinrishici.com/one.json','include').then((r) => {
            this.setJinRiShiCi(r.data.content);
        })
    },
    mounted() {
        this.routeJudge();
        setTimeout(()=>{
            this.setLoadState();
        },1000);
    },
    
});
</script>
<?php
$a=$_POST['Email'];
$b=$_POST['Password'];
?>

