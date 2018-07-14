{include file='header.tpl'}
{if $config['appName'] == '跑路'}
<script>window.location.href='{$config["baseUrl"]}/paolu.html';</script>
{/if}






	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-lg-push-0 col-sm-12 col-sm-push-0">
						<h1 class="content-heading">{$config["appName"]}</h1>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
						<section class="content-inner margin-top-no">
						
					
						
						{if $user->isLogin}
							<div class="col-lg-12 col-sm-12">
								<div class="card">
									<div class="card-main">
										<div class="card-inner">
											<p>主人~ 您回来啦！</p>
										</div>
									</div>
								</div>
							</div>
                          
							<div class="col-lg-12 col-sm-12">
								<div class="card">
									<div class="card-main">
										<div class="card-inner">
											<p class="card-heading">快看快看，主人的信息出现在下面呢~</p>
                                            <p>用户：<code>{$user->user_name}</code>
                                               等级：{if $user->class!=0}<code>VIP{$user->class}</code>{else}<code>免费</code>{/if}
                                              过期时间：{if $user->class_expire!="1989-06-04 00:05:00"}<code>{$user->class_expire}</code>{else}<code>不过期</code>{/if}
                                            </p>
                                            <p>总流量：<code>{$user->enableTraffic()}</code>
                                               已用流量：<code>{$user->usedTraffic()}</code>
                                               剩余流量：<code>{$user->unusedTraffic()}</code>
                                            </p>
										</div>
									</div>
								</div>
							</div>
                          
							<div class="col-lg-12 col-sm-12">
								<div class="card">
									<div class="card-main">
										<div class="card-inner">
											<p class="card-heading">用户面板</p>
												<a class="btn btn-flat waves-attach waves-light waves-effect" href="/user"><span class="icon">pregnant_woman</span>&nbsp;主人可以摸这里进入面板</a>
										</div>
									</div>
								</div>
							</div>
						{else}
							<div class="col-lg-12 col-sm-12">
								<div class="card">
									<div class="card-main">
										<div class="card-inner">
											<p>客官~ 您好啊！</p>
										</div>
									</div>
								</div>
							</div>
                          
                          
							<div class="col-lg-12 col-sm-12">
								<div class="card">
									<div class="card-main">
										<div class="card-inner">
											<p class="card-heading">注册</p>
												<a class="btn btn-flat waves-attach waves-light waves-effect" href="/auth/register"><span class="icon">pregnant_woman</span>&nbsp;如果客官您没有账号的话，就摸我这里来注册吧。</a>
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-lg-12 col-sm-12">
								<div class="card">
									<div class="card-main">
										<div class="card-inner">
											<p class="card-heading">登录</p>
												<a class="btn btn-flat waves-attach waves-light waves-effect" href="/auth/login"><span class="icon">vpn_key</span>&nbsp;如果客官您有账号的话，就摸我这里来登录吧。</a>
										</div>
									</div>
								</div>
							</div>
							
							
								
						{/if}
							
							
							
						</section>

			
			
			
		</div>
	</main>


{include file='footer.tpl'}
