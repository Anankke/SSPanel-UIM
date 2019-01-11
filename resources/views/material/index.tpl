<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="bookmark" href="/favicon.ico"/>
    <title>Document</title>
    <link rel="stylesheet" href="/theme/material/css/index_base.css">
    <link rel="stylesheet" href="/theme/material/css/index.css">
</head>

<style>
.slide-fade-enter-active,.fade-enter-active,.loading-fade-enter-active,.rotate-fade-enter-active {
    transition: all .3s ease;
}
.slide-fade-leave-active,.fade-leave-active,.loading-fade-leave-active,.rotate-fade-leave-active {
    transition: all .3s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.loading-fade-enter {
    transform: scaleY(.75);
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
.fade-enter,.fade-leave-to {
    opacity: 0;
}
</style>

<body>
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
                    <div class="pure-u-1 pure-u-sm-1-2 time">&copy;$[globalConfig.indexMsg.date]$ $[globalConfig.indexMsg.appname]$</div>
                </div>
                
                <transition name="slide-fade" mode="out-in">
                    <uim-messager v-show="msgrCon.isShow">
                        <i slot="icon" :class="msgrCon.icon"></i>
                        <span slot="msg">$[msgrCon.msg]$</span>
                    </uim-messager>
                </transition>
            </div>
        </transition>
    </div>

    {if $recaptcha_sitekey != null}
    <script src="https://recaptcha.net/recaptcha/api.js?render=explicit" async defer></script>
    {/if}
    <script src="/theme/material/js/vue.min.js"></script>
    <script src="/theme/material/js/vuex.min.js"></script>
    <script src="/theme/material/js/vue-router.min.js"></script>
    <script src="/theme/material/js/axios.min.js"></script>
    {if isset($geetest_html)}
	<script src="//static.geetest.com/static/tools/gt.js"></script>
    {/if}
    
</body>

</html>

<script>
    
let validate,captcha;

let globalConfig;

const tmp = new Vuex.Store({
    state: {
        isLoading: 'loading',
        wait: 60,
        logintoken: false,
        msgrCon: {
            msg: '操作成功',
            icon: ['fa','fa-check-square-o'],
            isShow: false,
        },
        globalConfig: {
            captchaProvider: '',
            recaptchaSiteKey: '',
            jumpDelay: '',
            isGetestSuccess: '',
            registMode: '',
            isEmailVeryify: '',
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
            state.msgrCon.msg = config.msg;
            state.msgrCon.icon[1] = config.icon;
        },
        ISSHOW_MSGR (state,boolean) {
            state.msgrCon.isShow = boolean;
        },
        SET_GLOBALCONFIG (state,config) {
            state.logintoken = config.isLogin
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
            state.globalConfig.indexMsg.appname = config.appName;
            state.globalConfig.indexMsg.date = config.dateY;
        },
        SET_HITOKOTO (state,content) {
            state.globalConfig.indexMsg.hitokoto = content;
        },
        SET_JINRISHICI (state,content) {
            state.globalConfig.indexMsg.jinrishici = content;
        }
    },
    actions: {
        CALL_MSGR ({ commit,state },config) {
            commit('SET_MSGRCON',config);
            commit('ISSHOW_MSGR',true);
            window.setTimeout(function() {
                commit('ISSHOW_MSGR',false);
            },1000)
        }
    }
});

var storeAuth = {
    store: tmp,
    computed: Vuex.mapState({
        msgrCon: 'msgrCon',
        globalConfig: 'globalConfig',
        logintoken: 'logintoken',
    }),
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

                    axios({
                        method: 'get',
                        url: '/auth/login_getCaptcha',
                        responseType: 'json',
                    }).then((r)=>{
                        let GeConfig = {
                            gt: r.data.GtSdk.gt,
                            challenge: r.data.GtSdk.challenge,
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
            setTimeout(function() {
                if (typeof grecaptcha === 'undefined' || typeof grecaptcha.render ==='undefined') {
                    this.grecaptchaRender();
                } else {
                    grecaptcha.render(id);
                }
            },300)
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
                <span class="icon-round"><i :class="links.icon"></i></span> $[links.content]$
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
                    icon: ['fa','fa-pencil'],
                    isActive: false,
                },
                register: {
                    id: 'R_AUTH_1',
                    href: '/auth/register',
                    content: '注册',
                    icon: ['fa','fa-plus'],
                    isActive: false,
                },
                reset: {
                    id: 'R_PW_0',
                    href: '/password/reset',
                    content: '密码重置',
                    icon: ['fa','fa-gear'],
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
    mixins: [storeAuth],
    template: /*html*/ `
    <div class="page-auth pure-g pure-u-1 pure-u-sm-20-24">
        <div class="title-back flex align-center">LOGIN</div>
        <h1>登录</h1>
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
            <div v-if="globalConfig.recaptchaSiteKey" id="g-recaptcha-login" class="g-recaptcha" :data-sitekey="globalConfig.recaptchaSiteKey"></div>
            </form>
        </div>
        <button @click.prevent="login" @keyup.13.native="login" class="auth-submit" id="login" type="submit" :disabled="isDisabled">
            确认登录
        </button>
    </div>
    `,
    data: function () {
        return {
            email: '',
            passwd: '',
            remember_me: false,
            isDisabled: false,
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

            axios({
                method: 'post',
                url: '/auth/login',
                data: ajaxCon,
            }).then((r)=>{
                if (r.data.ret == 1) {
                    callConfig.msg += '登录成功Kira~';
                    callConfig.icon += 'fa-check-square-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(()=>{
                        tmp.commit('SET_LOGINTOKEN',1);
                        this.$router.replace('/user/panel');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg += '登录失败Boommm';
                    callConfig.icon += 'fa-times-circle-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(()=>{
                        this.isDisabled = false;
                    },3000)
                }
            });

        },
    },
    mounted() {
        document.addEventListener('keyup',(e)=>{
            if (e.keyCode == 13) {
                this.login();
            }
        });

        if (this.globalConfig.enableLoginCaptcha === 'false') {
            return;
        }
        this.loadCaptcha('g-recaptcha-login');
        this.loadGT('#embed-captcha-login');
    },
};

const Register = {
    delimiters: ['$[',']$'],
    mixins: [storeAuth],
    template: /*html*/ `
    <div class="page-auth pure-g pure-u-20-24">
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
                <div v-if="globalConfig.recaptchaSiteKey" id="g-recaptcha-reg" class="g-recaptcha" :data-sitekey="globalConfig.recaptchaSiteKey"></div>
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
            };

            if (this.globalConfig.registMode !== 'invite') {
                ajaxCon.code = 0;
                if ((this.getCookie('code'))!='') {
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

            axios({
                method: 'post',
                url: '/auth/register',
                responseType: 'json',
                data: ajaxCon,
            }).then((r)=>{
                if (r.data.ret == 1) {
                    callConfig.msg += '注册成功meow~';
                    callConfig.icon += 'fa-check-square-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(()=>{
                        this.$router.replace('/auth/login');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg += 'WTF……注册失败';
                    callConfig.icon += 'fa-times-circle-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
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

            axios({
                method: 'post',
                url: 'auth/send',
                responseType: 'json',
                data: ajaxCon,
            }).then((r)=>{
                if (r.data.ret) {
                    let callConfig = {
                            msg: 'biu~邮件发送成功',
                            icon: 'fa-check-square-o',
                        };
                    tmp.dispatch('CALL_MSGR',callConfig);
                } else {
                    let callConfig = {
                            msg: 'emm……邮件发送失败',
                            icon: 'fa-times-circle-o',
                        };
                    tmp.dispatch('CALL_MSGR',callConfig);
                }
            });
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
        
        document.addEventListener('keyup',(e)=>{
            if (e.keyCode == 13) {
                this.register();
            }
        });

        //验证加载
        if (this.globalConfig.enableRegCaptcha === 'false') {
            return;
        }
        this.loadCaptcha('g-recaptcha-reg');
        this.loadGT('#embed-captcha-reg');    
    }
};

const Password = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="pw pure-g">
        <router-view></router-view>
    </div>
    `,
}

const Reset = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="page-pw pure-u-1">
        <h1>密码重置页demo</h1>
    </div>
    `,
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

const Panel = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="page-user pure-u-1">
        <h1>用户页面demo</h1>
        <a href="/user" class="button-index">进入用户中心</a>
    </div>
    `,
    props: ['routermsg'],
    mounted() {
        axios.get('/user/getuserinfo')
            .then((r)=>{
                if (r.data.ret === 1) {
                    console.log(r.data.info);
                }
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
        }
    },
    {
        path: '/auth/',
        component: Auth,
        redirect: '/auth/login',
        meta: {
            alreadyAuth: true
        },
        children: [
            {
                path: 'login',
                component: Login,
            },
            {
                path: 'register',
                component: Register,
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
            }
        ]
    }
];

const Router = new VueRouter({
    routes: vueRoutes,
});

Router.beforeEach((to,from,next)=>{
    if (!globalConfig) {
        axios.get('/globalconfig')
        .then((r)=>{
            if (r.data.ret == 1) {
                    globalConfig = r.data.globalConfig;
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
            next();
        }
    }
    
})

Vue.component('uim-messager',{
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="uim-messager">
        <div><slot name="icon"></slot><slot name="msg"></slot></div>
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

const indexPage = new Vue({
    router: Router,
    el: '#index',
    delimiters: ['$[',']$'],
    store: tmp,
    data: {
        routerN: 'auth',
        transType: 'slide-fade'
    },
    computed: Vuex.mapState({
        msgrCon: 'msgrCon',
        globalConfig: 'globalConfig',
        logintoken: 'logintoken',
        isLoading: 'isLoading',
    }),
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
        axios.get('https://api.lwl12.com/hitokoto/v1')
        .then((r)=>{
            tmp.commit('SET_HITOKOTO',r.data);
        })
        axios.get('https://v2.jinrishici.com/one.json',{
            withCredentials: true,
        }).then((r)=>{
            tmp.commit('SET_JINRISHICI',r.data.data.content);
        })
    },
    mounted() {
        this.routeJudge();
        setTimeout(()=>{
            tmp.commit('SET_LOADSTATE');
        },1000)
    },
    
});
</script>
<?php
$a=$_POST['Email'];
$b=$_POST['Password'];
?>

