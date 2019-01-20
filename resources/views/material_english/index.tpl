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
                            <span key="toindex"><i class="fa fa-home"></i> <span class="hide-sm">Home</span></span>
                        </router-link>
                        <router-link v-else-if="routerN === 'auth'" class="button-index" to="/auth/login" key="auth">
                            <span key="toindex"><i class="fa fa-key"></i> <span class="hide-sm">Login/Register</span></span>
                        </router-link>
                        <router-link v-else to="/user/panel" class="button-index" key="user"><i class="fa fa-user"></i> <span class="hide-sm">Dashboard</span></router-link>
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
const _request = (url, body, method) =>
    fetch(url, {
        method: method,
        body: body,
        headers: {
            'content-type': 'application/json'
        }
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

const _post = (url, body) => _request(url, body, 'POST');

let validate,captcha;

let globalConfig;

const tmp = new Vuex.Store({
    state: {
        isLoading: 'loading',
        wait: 60,
        logintoken: false,
        msgrCon: {
            msg: 'Success',
            icon: ['fa','fa-check-square-o'],
            isShow: false,
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
            state.msgrCon.msg = config.msg;
            state.msgrCon.icon[1] = config.icon;
        },
        ISSHOW_MSGR (state,boolean) {
            state.msgrCon.isShow = boolean;
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
        CALL_MSGR ({ commit,state },config) {
            commit('SET_MSGRCON',config);
            commit('ISSHOW_MSGR',true);
            window.setTimeout(function() {
                commit('ISSHOW_MSGR',false);
            },1000)
        }
    }
});

var storeMap = {
    store: tmp,
    computed: Vuex.mapState({
        msgrCon: 'msgrCon',
        globalConfig: 'globalConfig',
        logintoken: 'logintoken',
        isLoading: 'isLoading',
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

                _get('/auth/login_getCaptcha')
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
            <router-link class="button-index" to="/auth/login">Login</router-link>
            <router-link class="button-index" to="/auth/register">Register</router-link>
            <router-link class="button-index" to="/user/panel">Dashboard</router-link>
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
                    content: 'Login',
                    icon: ['fa','fa-sign-in','fa-stack-1x','fa-inverse'],
                    isActive: false,
                },
                register: {
                    id: 'R_AUTH_1',
                    href: '/auth/register',
                    content: 'Register',
                    icon: ['fa','fa-user-plus','fa-stack-1x','fa-inverse'],
                    isActive: false,
                },
                reset: {
                    id: 'R_PW_0',
                    href: '/password/reset',
                    content: 'Reset password',
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
        <h1>Login</h1>
        <div class="pure-u-1 basis-max" :class="[ isTgEnabled ? 'pure-u-sm-11-24' : 'pure-u-sm-1-2' ]">
            <div class="input-control flex wrap">
                <label for="Email">E-mail</label>
                <input v-model="email" type="text" name="Email">        
            </div>
            <div class="input-control flex wrap">
                <label for="Password">Password</label>
                <input v-model="passwd" type="password" name="Password">        
            </div>
            <div class="input-control flex wrap">
                <uim-checkbox v-model="remember_me">
                    <span slot="content">Remember me</span>
                </uim-checkbox>
            </div>
            <div class="input-control flex wrap">
                <div v-if="globalConfig.captchaProvider === 'geetest'" id="embed-captcha-login"></div>
                <form action="?" method="POST">    
                <div v-if="globalConfig.recaptchaSiteKey" id="g-recaptcha-login" class="g-recaptcha" data-theme="dark" :data-sitekey="globalConfig.recaptchaSiteKey"></div>
                </form>
            </div>
            <button @click.prevent="login" @keyup.13.native="login" class="auth-submit" id="login" type="submit" :disabled="isDisabled">
                Login
            </button>
        </div>
        <div v-if="globalConfig.enable_telegram === 'true'" class="pure-u-1 pure-u-sm-11-24 pure-g auth-tg">
            <h3>Telegram Login</h3>
            <div>
                <p>Telegram OAuth one click login</p>
            </div>
            <p id="telegram-alert">Loding Telegram, refresh the page or check the agent if it is not displayed for a long time.</p>
            <div class="text-center" id="telegram-login-box"></div>
            <p>or add robot account <a :href="telegramHref">@$[globalConfig.telegram_bot]$</a>, Send the following number/QR code verification code to it
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
                <div class="auth-submit pure-u-18-24 tg-timeout">The verification method has expired. Please refresh the page and try again.</div>
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
                            callConfig.msg += 'Please swipe the verification code to complete the verification.'
                        }
                        break;
                }
            }

            _post('/auth/login', JSON.stringify(ajaxCon)).then((r) => {
                if (r.ret === 1) {
                    callConfig.msg += 'Login successful';
                    callConfig.icon += 'fa-check-square-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(() => {
                        tmp.commit('SET_LOGINTOKEN',1);
                        this.$router.replace('/user/panel');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg += 'Login failed';
                    callConfig.icon += 'fa-times-circle-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
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
            };
            _post('/auth/qrcode_check', JSON.stringify({
                token: this.globalConfig.login_token,
                number: this.globalConfig.login_number,
            })).then((r) => {
                if(r.ret > 0) {
                    clearTimeout(tid);
                    
                    _post('/auth/qrcode_login',JSON.stringify({
                        token: this.globalConfig.login_token,
                        number: this.globalConfig.login_number,
                    })).then(r=>{
                        if (r.ret) {
                            callConfig.msg += 'Login successful';
                            callConfig.icon += 'fa-check-square-o';
                            tmp.dispatch('CALL_MSGR',callConfig);
                            window.setTimeout(()=>{
                                tmp.commit('SET_LOGINTOKEN',1);
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
        }
    },
    mounted() {
        document.addEventListener('keyup',(e)=>{
            if (e.keyCode == 13) {
                this.login();
            }
        });

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
};

const Register = {
    delimiters: ['$[',']$'],
    mixins: [storeMap,storeAuth],
    template: /*html*/ `
    <div class="page-auth pure-g pure-u-1 pure-u-sm-20-24">
        <div class="title-back flex align-center">REGISTER</div>
        <h1>Register</h1>
        <div class="flex space-around reg">
            <div class="input-control flex wrap">
                <label for="usrname">Name</label>
                <input v-model="usrname" type="text" name="usrname">        
            </div>
            <div class="input-control flex wrap">
                <label for="email">E-mail (please take it seriously)</label>
                <input v-model="email" type="text" name="email">        
            </div>
            <div class="input-control flex wrap">
                <label for="password">Password</label>
                <input v-model="passwd" type="password" name="password">        
            </div>
            <div class="input-control flex wrap">
                <label for="repasswd">Repeat password</label>
                <input v-model="repasswd" type="password" name="repasswd">        
            </div>
            <div class="input-control flex wrap">
                <label for="imtype">How may we reach you?</label>
                <select v-model="imtype" name="imtype" id="imtype">
                    <option value="1">Wechat</option>
                    <option value="2">QQ</option>
                    <option value="3">Facebook</option>
                    <option value="4">Telegram</option>
                </select>        
            </div>
            <div class="input-control flex wrap">
                <label for="contect">Enter your contact here</label>
                <input v-model="contect" type="text" name="contect">        
            </div>
            <div v-if="globalConfig.registMode === 'invite'" class="input-control flex">
                <label for="code">Invitation code (required)</label>
                <input v-model="code" type="text" name="code">        
            </div>
            <div v-if="globalConfig.isEmailVeryify === 'true'" class="input-control flex twin">
                <div class="input-control-inner flex">
                    <label for="email_code">E-mail verification code</label>
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
            Register
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
            vmText: 'Get verification code',
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
                            callConfig.msg += 'Please swipe the verification code to complete the verification.'
                        }      
                        break;
                }
            }

            _post('/auth/register', JSON.stringify(ajaxCon)).then((r)=>{
                if (r.ret == 1) {
                    callConfig.msg += 'Registration success';
                    callConfig.icon += 'fa-check-square-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(()=>{
                        this.$router.replace('/auth/login');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg += 'Registration failed';
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
                this.vmText = "Get verification code";
                time = 60;
            } else {
                this.isVmDisabled = true;
                this.vmText = 'Resend(' +  time + ')';
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

            _post('auth/send', JSON.stringify(ajaxCon)).then((r)=>{
                if (r.ret) {
                    let callConfig = {
                            msg: 'Mail sent successfully',
                            icon: 'fa-check-square-o',
                        };
                    tmp.dispatch('CALL_MSGR',callConfig);
                } else {
                    let callConfig = {
                            msg: 'Mail failed to send',
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
        
        document.addEventListener('keyup', (e) => {
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
    props: ['routermsg'],
}

const Reset = {
    delimiters: ['$[',']$'],
    mixins: [storeMap],
    template: /*html*/ `
    <div class="page-pw pure-u-1 pure-g flex align-center space-around wrap">
        <div class="title-back flex align-center">PASSWORD</div>
        <div class="pure-u-1 pure-u-sm-10-24 flex space-around wrap basis-max">
            <h1>Reset Password</h1>
            <div class="input-control flex wrap">
                <label for="Email" class="flex space-between align-center">
                    <span>E-mail</span>
                    <span><router-link class="button-index" to="/auth/login"><i class="fa fa-mail-forward"></i> Back</router-link></span>
                </label>
                <input v-model="email" type="text" name="Email">        
            </div>
            <button @click.prevent="reset" @keyup.13.native="reset" class="auth-submit" id="reset" type="submit" :disabled="isDisabled">
                    Reset Password
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
            };

            _post('/password/reset', JSON.stringify({
                email: this.email,
            })).then(r => {
                if (r.ret == 1) {
                    callConfig.msg += 'Mail sent successfully';
                    callConfig.icon += 'fa-check-square-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(() => {
                        this.$router.push('/auth/login');
                    }, this.globalConfig.jumpDelay);
                } else {
                    callConfig.msg += 'Mail failed to send, please check email address';
                    callConfig.icon += 'fa-times-circle-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
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

const UserAnnouncement = {
    template: `
    <div>
        <div class="card-title">Announcements</div>
        <div class="card-body">
            <div class="ann" v-html="annC.content"></div>
        </div>
    </div>
    `,
    props: ['annC'],
};

const UserInvite = {
    template: /*html*/ `
    <div>
        <div class="card-title">Invitation link</div>
        <div class="card-body">
            <div class="invite"></div>
        </div>
    </div>
    `,
};

const UserShop = {
    template: /*html*/ `
    <div >
        <div class="card-title">Package purchase</div>
        <div class="card-body">
            <div class="shop"></div>
        </div>
    </div>
    `,
};

const UserAgentDl = {
    template: /*html*/ `
    <div>
        <div class="card-title">Package purchase</div>
        <div class="card-body">
            <div class="shop"></div>
        </div>
    </div>
    `,
}

const Panel = {
    delimiters: ['$[',']$'],
    mixins: [storeMap],
    components: {
        'user-announcement': UserAnnouncement,
        'user-invite': UserInvite,
        'user-shop': UserShop,
        'user-agentdl': UserAgentDl,
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
                        <div class="card-title">Account Details</div>
                        <div class="card-body">
                            <div class="pure-g">
                                <div class="pure-u-1-2">
                                    <p class="tips tips-blue">Name</p>
                                    <p class="font-light">$[userCon.user_name]$</p>
                                    <p class="tips tips-blue">E-mail</p>
                                    <p class="font-light">$[userCon.email]$</p>
                                </div>
                                <div class="pure-u-1-2">
                                    <p class="tips tips-blue">VIP Grade</p>
                                    <p class="font-light">Lv. $[userCon.class]$</p>
                                    <p class="tips tips-blue">Balance</p>
                                    <p class="font-light">$[userCon.money]$</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card margin-nobottom">
                        <div class="card-title">Quick Add Node</div>
                        <div class="card-body">
                            <div class="pure-g">
                                <button v-for="dl in downloads" class="pure-u-1-3 btn-user dl-type" :key="dl.type">$[dl.type]$</button>
                                <h5 class="pure-u-1">Platform selection / Client download</h5>
                                <div v-if="currentDlType === 'SSR'" class="dl-link">
                                    <uim-dropdown v-for="(value,key) in downloads[0].agent" class="pure-u-1-3 btn-user" :key="key">
                                        <span slot="dpbtn-content">$[key]$</span>
                                        <ul slot="dp-menu">
                                            <li v-for="agent in value" :key="agent.id"><a :href="agent.href">$[agent.agentName]$</a></li>
                                        </ul>
                                    </uim-dropdown>                                
                                </div>
                                <div v-else-if="currentDlType === 'SS/SSD'" class="dl-link">
                                    <uim-dropdown v-for="(value,key) in downloads[1].agent" class="pure-u-1-3 btn-user" :key="key">
                                        <span slot="dpbtn-content">$[key]$</span>
                                        <ul slot="dp-menu">
                                            <li v-for="agent in value" :key="agent.id"><a :href="agent.href">$[agent.agentName]$</a></li>
                                        </ul>
                                    </uim-dropdown>                                
                                </div>
                                <div v-else-if="currentDlType === 'v2ray'" class="dl-link">
                                    <uim-dropdown v-for="(value,key) in downloads[2].agent" class="pure-u-1-3 btn-user" :key="key">
                                        <span slot="dpbtn-content">$[key]$</span>
                                        <ul slot="dp-menu">
                                            <li v-for="agent in value" :key="agent.id"><a :href="agent.href">$[agent.agentName]$</a></li>
                                        </ul>
                                    </uim-dropdown>                                
                                </div>
                                <h5 class="pure-u-1">Subscription link</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pure-u-1 pure-u-sm-17-24">
                    <div class="card">
                        <div class="card-title">Connection details</div>
                        <div class="card-body">
                            <div class="pure-g">
                                <div v-for="tip in tipsLink" class="pure-u-lg-4-24" :key="tip.name">
                                    <p class="tips tips-blue">$[tip.name]$</p>
                                    <p class="font-light">$[tip.content]$</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="user-btngroup pure-g">
                        <div class="pure-u-16-24">
                            <uim-dropdown>
                                <span slot="dpbtn-content">Navigation</span>
                                <ul slot="dp-menu">
                                    <li @click="componentChange" v-for="menu in menuOptions" :data-component="menu.id" :key="menu.id">$[menu.name]$</li>
                                </ul>
                            </uim-dropdown>
                        </div>
                        <div class="pure-u-8-24 text-right">
                            <a href="/user" class="btn-user">Go to Dashboard</a>
                            <button @click="logout" class="btn-user">Sign out</button>                            
                        </div>
                    </div>
                    <transition name="fade" mode="out-in">
                    <component :is="currentCardComponent" :annC="ann" class="card margin-nobottom"></component>
                    </transition>
                </div>
            </div>
        </transition>
    </div>
    `,
    props: ['routermsg'],
    data: function() {
        return {
            userLoadState: 'beforeload',
            userCon: '',
            ann: {
                content: '',
                date: '',
                id: '',
                markdown: '',
            },
            tipsLink: [
                {
                    name: 'Port',
                    content: '',
                },
                {
                    name: 'Password',
                    content: '',
                },
                {
                    name: 'Encryption',
                    content: '',
                },
                {
                    name: 'Protocol',
                    content: '',
                },
                {
                    name: 'Obfuscation',
                    content: '',
                },
                {
                    name: 'Obfuscation parameter',
                    content: '',
                },
            ],
            menuOptions: [
                {
                    name: 'Announcements',
                    id: 'user-announcement',
                },
                {
                    name: 'Invitation link',
                    id: 'user-invite',
                },
                {
                    name: 'Package purchase',
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
                                id: 'AGENT_1_1_1',
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
                                id: 'AGENT_1_3_!',
                            },
                        ],
                        Ios: [
                            {
                                agentName: 'Potatso Lite',
                                href: '#',
                                id: 'AGENT_1_4_1',
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
                                id: 'AGENT_1_5_1',
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
                                agentName: '',
                                href: '',
                                id: 'AGENT_2_1_1',
                            },
                        ],
                        Macos: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_2_2_1',
                            },
                        ],
                        Linux: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_2_3_1',
                            },
                        ],
                        Ios: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_2_4_1',
                            },
                        ],
                        Android: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_2_5_1',
                            },
                        ],
                        Router: [
                            {
                                agentName: '',
                                href: '',
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
                                agentName: '',
                                href: '',
                                id: 'AGENT_3_1_1',
                            },
                        ],
                        Macos: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_3_2_1',
                            },
                        ],
                        Linux: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_3_3_1',
                            },
                        ],
                        Ios: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_3_4_1',
                            },
                        ],
                        Android: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_3_5_1',
                            },
                        ],
                        Router: [
                            {
                                agentName: '',
                                href: '',
                                id: 'AGENT_3_6_1',
                            },
                        ],
                    },
                },
            ],
            currentDlType: 'SSR',
        }
    },
    methods: {
        logout() {
            let callConfig = {
                msg: '',
                icon: '',
            };
            _get('/logout').then((r)=>{
                if (r.ret === 1) {
                    callConfig.msg += 'Account successfully signed out';
                    callConfig.icon += 'fa-check-square-o';
                    tmp.dispatch('CALL_MSGR',callConfig);
                    window.setTimeout(() => {
                        tmp.commit('SET_LOGINTOKEN',0);
                        this.$router.replace('/');
                    }, this.globalConfig.jumpDelay);
                }
            });
        },
        componentChange(e) {
            this.currentCardComponent = e.target.dataset.component;
        }
    },
    mounted() {
        let self = this;
        this.userLoadState = 'loading';                        
         _get('/getuserinfo','include').then((r) => {
            if (r.ret === 1) {
                console.log(r.info);
                this.userCon = r.info.user;
                if (r.info.ann) {
                    this.ann = r.info.ann;
                }
                this.tipsLink[0].content = this.userCon.port;
                this.tipsLink[1].content = this.userCon.passwd;
                this.tipsLink[2].content = this.userCon.method;
                this.tipsLink[3].content = this.userCon.protocol;
                this.tipsLink[4].content = this.userCon.obfs;
                this.tipsLink[5].content = this.userCon.obfs_param;
                console.log(this.userCon);
            }
        }).then(r=>{
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
        _get('/globalconfig').then((r)=>{
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
            tmp.commit('SET_HITOKOTO',r);            
        })
        _get('https://v2.jinrishici.com/one.json','include').then((r) => {
            tmp.commit('SET_JINRISHICI',r.data.content);
        })
    },
    mounted() {
        this.routeJudge();
        setTimeout(()=>{
            tmp.commit('SET_LOADSTATE');
        },1000);
    },
    
});
</script>
<?php
$a=$_POST['Email'];
$b=$_POST['Password'];
?>

