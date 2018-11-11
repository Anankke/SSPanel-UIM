<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<meta name="theme-color" content="#ff9800">
	<title>{$config["appName"]}</title>


	<!-- css -->
	<link href="/theme/material/css/base.min.css" rel="stylesheet">
	<link href="/theme/material/css/project.min.css" rel="stylesheet">
	<link href="https://fonts.loli.net/css?family=Roboto:300,300italic,400,400italic,500,500italic" rel="stylesheet">
	<link href="https://fonts.loli.net/css?family=Material+Icons" rel="stylesheet">

	<!-- jquery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
 <style>
    body {
        background: #eee;
    }

    @keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            transform: rotate(1080deg);
        }
        100% {
            transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-webkit-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -webkit-transform: rotate(1080deg);
        }
        100% {
            -webkit-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-moz-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -moz-transform: rotate(1080deg);
        }
        100% {
            -moz-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-ms-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -ms-transform: rotate(1080deg);
        }
        100% {
            -ms-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-o-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -o-transform: rotate(1080deg);
        }
        100% {
            -o-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    .roll {
        position: absolute;
        opacity: 0;
        animation: mysnow 5s;
        -webkit-animation: mysnow 5s;
        -moz-animation: mysnow 5s;
        -ms-animation: mysnow 5s;
        -o-animation: mysnow 5s;
        height: 80px;
    }

    .div {
        position: fixed;
    }
    </style>



	<!-- favicon -->
	<!-- ... -->
	<style>
		.pagination {
			display:inline-block;
			padding-left:0;
			margin:20px 0;
			border-radius:4px
		}
		.pagination>li {
			display:inline
		}
		.pagination>li>a,.pagination>li>span {
			position:relative;
			float:left;
			padding:6px 12px;
			margin-left:-1px;
			line-height:1.42857143;
			color:#337ab7;
			text-decoration:none;
			background-color:#fff;
			border:1px solid #ddd
		}
		.pagination>li:first-child>a,.pagination>li:first-child>span {
			margin-left:0;
			border-top-left-radius:4px;
			border-bottom-left-radius:4px
		}
		.pagination>li:last-child>a,.pagination>li:last-child>span {
			border-top-right-radius:4px;
			border-bottom-right-radius:4px
		}
		.pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover {
			color:#23527c;
			background-color:#eee;
			border-color:#ddd
		}
		.pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover {
			z-index:2;
			color:#fff;
			cursor:default;
			background-color:#337ab7;
			border-color:#337ab7
		}
		.pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover {
			color:#777;
			cursor:not-allowed;
			background-color:#fff;
			border-color:#ddd
		}
		.pagination-lg>li>a,.pagination-lg>li>span {
			padding:10px 16px;
			font-size:18px
		}
		.pagination-lg>li:first-child>a,.pagination-lg>li:first-child>span {
			border-top-left-radius:6px;
			border-bottom-left-radius:6px
		}
		.pagination-lg>li:last-child>a,.pagination-lg>li:last-child>span {
			border-top-right-radius:6px;
			border-bottom-right-radius:6px
		}
		.pagination-sm>li>a,.pagination-sm>li>span {
			padding:5px 10px;
			font-size:12px
		}
		.pagination-sm>li:first-child>a,.pagination-sm>li:first-child>span {
			border-top-left-radius:3px;
			border-bottom-left-radius:3px
		}
		.pagination-sm>li:last-child>a,.pagination-sm>li:last-child>span {
			border-top-right-radius:3px;
			border-bottom-right-radius:3px
		}
		.pager {
			padding-left:0;
			margin:20px 0;
			text-align:center;
			list-style:none
		}
		.pager li {
			display:inline
		}
		.pager li>a,.pager li>span {
			display:inline-block;
			padding:5px 14px;
			background-color:#fff;
			border:1px solid #ddd;
			border-radius:15px
		}
		.pager li>a:focus,.pager li>a:hover {
			text-decoration:none;
			background-color:#eee
		}
		.pager .next>a,.pager .next>span {
			float:right
		}
		.pager .previous>a,.pager .previous>span {
			float:left
		}
		.pager .disabled>a,.pager .disabled>a:focus,.pager .disabled>a:hover,.pager .disabled>span {
			color:#777;
			cursor:not-allowed;
			background-color:#fff
		}





		.pagination>li>a,
		.pagination>li>span {
		  border: 1px solid white;
		}
		.pagination>li.active>a {
		  background: #f50057;
		  color: #fff;
		}

		.pagination>li>a {
		  background: white;
		  color: #000;
		}


		.pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
			color: #fff;
			background-color: #000;
			border-color: #000;
		}

		.pagination>.active>span {
		  background-color: #f50057;
		  color: #fff;
		  border-color: #fff;
		}



		.pagination > .disabled > span {
		  border-color: #fff;
		}


		pre {
			white-space: pre-wrap;
			word-wrap: break-word;
		}

		.progress-green .progress-bar {
			background-color: #f0231b;
		}

		.progress-green {
			background-color: #000;
		}

		.progress-green .progress-bar {
			background-color: #ff0a00;
		}

		.page-orange .ui-content-header {
			background-image: url(/theme/material/css/images/bg/amber.jpg);
		}

		.content-heading {
			font-weight: 300;
			color: #fff;
		}
		
		.reset-invitelink {
			margin-left: 1em;
		}

		.enable-flag {
			color: #383838;
		}

		.node-icon {
			color: #ff9000;
		}

		.node-alive {
			color: #474747;
		}

		.node-load,.node-mothed {
			color: #828282;
		}

		.node-band {
			color: #aaaaaa;
		}

		.node-tr {
			color: #a5a5a5;
		}

		.node-status {
			color: #c4c4c4;
		}

		.usercheck {
			text-align: center;
			width: 100%;
		}

	</style>

    <style>

