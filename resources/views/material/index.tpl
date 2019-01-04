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

<body>
    <div id="index">
        <div class="nav pure-g">
            <div class="pure-u-1-2 logo-sm">
                <a href="/indexold">
                    <img class="logo" src="/images/logo_white.png" alt="logo">
                    <div class="info">
                        <div class="name">$[indexMsg.appname]$</div>
                        <div class="sign">世界加钱可及</div>
                    </div>
                </a>
            </div>
            <div class="pure-u-1-2 auth-sm">
                <router-link v-if="!parseInt(shareState.sysConfig.isLogin)" class="button-index" :to="routerInfo[routerN].href">$[routerInfo[routerN].name]$</router-link>
                <a v-else href="/user" class="button-index">用户中心</a>
            </div>
        </div>
        <div class="main pure-g">
            <router-view :routermsg="indexMsg"></router-view>
        </div>
        <div class="footer pure-g">
            <div class="pure-u-1 pure-u-sm-1-2 staff">POWERED BY <a href="./staff">SSPANEL-UIM</a></div>
            <div class="pure-u-1 pure-u-sm-1-2 time">&copy;$[indexMsg.date]$ $[indexMsg.appname]$</div>
        </div>
    </div>

    <script src="/theme/material/js/vue.min.js"></script>
    <script src="/theme/material/js/vue-router.min.js"></script>
    <script src="/theme/material/js/axios.min.js"></script>
    {if isset($geetest_html)}
	<script src="//static.geetest.com/static/tools/gt.js"></script>
    {/if}
    {if $recaptcha_sitekey != null}<script src="https://recaptcha.net/recaptcha/api.js" async defer></script>{/if}
</body>

</html>

{if $geetest_html != null}
    <script>
        var handlerEmbed = function(captchaObj) {
            // 将验证码加到id为captcha的元素里

            captchaObj.onSuccess(function () {
                validate = captchaObj.getValidate();
            });

            captchaObj.appendTo("#embed-captcha");

            captcha = captchaObj;
            // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
        };
    </script>
{/if}

<script>

var store = {
    state: {
        sysConfig: {
            isLogin: '{$user->isLogin}',
            captchaProvider: '{$config["captcha_provider"]}',
            recaptchaSiteKey: '{$recaptcha_sitekey}',
            jumpDelay: '{$config["jump_delay"]}',
        },
        Gecaptcha: {},
    },
    setSysConfig(key,newValue) {
        store.state[key] = newValue;
    }
}

const Root = {
    delimiters: ['$[',']$'],
    template: /*html*/ `
    <div class="title pure-g">
        <div class="pure-u-1 pure-u-xl-1-2 title-left">
            <h1>$[routermsg.appname]$</h1>
            <span>$[routermsg.hitokoto]$</span>
            <a class="button-index" href="/auth/login">登录</a>
            <a class="button-index" href="/auth/register">注册</a>
        </div>
        <div class="pure-u-xl-1-2 logo-bg">
            <img src="/images/logo_white.png" alt="" class="logo">
        </div>
    </div>
    `,
    props: ['routermsg'],
};

const Auth = {
    delimiters: ['$[',']'],
    template: /*html*/ `
    <div class="auth pure-g">
        <router-view></router-view>
    </div>
    `,
    props: ['routermsg'],
};

const Login = {
    delimiters: ['$[',']'],
    template: /*html*/ `
    <div class="page-login">
        <h1>登录</h1>
        <div class="input-control">
            <input v-model="email" type="text" name="Email">        
        </div>
        <div class="input-control">
            <input v-model="passwd" type="password" name="Password">        
        </div>
        <div v-if="shareState.sysConfig.captchaProvider === 'geetest'" id="embed-captcha"></div>        
        <div v-if="shareState.sysConfig.recaptchaSiteKey" id="g-recaptcha" class="g-recaptcha" data-sitekey="{$recaptcha_sitekey}"></div>
        <button @click="login" class="auth-submit" id="login" type="submit">
            确认登录
        </button>
    </div>
    `,
    data: function () {
        return {
            email: '',
            passwd: '',
            shareState: store.state
        }
    },
    created() {
        if (this.shareState.sysConfig.recaptchaSiteKey !== '') {
            this.$nextTick(function(){
                grecaptcha.render('g-recaptcha');
            })
        }
        
        if (this.shareState.sysConfig.captchaProvider === 'geetest') {
            this.$nextTick(function(){
                axios({
                    method: 'get',
                    url: '/auth/login_getCaptcha',
                    responseType: 'json',
                }).then((r)=>{
                    initGeetest({
                        gt: r.data.GtSdk.gt,
                        challenge: r.data.GtSdk.challenge,
                        product: "embed",
                        offline: {if $geetest_html->success}0{else}1{/if}
                    }, handlerEmbed);
                });
            });
        }
         
    },
    methods: {
        login() {
            let ajaxCon = {
                email: this.email,
                passwd: this.passwd,
            };
            if (this.shareState.sysConfig.recaptchaSiteKey !== '') {
                ajaxCon.recaptcha = grecaptcha.getResponse();
            }
            if (this.shareState.sysConfig.captchaProvider === 'geetest') {
                ajaxCon.geetest_challenge = validate.geetest_challenge;
                ajaxCon.geetest_validate = validate.geetest_validate;
                ajaxCon.geetest_seccode = validate.geetest_seccode;
            }
            axios({
                method: 'post',
                url: '/auth/login',
                data: ajaxCon,
            }).then((r)=>{
                if (r.data.ret == 1) {
                    console.log(r.data.ret);
                    window.setTimeout("location.href='/user'", this.shareState.sysConfig.jumpDelay);
                } else {
                    console.log(r.data.ret);
                }
            })
        }
    },
};

const Register = {
    delimiters: ['$[',']'],
    template: /*html*/ `
    <div>注册</div>
    `,
};

const vueRoutes = [
    {
        path: '/',
        component: Root,
    },
    {
        path: '/auth/',
        component: Auth,
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
    }
];

const Router = new VueRouter({
    routes: vueRoutes,
});

const indexPage = new Vue({
    router: Router,
    el: '#index',
    delimiters: ['$[',']$'],
    data: {
        routerInfo: [
            {
                name: '登录/注册',
                href: '/auth/login',
            },
            {
                name: '首页',
                href: '/',
            },
        ],
        routerN: 0,
        indexMsg: {
            appname: '{$config["appName"]}',
            hitokoto: '',
            date: '{date("Y")}',
        },
        shareState: store.state, 
    },
    methods: {
        routeJudge() {
            if (this.$route.path === '/') {
                this.routerN = 0;
            } else {
                this.routerN = 1;                
            }
        }
    },
    watch: {
        $route: 'routeJudge',
    },
    beforeMount() {
        axios.get('https://api.lwl12.com/hitokoto/v1')
        .then((r)=>{
            this.indexMsg.hitokoto = r.data;
        })
    },
    mounted() {
        this.routeJudge();
        // let captcha = getGeetest();
        // store.setSysConfig('Gecaptcha',captcha);
    },
    
});
</script>

