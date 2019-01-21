{include file='user/main.tpl'}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">使用教程</h1>
			</div>
		</div>
      
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="ui-card-wrap">
                  
					
						<div class="col-lg-12 col-md-12">
							<div class="card">
								<div class="card-main">
                                  
									<div class="card-inner">
										<nav class="tab-nav margin-top-no">
											<ul class="nav nav-list">
												<li class="active">
													<a class="" data-toggle="tab" href="#all_ssr_windows"><i class="icon icon-lg">desktop_windows</i>&nbsp;Windows</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_mac"><i class="icon icon-lg">laptop_mac</i>&nbsp;MacOS</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_linux"><i class="icon icon-lg">dvr</i>&nbsp;Linux</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_ios"><i class="icon icon-lg">phone_iphone</i>&nbsp;iOS</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_android"><i class="icon icon-lg">android</i>&nbsp;Android</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_router"><i class="icon icon-lg">router</i>&nbsp;路由器</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_game"><i class="icon icon-lg">videogame_asset</i>&nbsp;游戏端</a>
												</li>
											</ul>
										</nav>
                                      
										<div class="tab-pane fade active in page-course" id="all_ssr_windows">
											<ul>
												<h3><li>下载软件</li> </h3>
												<ol>
													<li>点击左侧用户中心(手机需先点左上角按钮调出导航菜单)</li>
													<li>找到快速添加节点</li>
													<li>点击下载客户端</li>
													<p><img src="/images/c_win_1.png"/></p>
												</ol>
											</ul>
											<ul>
												<h3><li>导入节点</li> </h3>
												<ul>
													<li>解压客户端，双击shadowsocksr4.0的客户端(打不开就用2.0，2.0打不开请下载安装net.framework3.0，还打不开麻烦升级到win7)</li>
													<li>方法一：</li>
													<ol>
														<li>在快速添加节点中找到【备用节点导入方法】</li>
														<li>点击其中的链接</li>
														<p><img src="/images/c_win_2.png"/></p>
														<li>找到系统托盘菜单中的SSR纸飞机图标右键调出菜单</li>
														<li>点击剪贴板批量导入ssr://链接</li>
														<p><img src="/images/c_win_3.png"/></p>
													</ol>
													<li>方法二(推荐)：</li>
													<ol>
														<li>在快速添加节点中找到节点订阅地址</li>
														<li>点击按钮复制订阅链接</li>
														<p><img src="/images/c_win_4.png"/></p>
														<li>找到系统托盘菜单中的SSR纸飞机图标右键调出菜单</li>
														<li>打开SSR服务器订阅链接设置</li>
														<p><img src="/images/c_win_5.png"/></p>
														<li>点击add添加一个订阅，将复制的链接填入右侧框内点击确定</li>
														<p><img src="/images/c_win_6.png"/></p>
														<li>找到系统托盘菜单中的SSR纸飞机图标右键调出菜单</li>
														<li>点击更新SSR服务器订阅(不通过代理)</li>
														<p><img src="/images/c_win_7.png"/></p>
													</ol>
												</ul>
											</ul>
											<ul>
												<h3><li>选择节点</li></h3>
												<ol>
													<li>找到系统托盘菜单中的SSR纸飞机图标右键调出菜单</li>
													<li>服务器->找到对应本站的节点组->选择一个节点单击</li>
													<p><img src="/images/c_win_8.png"/></p>
													<li>打开浏览器输入www.google.com试试吧！</li>
												</ol>
												<ul>以上教程均为电脑没有安装过任何代理软件的步骤，如果安装过其他代理软件可能产生冲突</ul>
											</ul>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_mac">
											<p>1：把下载的DMG包放入应用程序列表</p>
											<p><img src="/images/c_mac_1.png"/></p>
											<p>2：打开程式</p>
											<p><img src="/images/c_mac_2.png"/></p>
											<p>3：如提示不安全，请到系统偏好设置打开程式</p>
											<p><img src="/images/c_mac_3.png"/></p>
											<p>4：服务器-编辑订阅</p>
											<p><img src="/images/c_mac_4.png"/></p>
											<p>5：点击+号后填入订阅链接后手动更新订阅</p>
											<p><img src="/images/c_mac_5.png"/></p>
											<p><img src="/images/c_mac_4.png"/></p>
											<p>6：选择一个节点</p>
											<p><img src="/images/c_mac_6.png"/></p>
											<p>7：打开谷歌测试一下吧</p>
											<p><img src="/images/c_mac_7.png"/></p>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_linux">
											<h3>Ubuntu使用Shadowsocks-qt5科学上网</h3>
											<h4>说明：shadowsocks-qt5是ubuntu上一个可视化的版本</h4>
											<hr/>
											<h5>安装shadowsocks-qt5</h5>
											<pre><code>1.$ sudo add-apt-repository ppa:hzwhuang/ss-qt5
												2.$ sudo apt-get update
												3.$ sudo apt-get install shadowsocks-qt5</code></pre>
											<h5>如果安装成功之后，按<code>win</code>键搜索应该能够找到软件，如下图所示：</h5>
											<p><img src="/images/c-linux-1.png"/></p>
											<h5>配置shadowsocks-qt5</h5>
											<h6>填写对应的服务器IP，端口，密码，加密方式，红色标注地方请与图片一样</h6>
											<p><img src="/images/c-linux-4.png"/></p>
											<h5>配置系统代理模式</h5>
											<p><img src="/images/c-linux-5.png"/></p>
											<h5>配置浏览器代理模式（本次为Ubuntu自带FireFox浏览器为例）</h5>
											<p><img src="/images/c-linux-6.png"/></p>
											<h5>连接并开始上网</h5>
											<p><img src="/images/c-linux-7.png"/></p>
											<hr/>
											<p>本教程由仟佰星云试验截图整理，转载请附本文链接</p>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_ios">
											<p>1：前往用户中心查看App Store账号，国区App Store已下架)</p>
											<p><img src="/images/c_ios_1.jpg"/></p>
											<p>2：打开App Store 切换账号，并下载App</p>
											<p><img src="/images/c_ios_2.jpg"/></p>
											<p>3：打开Safari，登录到 {$config["appName"]} 的用户中心导入节点</p>
											<p><img src="/images/c_ios_3.jpg"/></p>
											<p>附加：iOS快速连接</p>
											<p><img src="/images/c_ios_4.jpg"/></p>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_android">
											<p>1：下载app</p>
											<p><img src="/images/c_android_1.jpg"/></p>
											<p>2：添加订阅并更新</p>
											<p><img src="/images/c_android_2.jpg"/></p>
											<p><img src="/images/c_android_3.jpg"/></p>
											<p><img src="/images/c_android_4.jpg"/></p>
											<p><img src="/images/c_android_5.jpg"/></p>
											<p>3：选择一个节点并设置路由</p>
											<p><img src="/images/c_android_6.jpg"/></p>
											<p><img src="/images/c_android_7.jpg"/></p>
											<p>4：连接</p>
											<p><img src="/images/c_android_8.jpg"/></p>
											<p>注释：国产安卓系统为定制系统，如需要Youtube、Google套件等，需要安装Google框架，具体机型如何安装各不相同，请直接查找教程</p>
										</div>

										<div class="tab-pane fade" id="all_ssr_router">
											<h2 class="major">路由器</h2>
										</div>  
										
										<div class="tab-pane fade" id="all_ssr_game">
											<h2 class="major">游戏端</h2>
										</div>

									</div>

								</div>
							</div>
							
						
				
							
						
						{include file='dialog.tpl'}
						
					</div>
						
					
				</div>
			</section>
		</div>
	</main>







{include file='user/footer.tpl'}



