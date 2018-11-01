





{include file='user/header_info.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">节点信息</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="ui-card-wrap">
					<div class="row">
						<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading">注意！</p>
										<p>下面为您的 PAC 配置。</p>
									</div>
									
								</div>
							</div>
						</div>			
						<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading">配置信息</p>
										<p>{$json_show}</p>
									</div>
									
								</div>
							</div>
						</div>
						
						<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading">配置方法</p>
										<p>Android：打开设置-WLAN-长按要使用的wifi-弹出窗口-点击修改网络-展开高级选项-代理框中选择代理自动配置-PAC网址中填写配置信息所给出的地址-保存。连接时会提示输入用户名密码，按照配置信息输入即可。</p>
                                        <p>iOS：打开设置-无线局域网-点击需要使用的wifi右侧蓝色修改按钮-在新打开的页面中-HTTP代理选择自动-URL框中填写配置信息所给出的地址-保存。连接时会提示输入用户名密码，按照配置信息输入即可。</p>
                                        <p>Windows：打开控制面板-在查看方式中选择小图标或大图标-打开Internet选项-在连接选项卡上单击局域网设置-在自动配置中选择使用自动脚本，并将配置信息中给出的地址填写到地址一栏中-之后点击确定-应用。连接时会提示输入用户名密码，按照配置信息输入即可。</p>
									</div>
									
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</section>
		</div>
	</main>







{include file='user/footer.tpl'}




