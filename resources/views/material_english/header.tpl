<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<meta name="theme-color" content="#4285f4">
	<title>{$config["appName"]}</title>
	<!-- css -->
	<link href="/theme/material/css/base.min.css" rel="stylesheet">
	<link href="/theme/material/css/project.min.css" rel="stylesheet">
	<link href="/theme/material/css/auth.css" rel="stylesheet">
    <link href="https://fonts.loli.net/css?family=Roboto:300,300italic,400,400italic,500,500italic|Material+Icons" rel="stylesheet">

	<!-- favicon -->

	<!-- ... -->
</head>
  <style>
.divcss5{ position:fixed; bottom:0;}
</style>
<body class="page-brand">
	{* <header class="header header-transparent header-waterfall ui-header">
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
							<a class="padding-right-lg waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>Dashboard</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/user/logout"><span class="icon icon-lg margin-right">exit_to_app</span>Sign out</a>
						</li>
					</ul>
				{else}
					<span class="access-hide">Not logged in</span>
             			 <span class="icon icon-cd margin-right">account_circle</span>
				<!--	<span class="avatar avatar-sm"><img alt="alt text for John Smith avatar" src="/theme/material/images/users/avatar-001.jpg"></span> -->
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">vpn_key</span>Login</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/register"><span class="icon icon-lg margin-right">person_add</span>Register</a>
						</li>
					</ul>
				{/if}

			</li>
		</ul>
	</header>
	<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
		<div class="menu-scroll">
			<div class="menu-content">
				<a class="menu-logo" href="/"><i class="icon icon-lg">language</i>&nbsp;Return to Home Page</a>
				<ul class="nav">

					{if $user->isLogin}
					<li>
						<a  href="/user"><i class="icon icon-lg">person</i>&nbsp;Dashboard</a>
					</li>
					<li>
						<a  href="/user/logout"><i class="icon icon-lg">call_missed_outgoing</i>&nbsp;Sign out</a>
					</li>
					{else}
					<li>
						<a  href="/auth/login"><i class="icon icon-lg">vpn_key</i>&nbsp;Login</a>
					</li>
					<li>
						<a  href="/auth/register"><i class="icon icon-lg">person_add</i>&nbsp;Register</a>
					</li>
                  	<li>
				     <a  href="/password/reset"><i class="icon icon-lg">security</i>&nbsp;Reset Password</a>
					</li>

					{/if}
				</ul>
			</div>
		</div>
	</nav> *}

{if $config["enable_crisp"] == 'true'}{include file='crisp.tpl'}{/if}
