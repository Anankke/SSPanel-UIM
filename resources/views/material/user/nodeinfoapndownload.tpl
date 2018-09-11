





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
										<p>下面为您的 APN 配置。</p>
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
										<p>本方法仅限iOS用户在 移动/联通/电信 3G或4G网络下使用</p>
                                        <p>1 根据你的运营商在safari中输入与你运营商对应的地址</p>
                                        <p>2 访问这个地址后会跳转到设置中自动弹出安装描述文件弹窗</p>
                                        <p>3 之后点击安装，如有密码会提示你输入密码，请正确输入设备密码</p>
                                        <p>4 如果出现警告，忽略并点击安装，经再次确认安装后就已经安装完成配置文件了，点击右上角完成结束配置</p>
                                        <p>5 该方法如果需要使用请切换到3/4G网络下，打开网页时会提示你输入用户名与密码，请按照配置信息中内容输入</p>
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




