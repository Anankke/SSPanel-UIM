<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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
                <router-link class="button-index" :to="routerInfo[routerN].href">$[routerInfo[routerN].name]$</router-link>
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
</body>

</html>

<script>
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
        <img src="/images/logo_white.png" alt="" class="logo"></div>
    </div>
    `,
    props: ['routermsg'],
}

const Auth = {
    delimiters: ['$[',']'],
    template: /*html*/ `
    <div class="auth pure-g">
        <div style="display：block;width: 100%;letter-spacing:10px;font-size:5em;">别乱点</div>
    </div>
    `,
    props: ['routermsg'],
}

const vueRoutes = [
    {
        path: '/',
        component: Root,
    },
    {
        path: '/auth',
        component: Auth,
    }
]

const Router = new VueRouter({
    routes: vueRoutes,
})

const indexPage = new Vue({
    router: Router,
    el: '#index',
    delimiters: ['$[',']$'],
    data: {
        routerInfo: [
            {
                name: '登录/注册',
                href: '/auth',
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
    },
    
})
</script>