.progressbar{
    position:relative;
	display:block;
    width:90%;
    height:20px;
    padding:10px 20px;
    border-bottom:1px solid rgba(255,255,255,0.25);
    border-radius:16px;
	margin:40px auto;
	margin-top: 60px;
}

.progressbar .before{
    position:absolute;
    display:block;
    content:"";
    width:calc(100% - 42px);
    height:18px;
    top:10px;
    left:20px;
    border-radius:20px;
    background:#fff;
    box-shadow: 0px 0px 2px 0px rgba(180, 180, 180, .85);
	border:1px solid rgba(222,222,222,.8);
}
.progressbar .bar {
	position:absolute;
	display:block;
	width:0px;
	height:16px;
	top:12px;
	left:22px;
	background:rgb(126,234,25);
	
	border-radius:16px;
	box-shadow:0px 0px 12px 0px rgba(126, 234, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
	overflow:hidden;
}
.progressbar .label .bar {
	position: relative;
	display: inline-block;
	top: 12%;
	left: 0;
	margin-right: 5px;
	width: 16px;
	
}
.progressbar .label .bar.color {
	box-shadow:0px 0px 5px 0px rgba(126, 234, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .label .bar.color2 {
	box-shadow:0px 0px 5px 0px rgba(229, 195, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .label .bar.color3 {
	box-shadow:0px 0px 5px 0px rgba(232, 25, 87, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .label .bar.color4 {
	box-shadow:0px 0px 5px 0px rgba(24, 109, 226, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar.color2 {
	background:rgb(229,195,25);
	
	box-shadow:0px 0px 12px 0px rgba(229, 195, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar.color3 {
	background:rgb(255, 104, 149);
	
	box-shadow:0px 0px 7px 0px rgba(232, 25, 87, 1), 0px 1px 0px 0px rgba(255, 255, 255, 0.45), 1px 0px 0px 0px rgba(255, 255, 255, 0.25), -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar.color4 {
	background:rgb(24,109,226);
	
	box-shadow:0px 0px 12px 0px rgba(24, 109, 226, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar:before {
	position:absolute;
	display:block;
	content:"";
	width:606px;
	height:150%;
	top:-25%;
	left:-25px;
	/* background:-moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%, rgba(255,255,255,0.01) 50%, rgba(255,255,255,0) 51%, rgba(255,255,255,0) 100%);
	background:-webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,0.35)), color-stop(50%,rgba(255,255,255,0.01)), color-stop(51%,rgba(255,255,255,0)), color-stop(100%,rgba(255,255,255,0)));
	background:-webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	background:-o-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	background:-ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	background:radial-gradient(ellipse at center,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%); */
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#59ffffff', endColorstr='#00ffffff',GradientType=1 );
}
.progressbar .bar:after {
	position:absolute;
	display:block;
	content:"";
	width:64px;
	height:16px;
	right:0;
	top:0;
	border-radius:0px 16px 16px 0px;
	/* background:-moz-linear-gradient(left,  rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 98%, rgba(255,255,255,0) 100%);
	background:-webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,255,255,0)), color-stop(98%,rgba(255,255,255,0.6)), color-stop(100%,rgba(255,255,255,0)));
	background:-webkit-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	background:-o-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	background:-ms-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	background:linear-gradient(to right,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%); */
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#00ffffff',GradientType=1 );
}
.progressbar .bar span {
	position:absolute;
	display:block;
	width:100%;
	height:64px;
	-webkit-border-radius:16px;
	border-radius:16px;
	top:0;
	left:0;
	background:url(./theme/material/images/users/trafficbar.png) 0 0;
	-webkit-animation:sparkle 1500ms linear infinite;
    -moz-animation:sparkle 1500ms linear infinite;
    -o-animation:sparkle 1500ms linear infinite;
    animation:sparkle 1500ms linear infinite;
	opacity:0.4;
}
.progressbar .label {
	font-family:'Aldrich', sans-serif;
	position:relative;
	display:block;
	width:30%;
	height:30px;
	line-height:30px;
	bottom:40px;
	left:0px;
	background:rgb(255, 255, 255);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#4c4c4c', endColorstr='#262626',GradientType=0 );
	font-weight:bold;
	font-size:12px;
	color:#252525;
    filter:dropshadow(color=#000000, offx=0, offy=-1);
}


	</style>

</head>
<body class="page-orange">
	<header class="header header-orange header-transparent header-waterfall ui-header">
		<ul class="nav nav-list pull-left">
			<div>
				<a data-toggle="menu" href="#ui_menu">
					<span class="icon icon-lg text-white">format_align_justify</span>
				</a>
			</div>
		</ul>

		<ul class="nav nav-list pull-right">
			<div class="dropdown margin-right">
				<a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
				{if $user->isLogin}
					<span class="access-hide">{$user->user_name}</span>
              	    <span class="icon icon-cd margin-right">account_circle</span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>用户中心</a>
						</li>

						<li>
							<a class="padding-right-cd waves-attach" href="/user/logout"><span class="icon icon-lg margin-right">exit_to_app</span>登出</a>
						</li>
					</ul>
				{else}
					<span class="access-hide">未登录</span>
             		 <span class="icon icon-lg margin-right">account_circle</span>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">account_box</span>登录</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/register"><span class="icon icon-lg margin-right">pregnant_woman</span>注册</a>
						</li>
					</ul>
				{/if}

			</div>
		</ul>
	</header>
	<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
		<div class="menu-scroll">
			<div class="menu-content">
				<a class="menu-logo" href="/"><i class="icon icon-lg" >language</i>&nbsp;{$config["appName"]}</a>
				<ul class="nav">
					<li>
						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_me">我的</a>
						<ul class="menu-collapse collapse in" id="ui_menu_me">
							<li>
								<a href="/user">
									<i class="icon icon-lg">account_balance_wallet</i>&nbsp;用户中心
								</a>
							</li>

							<li>
								<a href="/user/profile">
									<i class="icon icon-lg">account_box</i>&nbsp;账户信息
								</a>
							</li>

							<li>
								<a href="/user/edit">
									<i class="icon icon-lg">sync_problem</i>&nbsp;资料编辑
								</a>
							</li>

							{if $config['enable_ticket']=='true'}
                            <li>
								<a href="/user/ticket">
									<i class="icon icon-lg">question_answer</i>&nbsp;工单系统
								</a>
							</li>
							{/if}

                            <li>
								<a href="/user/invite">
									<i class="icon icon-lg">loyalty</i>&nbsp;邀请链接
								</a>
							</li>
							
						</ul>


						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_use">使用</a>
						<ul class="menu-collapse collapse in" id="ui_menu_use">
							<li>
								<a href="/user/node">
									<i class="icon icon-lg">airplanemode_active</i>&nbsp;节点列表
								</a>
							</li>

							<li>
								<a href="/user/relay">
									<i class="icon icon-lg">compare_arrows</i>&nbsp;中转规则
								</a>
							</li>

							<li>
								<a href="/user/trafficlog">
									<i class="icon icon-lg">hourglass_empty</i>&nbsp;流量记录
								</a>
							</li>

							<li>
								<a href="/user/lookingglass">
									<i class="icon icon-lg">visibility</i>&nbsp;延迟检测
								</a>
								<a href="/user/announcement">
									<i class="icon icon-lg">start</i>&nbsp;使用教程
								</a>
							</li>
						</ul>

						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_detect">审计</a>
						<ul class="menu-collapse collapse in" id="ui_menu_detect">
							<li><a href="/user/detect"><i class="icon icon-lg">account_balance</i>&nbsp;审计规则</a></li>
							<li><a href="/user/detect/log"><i class="icon icon-lg">assignment_late</i>&nbsp;审计记录</a></li>
						</ul>

						{if $config['enable_wecenter']=='true'}
						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_trade">帮助</a>
						<ul class="menu-collapse collapse in" id="ui_menu_trade">
							<li>
								<a href="{$config["wecenter_url"]}" target="_blank">
									<i class="icon icon-lg">help</i>&nbsp;问答系统
								</a>
							</li>
						</ul>
						{/if}

						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_help">商店</a>
						<ul class="menu-collapse collapse in" id="ui_menu_help">
                          	<li>
								<a href="/user/code">
									<i class="icon icon-lg">code</i>&nbsp;充值
								</a>
							</li>

							<li>
								<a href="/user/shop">
									<i class="icon icon-lg">shop</i>&nbsp;套餐购买
								</a>
							</li>

							<li><a href="/user/bought"><i class="icon icon-lg">shopping_cart</i>&nbsp;购买记录</a></li>




                          {if $config['enable_donate']=='true'}
							<li>
								<a href="/user/donate">
									<i class="icon icon-lg">attach_money</i>&nbsp;捐赠公示
								</a>
							</li>
							{/if}

						</ul>


						{if $user->isAdmin()}
							<li>
								<a href="/admin">
									<i class="icon icon-lg">person_pin</i>&nbsp;管理面板
								</a>
							</li>
						{/if}
                                          	{if $can_backtoadmin}
                                         	    <li>
                                <a class="padding-right-cd waves-attach" href="/user/backtoadmin"><span class="icon icon-lg margin-right">backtoadmin</span>返回管理员身份</a>
                                                    <li>
                                                {/if}


					</li>
				</ul>
			</div>
		</div>
	</nav>

{if $config["enable_crisp"] == 'true'}{include file='crisp.tpl'}{/if}
