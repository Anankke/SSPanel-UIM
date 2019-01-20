<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<meta name="theme-color" content="#4285f4">
	<title>{$config["appName"]}</title>


	<!-- css -->
	<link href="/theme/material/css/base.min.css" rel="stylesheet">
	<link href="/theme/material/css/project.min.css" rel="stylesheet">
    <link href="https://fonts.loli.net/css?family=Roboto:300,300italic,400,400italic,500,500italic|Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="/theme/material/css/user.css">
	<!-- jquery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>

</head>
<body class="page-orange">
	<header class="header header-orange header-transparent header-waterfall ui-header">
		<ul class="nav nav-list pull-left">
			<div>
				<a data-toggle="menu" href="#ui_menu">
					<span class="icon icon-lg text-white">menu</span>
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
							<a class="waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>Dashboard</a>
						</li>

						<li>
							<a class="padding-right-cd waves-attach" href="/user/logout"><span class="icon icon-lg margin-right">exit_to_app</span>Sign out</a>
						</li>
					</ul>
				{else}
					<span class="access-hide">Not sing in</span>
             		 <span class="icon icon-lg margin-right">account_circle</span>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">account_box</span>Login</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/register"><span class="icon icon-lg margin-right">pregnant_woman</span>Register</a>
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
						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_me">My Account</a>
						<ul class="menu-collapse collapse in" id="ui_menu_me">
							<li>
								<a href="/user">
									<i class="icon icon-lg">account_balance_wallet</i>&nbsp;Dashboard
								</a>
							</li>

							<li>
								<a href="/user/profile">
									<i class="icon icon-lg">account_box</i>&nbsp;Account Details
								</a>
							</li>

							<li>
								<a href="/user/edit">
									<i class="icon icon-lg">sync_problem</i>&nbsp;Edit My Account
								</a>
							</li>

							<li>
								<a href="/user/trafficlog">
									<i class="icon icon-lg">hourglass_empty</i>&nbsp;Data usage
								</a>
							</li>

							{if $config['enable_ticket']=='true'}
                            <li>
								<a href="/user/ticket">
									<i class="icon icon-lg">question_answer</i>&nbsp;Support Tickets
								</a>
							</li>
							{/if}

                            <li>
								<a href="/user/invite">
									<i class="icon icon-lg">loyalty</i>&nbsp;Invitation Codes
								</a>
							</li>
							
						</ul>


						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_use">Usage</a>
						<ul class="menu-collapse collapse in" id="ui_menu_use">
							<li>
								<a href="/user/node">
									<i class="icon icon-lg">airplanemode_active</i>&nbsp;Server List
								</a>
							</li>

							<li>
								<a href="/user/relay">
									<i class="icon icon-lg">compare_arrows</i>&nbsp;Data Redirect Rules
								</a>
							</li>

							<li>
								<a href="/user/lookingglass">
									<i class="icon icon-lg">visibility</i>&nbsp;Server Looking-glass
								</a>
							</li>

							<li>
								<a href="/user/announcement">
									<i class="icon icon-lg">announcement</i>&nbsp;Announcements
								</a>
							</li>

							<li>
								<a href="/user/tutorial">
									<i class="icon icon-lg">start</i>&nbsp;Use tutorial
								</a>
							</li>
						</ul>

						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_detect">Filtering</a>
						<ul class="menu-collapse collapse in" id="ui_menu_detect">
							<li><a href="/user/detect"><i class="icon icon-lg">account_balance</i>&nbsp;Filtering rules</a></li>
							<li><a href="/user/detect/log"><i class="icon icon-lg">assignment_late</i>&nbsp;Filtering record</a></li>
						</ul>

						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_help">Your Subsciption</a>
						<ul class="menu-collapse collapse in" id="ui_menu_help">
                          	<li>
								<a href="/user/code">
									<i class="icon icon-lg">code</i>&nbsp;Recharge
								</a>
							</li>

							<li>
								<a href="/user/shop">
									<i class="icon icon-lg">shop</i>&nbsp;Shop
								</a>
							</li>

							<li><a href="/user/bought"><i class="icon icon-lg">shopping_cart</i>&nbsp;Purchase History</a></li>




                          {if $config['enable_donate']=='true'}
							<li>
								<a href="/user/donate">
									<i class="icon icon-lg">attach_money</i>&nbsp;Donations
								</a>
							</li>
							{/if}

						</ul>

						{if $config['enable_telegram']=='true' && $config['telegram_grouplink']!='' }
						<li>
							<a href="{$config['telegram_grouplink']}" target="_blank"><span class="icon icon-lg">near_me</span> Telegram Group</a>
						</li>
						{/if}

						{if $user->isAdmin()}
							<li>
								<a href="/admin">
									<i class="icon icon-lg">person_pin</i>&nbsp;Management panel
								</a>
							</li>
						{/if}

                        {if $can_backtoadmin}
                            <li>
                                <a class="padding-right-cd waves-attach" href="/user/backtoadmin"><span class="icon icon-lg margin-right">backtoadmin</span>Return to admin</a>
                            <li>
                        {/if}


					</li>
				</ul>
			</div>
		</div>
	</nav>

{if $config["enable_crisp"] == 'true'}{include file='crisp.tpl'}{/if}
