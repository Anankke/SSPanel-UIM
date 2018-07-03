<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<meta name="theme-color" content="#3f51b5">
	<title>{$config["appName"]}</title>
	<!-- css -->
	<link href="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.10/public/theme/material/css/base.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.10/public/theme/material/css/project.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.8/public/theme/material/css/materialdesignicons.min.css" rel="stylesheet">
	<link href="https://fonts.loli.net/css?family=Roboto:300,300italic,400,400italic,500,500italic" rel="stylesheet">
	<link href="https://fonts.loli.net/css?family=Material+Icons">
	<!-- favicon -->
	<!-- ... -->
</head>
  <style>
.divcss5{ position:fixed; bottom:0;}
</style>
<body class="page-brand">
	<header class="header header-transparent header-waterfall ui-header">
		<ul class="nav nav-list pull-left">
			<li>
				<a data-toggle="menu" href="#ui_menu">
					<span class="icon icon-lg">format_align_justify</span>
				</a>
			</li>
		</ul>
		<ul class="nav nav-list pull-right">
			<li class="dropdown margin-right">
				<a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
				{if $user->isLogin}
					<span class="access-hide">{$user->user_name}</span>
                	  <span class="icon icon-cd margin-right">account_circle</span>
				<!--	<span class="avatar avatar-sm"><img alt="alt text for John Smith avatar" src="{$user->gravatar}"></span>  -->
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>用户中心</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/user/logout"><span class="icon icon-lg margin-right">exit_to_app</span>退出登錄</a>
						</li>
					</ul>
				{else}
					<span class="access-hide">未登錄</span>
             			 <span class="icon icon-cd margin-right">account_circle</span>
				<!--	<span class="avatar avatar-sm"><img alt="alt text for John Smith avatar" src="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.1/public/theme/material/images/users/avatar-001.jpg"></span> -->
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">vpn_key</span>登錄</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/register"><span class="icon icon-lg margin-right">person_add</span>注冊賬戶</a>
						</li>
					</ul>
				{/if}
			</li>
		</ul>
	</header>
	<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
		<div class="menu-scroll">
			<div class="menu-content">
				<a class="menu-logo" href="/user"><img src="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.9/public/images/walllink_logo_black_small.png" height="35"></img>&nbsp;{$config["appName"]}</a>
				<ul class="nav">
					{if $user->isLogin}
					<li>
						<a  href="/user"><i class="icon icon-lg">person</i>&nbsp;用戶中心</a>
					</li>
					<li>
						<a  href="/user/logout"><i class="icon icon-lg">call_missed_outgoing</i>&nbsp;退出登錄</a>
					</li>
					{else}
					<li>
						<a  href="/auth/login"><i class="icon icon-lg">vpn_key</i>&nbsp;登錄</a>
					</li>
					<li>
						<a  href="/auth/register"><i class="icon icon-lg">person_add</i>&nbsp;注冊賬戶</a>
					</li>
                  	<li>
				     <a  href="/password/reset"><i class="mdi mdi-lock-question"></i>&nbsp;重置賬戶密碼</a>
					</li>
					{/if}
				</ul>
			</div>
		</div>
	</nav>

{if $config["enable_crisp"] == 'true'}{include file='crisp.tpl'}{/if}