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
                <router-link v-if="!parseInt(isLogin)" class="button-index" :to="routerInfo[routerN].href">$[routerInfo[routerN].name]$</router-link>
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
    {if $recaptcha_sitekey != null}
    <script src="https://recaptcha.net/recaptcha/api.js" async defer></script>
    {/if}
</body>

</html>

{if $geetest_html != null}
    <script>
        var handlerEmbed = function(captchaObj) {
            // 将验证码加到id为captcha的元素里

            // captchaObj.onSuccess(function () {
            //      validate = captchaObj.getValidate();
            // });

            captchaObj.appendTo("#embed-captcha");

            captcha = captchaObj;
            // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
        };
    </script>
{/if}

<script>

var store = {
    data: function() {
        return {
            isLogin: '{$user->isLogin}',
            captchaProvider: '{$config["captcha_provider"]}',
            recaptchaSiteKey: '{$recaptcha_sitekey}',
            jumpDelay: '{$config["jump_delay"]}',
            isGetestSuccess: '{if $geetest_html && $geetest_html->success}1{else}0{/if}',
            registMode: '{$config["register_mode"]}',
            isEmailVeryify: '{$config["enable_email_verify"]}',
        }
    },
    mounted() {
        if (parseInt(this.isLogin)) {
            this.$router.replace('/');
        }
    },
}

var storeAuth = {
    mounted() {

    if (parseInt(this.isLogin)) {
        return;
    }


    if (this.recaptchaSiteKey !== '' && tmp.state.time !== 1) {
        this.$nextTick(function(){
            grecaptcha.render('g-recaptcha');
        })
    }

    tmp.setTmp('time',2);

    if (this.captchaProvider === 'geetest') {
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

                if (parseInt(this.isGetestSuccess)) {
                    GeConfig.offline = 0;
                } else {
                    GeConfig.offline = 1;
                }

                initGeetest(GeConfig, handlerEmbed);

            });

        });
    }
    
    },
}

var tmp = {
    state: {
        time: 1,
    },
    setTmp(key,newValue) {
        this.state[key] = newValue;
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
        <div class="pure-u-5-24">
            <router-link class="button-round" to="/auth/login">登录</router-link>
            <router-link class="button-round" to="/auth/register">注册</router-link>
        </div>
        <router-view></router-view>
    </div>
    `,
    props: ['routermsg'],
};

const Login = {
    delimiters: ['$[',']'],
    mixins: [store,storeAuth],
    template: /*html*/ `
    <div class="page-login pure-g pure-u-19-24">
        <h1>登录</h1>
        <div class="input-control">
            <label for="Email">邮箱</label>
            <input v-model="email" type="text" name="Email">        
        </div>
        <div class="input-control">
            <label for="Password">密码</label>
            <input v-model="passwd" type="password" name="Password">        
        </div>
        <div class="input-control">
            <div v-if="captchaProvider === 'geetest'" id="embed-captcha"></div>
            <form action="?" method="POST">    
            <div v-if="recaptchaSiteKey" id="g-recaptcha" class="g-recaptcha" data-sitekey="{$recaptcha_sitekey}"></div>
            </form>
        </div>
        <button @click="login" class="auth-submit" id="login" type="submit" :disabled="isDisabled">
            确认登录
        </button>
    </div>
    `,
    data: function () {
        return {
            email: '',
            passwd: '',
            isDisabled: false,
        }
    },
    methods: {
        login() {

            this.isDisabled = true;

            let ajaxCon = {
                email: this.email,
                passwd: this.passwd,
            };

            if (this.recaptchaSiteKey !== '') {
                ajaxCon.recaptcha = grecaptcha.getResponse();
            }

            if (this.captchaProvider === 'geetest' && captcha.getValidate()) {
                let validate = captcha.getValidate();
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
                    window.setTimeout("location.href='/user'", this.jumpDelay);
                } else {
                    this.isDisabled = false;
                    console.log(r.data.ret);
                }
            });

        },
    },
};

const Register = {
    delimiters: ['$[',']'],
    mixins: [store,storeAuth],
    template: /*html*/ `
    <div class="page-login pure-g pure-u-19-24">
        <h1>账号注册</h1>
        <div class="input-control">
            <label for="usrname">昵称</label>
            <input v-model="usrname" type="text" name="usrname">        
        </div>
        <div class="input-control">
            <label for="email">邮箱(唯一凭证请认真对待)</label>
            <input v-model="email" type="text" name="email">        
        </div>
        <div class="input-control">
            <label for="password">密码</label>
            <input v-model="passwd" type="password" name="password">        
        </div>
        <div class="input-control">
            <label for="repasswd">重复密码</label>
            <input v-model="repasswd" type="password" name="repasswd">        
        </div>
        <div class="input-control">
            <label for="imtype">选择您的联络方式</label>
            <select v-model="imtype" name="imtype" id="imtype">
                <option value="1">微信</option>
                <option value="2">QQ</option>
                <option value="3">Facebook</option>
                <option value="4">Telegram</option>
            </select>        
        </div>
        <div class="input-control">
            <label for="contect">联络方式账号</label>
            <input v-model="contect" type="text" name="contect">        
        </div>
        <div v-if="registMode === 'invite'" class="input-control">
            <label for="code">邀请码(必填)</label>
            <input v-model="code" type="text" name="code">        
        </div>
        <div v-if="isEmailVeryify === 'true'" class="input-control">
            <label for="email_code">邮箱验证码</label>
            <input v-model="email_code" type="text" name="email_code">
            <button class="auth-submit">获取邮箱验证码</button>    
        </div>
        <div class="input-control">
            <div v-if="captchaProvider === 'geetest'" id="embed-captcha"></div>
            <form action="?" method="POST">    
            <div v-if="recaptchaSiteKey" id="g-recaptcha" class="g-recaptcha" data-sitekey="{$recaptcha_sitekey}"></div>
            </form>
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
        }
    },
    methods: {
        register() {

            let ajaxCon = {
                    email: this.email,
                    name: this.usrname,
                    passwd: this.passwd,
                    repasswd: this.repasswd,
                    wechat: this.contect,
                    imtype: this.imtype,
                    code: this.code,
                };

            if (registMode !== 'invite') {
                ajaxCon.code = 0;
                if ((getCookie('code'))!='') {
                    ajaxCon.code = getCookie('code');
                }
            }

            if (this.recaptchaSiteKey !== '') {
                ajaxCon.recaptcha = grecaptcha.getResponse();
            }

            if (this.captchaProvider === 'geetest' && captcha.getValidate()) {
                let validate = captcha.getValidate();
                ajaxCon.geetest_challenge = validate.geetest_challenge;
                ajaxCon.geetest_validate = validate.geetest_validate;
                ajaxCon.geetest_seccode = validate.geetest_seccode;
            }

            axios({
                method: 'post',
                url: '/auth/register',
                responseType: 'json',
                data: ajaxCon,
            }).then((r)=>{
                if (r.data.ret == 1) {
                    console.log(r.data.ret);
                    window.setTimeout("location.href='#/auth/login'", this.jumpDelay);
                } else {
                    this.isDisabled = false;
                    console.log(r.data.ret);
                }
            });
        }
    }
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
                meta: {
                    keepAlive: true,
                }
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
    mixins: [store],
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
    },
    methods: {
        routeJudge() {
            if (this.$route.path === '/') {
                this.routerN = 0;
            } else {
                this.routerN = 1;                
            }
        },
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
        tmp.setTmp('time',2);
    },
    
});
</script>

