
{include file='user/main.tpl'}
{$ssr_prefer = URL::SSRCanConnect($user, 0)}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">用户中心</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
				<div class="ui-card-wrap">
						<div class="col-lg-6 col-md-6">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading"> <i class="icon icon-md">notifications_active</i>站點公告</p>
										{if $ann != null}
										<p>{$ann->content}</p>
										{/if}
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading"><i class="icon icon-md">phonelink</i> 快速添加节点</p>
										<nav class="tab-nav margin-top-no">
											<ul class="nav nav-list">
												<li {if $ssr_prefer}class="active"{/if}>
													<a class="waves-attach" data-toggle="tab" href="#all_ssr"><i class="icon icon-lg">airplanemode_active</i>&nbsp;ShadowsocksR</a>
												</li>
												<li {if !$ssr_prefer}class="active"{/if}>
													<a class="waves-attach" data-toggle="tab" href="#all_ss"><i class="icon icon-lg">flight_takeoff</i>&nbsp;Shadowsocks</a>
												</li>
											</ul>
										</nav>
										<div class="card-inner">
											<div class="tab-content">
												<div class="tab-pane fade {if $ssr_prefer}active in{/if}" id="all_ssr">
													{$pre_user = URL::cloneUser($user)}

													<nav class="tab-nav margin-top-no">
														<ul class="nav nav-list">
															<li class="active">
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_windows"><i class="icon icon-lg">desktop_windows</i>&nbsp;Windows</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_mac"><i class="icon icon-lg">laptop_mac</i>&nbsp;MacOS</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_linux"><i class="icon icon-lg">dvr</i>&nbsp;Linux</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_ios"><i class="icon icon-lg">phone_iphone</i>&nbsp;iOS</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_android"><i class="icon icon-lg">android</i>&nbsp;Android</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_router"><i class="icon icon-lg">router</i>&nbsp;路由器</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_game"><i class="icon icon-lg">videogame_asset</i>&nbsp;游戏端</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ssr_info"><i class="icon icon-lg">info_outline</i>&nbsp;连接信息</a>
															</li>
														</ul>
													</nav>
													<div class="tab-pane fade active in" id="all_ssr_windows">
														{$user = URL::getSSRConnectInfo($pre_user)}
														{$ssr_url_all = URL::getAllUrl($pre_user, 0, 0)}
														{$ssr_url_all_mu = URL::getAllUrl($pre_user, 1, 0)}
														<p><span class="icon icon-lg text-white">looks_one</span><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ssr-win.7z"> 点击我下载</a></p>
														<p><span class="icon icon-lg text-white">looks_two</span> 解压至任意磁盘</p>
														<p><span class="icon icon-lg text-white">looks_3</span> 运行程序</p>
														<p> <span class="icon icon-lg text-white">looks_4</span> 任务栏右下角右键纸飞机图标--服务器订阅--SSR服务器订阅设置，将订阅链接设置为下面的地址，确定之后再更新 SSR 服务器订阅。</p>
														<p> <span class="icon icon-lg text-white">looks_5</span> 然后选择一个合适的服务器，代理规则选“绕过局域网和大陆”，然后即可上网。</p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 普通节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0">点击拷贝订阅地址</button><br></p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 单端口节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1">点击拷贝订阅地址</button><br></p>
														<!--p><a href="/user/announcement">点击这里查看Windows傻瓜式教程</a></p-->
													</div>
													<div class="tab-pane fade" id="all_ssr_mac">
														<p><span class="icon icon-lg text-white">looks_one</span><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ssr-mac.dmg"> 点击我下载</a></p>
														<p><span class="icon icon-lg text-white">looks_two</span> 打开下载的Dmg文件</p>
														<p><span class="icon icon-lg text-white">looks_3</span> 把ShadowsocksX拖入到Finder的应用程序列表(Applications)</p>
														<p><span class="icon icon-lg text-white">looks_4</span> 打开Launchapad里的ShadowsocksX</p>
														<p><span class="icon icon-lg text-white">looks_5</span> 菜单栏的纸飞机图标-服务器-服务器订阅填入以下订阅地址，更新后出现您的节点</p>
														<p><span class="icon icon-lg text-white">looks_6</span> 菜单栏的纸飞机图标-打开shadowsocks</p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 普通节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0">点击拷贝订阅地址</button><br></p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 单端口节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1">点击拷贝订阅地址</button><br></p>
														<!--p><a href="/user/announcement">点击这里查看Mac傻瓜式教程</a></p-->
													</div>
                                          <div class="tab-pane fade" id="all_ssr_linux">
														<!--p><a href="/user/announcement">点击这里查看Linux傻瓜式教程</a></p-->
													</div>
													<div class="tab-pane fade" id="all_ssr_ios">
														<!--p><span class="icon icon-lg text-white">looks_one</span> 切换<code>App Store</code>账号-ID:<code>
														    {if $user->class>0}                                          	 xxxx@apple.com(帐号密码请在resources/views/materials/user/index.tpl中修改)
															{else}															VIP用户可见
															{/if}
														</code>密码<code>
														{if $user->class>0}															xxxxxx
															{else}															VIP用户可见
															{/if}														</code>注意特殊符号与大小写,如果显示需要解锁账号，请提交工单或邮件。（千万不要试图登陆iCloud，会導致您的隱私資料外泄以及機器被鎖定）</p-->
                                             <p><span class="icon icon-lg text-white">looks_two</span> 商店搜索<code>Shadowrocket</code>下载安装</p>
                                             <p><span class="icon icon-lg text-white">looks_3</span> 安装完成后切换回您自己的账号。（请务必切换回您自己的账户）</p>
														<p><span class="icon icon-lg text-white">looks_3</span> 打开Shadowrocket软件后，点击右上角<span class="icon icon-lg text-white">add</span>，添加类型为<code>Subscribe</code>，URL填写以下地址即可自动更新节点</p>
                                             <p><span class="icon icon-lg text-white">looks_4</span> 选择任意节点点击连接，然后点击allow后解锁指纹</p>
                                             <p><span class="icon icon-lg text-white">looks_5</span> 注意：第一次连接可能不能用，导入后程序退出并重新打开程序后连接即可！</p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 普通节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0">点击拷贝订阅地址</button><br></p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 单端口节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1">点击拷贝订阅地址</button><br></p>
                                             <!--p><a href="/user/announcement">点击这里查看iOS傻瓜式教程</a></p-->
													</div>
													<div class="tab-pane fade" id="all_ssr_android">
														<p><span class="icon icon-lg text-white">filter_1</span><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ssr-android.apk"> 点击我下载</a>并安装</p>
														<p><span class="icon icon-lg text-white">filter_2</span> 打开App，点击右下角的<span class="icon icon-lg text-white">add</span>号图标</p>
														<p><span class="icon icon-lg text-white">filter_3</span> 添加/升级 SSR订阅</p>
														<p><span class="icon icon-lg text-white">filter_4</span> 添加订阅地址，输入以下订阅地址后确定</p>
														<p><span class="icon icon-lg text-white">filter_5</span> 订阅出现系统自带的与{$config["appName"]}，请把系统自带的无效订阅左滑删除（自带影响订阅更新速度）</p>
														<p><span class="icon icon-lg text-white">filter_6</span> 点击确定并升级</p>
														<p><span class="icon icon-lg text-white">filter_7</span> 选择任意节点</p>
														<p><span class="icon icon-lg text-white">filter_8</span> 路由选择：略过区域网路以及中国大陆</p>
														<p><span class="icon icon-lg text-white">filter_9</span> 点击右上角的纸飞机图标即可连接</p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 普通节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0">点击拷贝订阅地址</button><br></p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 单端口节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1">点击拷贝订阅地址</button><br></p>
														<!--p><a href="/user/announcement">点击这里查看Android傻瓜式教程</a></p-->
													</div>
													<div class="tab-pane fade" id="all_ssr_router">
													<p>路由器 刷入<a href="http://www.right.com.cn/forum/thread-161324-1-1.html">这个固件</a>，然后 SSH 登陆路由器，执行以下命令（导入普通端口）<br>
														<code>wget -O- {$config['subUrl']}/link/{$router_token}?is_ss=0 | bash && echo -e "\n0 */3 * * * wget -O- {$config['subUrl']}/link/{$router_token}?is_ss=0 | bash\n">> /etc/storage/cron/crontabs/admin && killall crond && crond </code><br>
														这个单端口多用户的<br>
														<code>wget -O- {$config['subUrl']}/link/{$router_token_without_mu}?is_ss=0 | bash && echo -e "\n0 */3 * * * wget -O- {$config['subUrl']}/link/{$router_token_without_mu}?is_ss=0 | bash\n">> /etc/storage/cron/crontabs/admin && killall crond && crond </code><br>
														执行完毕以后就可以到路由器的设置面板里随意选择 Shadowsocks 服务器进行连接了。</p>
													</div>
													<div class="tab-pane fade" id="all_ssr_game">
														<p><span class="icon icon-lg text-white">looks_one</span><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/SSTap.7z">点击我下载</a></p>
														<p><span class="icon icon-lg text-white">looks_two</span> 安装，期间会安装虚拟网卡，请点击允许或确认</p>
														<p><span class="icon icon-lg text-white">looks_3</span> 打开桌面程序SSTap</p>
														<p><span class="icon icon-lg text-white">looks_4</span> 齿轮图标-SSR订阅-SSR订阅管理添加以下订阅链接即可</p>
														<p><span class="icon icon-lg text-white">looks_5</span> 更新后选择其中一个节点闪电图标测试节点-测试UDP转发...通过!（UDP通过即可连接并开始游戏），如测试不通过，点击齿轮图标设置DNS，推荐谷歌DNS</p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 普通节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=0">点击拷贝订阅地址</button><br></p>
														<p><span class="icon icon-lg text-white">flash_auto</span> 单端口节点订阅地址：<input type="text" class="input form-control form-control-monospace" name="input1" readonly value="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1" readonly="true"><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="{$config['subUrl']}/link/{$ssr_sub_token}?mu=1">点击拷贝订阅地址</button><br></p>
														<!--p><a href="/user/announcement">点击这里查看游戏客户端傻瓜式教程</a></p-->
													</div>
													<div class="tab-pane fade" id="all_ssr_info">
														{$user = URL::getSSRConnectInfo($pre_user)}
														{$ssr_url_all = URL::getAllUrl($pre_user, 0, 0)}
														{$ssr_url_all_mu = URL::getAllUrl($pre_user, 1, 0)}
														{if URL::SSRCanConnect($user)}
														<dl class="dl-horizontal">
                                                <p><dt><code>优先导入普通端口，如果普通端口无法使用再导入单端口</code></dt></p>
															<p><dt>端口</dt>
															<dd>{$user->port}</dd></p>
															<p><dt>密码</dt>
															<dd>{$user->passwd}</dd></p>
															<p><dt>自定义加密</dt>
															<dd>{$user->method}</dd></p>
															<p><dt>自定义协议</dt>
															<dd>{$user->protocol}</dd></p>
															<p><dt>自定义混淆</dt>
															<dd>{$user->obfs}</dd></p>
															<p><dt>自定义混淆参数</dt>
															<dd>{$user->obfs_param}</dd></p>
														</dl>
														{else}
															<p>您好，您目前的 加密方式，混淆，或者协议设置在 ShadowsocksR 客户端下无法连接。请您选用 Shadowsocks 客户端来连接，或者到 资料编辑 页面修改后再来查看此处。</p>
															<p>同时, ShadowsocksR 单端口多用户的连接不受您设置的影响,您可以在此使用相应的客户端进行连接~</p>
															<p>请注意，在当前状态下您的 SSR 订阅链接已经失效，您无法通过此种方式导入节点。</p>
														{/if}
													</div>
												</div>
												<div class="tab-pane fade {if !$ssr_prefer}active in{/if}" id="all_ss">
													<nav class="tab-nav margin-top-no">
														<ul class="nav nav-list">
															<li class="active">
																<a class="waves-attach" data-toggle="tab" href="#all_ss_info"><i class="icon icon-lg">info_outline</i>&nbsp;连接信息</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ss_windows"><i class="icon icon-lg">desktop_windows</i>&nbsp;Windows</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ss_mac"><i class="icon icon-lg">laptop_mac</i>&nbsp;MacOS</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ss_ios"><i class="icon icon-lg">laptop_mac</i>&nbsp;iOS</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ss_android"><i class="icon icon-lg">android</i>&nbsp;Android</a>
															</li>
															<li>
																<a class="waves-attach" data-toggle="tab" href="#all_ss_router"><i class="icon icon-lg">router</i>&nbsp;路由器</a>
															</li>
														</ul>
													</nav>
													<div class="tab-pane fade active in" id="all_ss_info">
														{$user = URL::getSSConnectInfo($pre_user)}
														{$ss_url_all = URL::getAllUrl($pre_user, 0, 1)}
														{$ss_url_all_mu = URL::getAllUrl($pre_user, 1, 1)}
														{$ss_url_all_win = URL::getAllUrl($pre_user, 0, 2)}
														{if URL::SSCanConnect($user)}
														<dl class="dl-horizontal">
															<p>各个节点的地址请到节点列表查看！</p>
															<p><dt>端口</dt>
															<dd>{$user->port}</dd></p>
															<p><dt>密码</dt>
															<dd>{$user->passwd}</dd></p>
															<p><dt>自定义加密</dt>
															<dd>{$user->method}</dd></p>
															<p><dt>自定义混淆</dt>
															<dd>{$user->obfs}</dd></p>
															<p><dt>自定义混淆参数</dt>
															<dd>{$user->obfs_param}</dd></p>
														</dl>
														{else}
															<p>您好，您目前的 加密方式，混淆，或者协议设置在 SS 客户端下无法连接。请您选用 SSR 客户端来连接，或者到 资料编辑 页面修改后再来查看此处。</p>
															<p>同时, Shadowsocks 单端口多用户的连接不受您设置的影响,您可以在此使用相应的客户端进行连接~</p>
														{/if}
													</div>
													<div class="tab-pane fade" id="all_ss_windows">
														<p><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ss-win.zip">下载</a>，解压，运行程序，然后您有两种方式导入所有节点<br>
															(1)下载<a href="/user/getpcconf?is_mu=0&is_ss=1">这个（普通端口）</a>，放到小飞机的目录下，然后打开小飞机。<br>
															(2)点击<a class="copy-text" data-clipboard-text="{$ss_url_all_win}">这里（普通端口）</a>, 然后右键小飞机 -- 从剪贴板导入 URL<br>
													</div>
													<div class="tab-pane fade" id="all_ss_mac">
														<p><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ss-mac.zip">下载</a>，安装，然后下载<a href="/user/getpcconf?is_mu=0&is_ss=1">这个（普通端口）</a>或者<a href="/user/getpcconf?is_mu=1&is_ss=1">这个（单端口多用户）</a>，运行程序，小飞机上右键 服务器列表 子菜单 的 “导入服务器配置文件...” 导入这个文件，然后选择一个合适的服务器，更新一下PAC，然后开启系统代理即可上网。</p>
													</div>
													<div class="tab-pane fade" id="all_ss_ios">
														<p>推荐下载<a href="https://itunes.apple.com/cn/app/shadowrocket/id932747118?mt=8">Shadowrocket</a>，请在左侧菜单栏里提交工单申请已购买软件的美国商店Apple ID自行切换商店账号下载安装，为了您的隐私安全，请不要登录 iCloud 。下载完成后在 Safari 中点击<a href="{$ss_url_all}">这个（普通端口）</a>或者<a href="{$ss_url_all_mu}">这个（单端口多用户）</a>，然后点击确定，就可以批量添加节点。</p>
														<p>iOS 下载<a href="/link/{$ios_token}?is_ss=1">这个（普通端口）</a>或者<a href="/link/{$ios_token}?is_ss=1&is_mu=1">这个（单端口多用户）</a>，导入到 Surge 中，然后就可以随意切换服务器上网了。</p>
													</div>
													<div class="tab-pane fade" id="all_ss_android">
														<p><a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ss-android.apk">下载</a>，再<a href="https://cdn.jsdelivr.net/gh/YihanH/SS-SSR-Client-Download@v1.0/ss-android-obfs.apk">下载</a>，然后安装，然后在手机上点击 <a class="copy-text" data-clipboard-text="{$ss_url_all}"> 这个链接（普通端口）</a>或者<a class="copy-text" data-clipboard-text="{$ss_url_all_mu}">这个链接（单端口多用户端口）</a>复制到剪贴板，打开 Shadowsocks 客户端，选择从剪贴板导入，然后选择一个节点，设置一下路由为绕过大陆，点击飞机就可以上网了。</p>
													</div>
													<div class="tab-pane fade" id="all_ss_router">
														<p>路由器 刷入<a href="http://www.right.com.cn/forum/thread-161324-1-1.html">这个固件</a>，然后 SSH 登陆路由器，执行以下命令（导入普通端口）<br>
														<code>wget -O- {$config['subUrl']}/link/{$router_token}?is_ss=1 | bash && echo -e "\n0 */3 * * * wget -O- {$config['subUrl']}/link/{$router_token}?is_ss=1 | bash\n">> /etc/storage/cron/crontabs/admin && killall crond && crond </code><br>
														或者这个单端口多用户的<br>
														<code>wget -O- {$config['subUrl']}/link/{$router_token_without_mu}?is_ss=1 | bash && echo -e "\n0 */3 * * * wget -O- {$config['subUrl']}/link/{$router_token_without_mu}?is_ss=1 | bash\n">> /etc/storage/cron/crontabs/admin && killall crond && crond </code><br>
														执行完毕以后就可以到路由器的设置面板里随意选择 Shadowsocks 服务器进行连接了。</p>
													</div>
												</div>
											</div>
										</div>
										<div class="card-action">
											<div class="card-action-btn pull-left">
												<p><a class="reset-link btn btn-brand btn-flat waves-attach" ><span class="icon">autorenew</span>&nbsp;重置订阅链接</a></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading"><i class="icon icon-md">account_circle</i>賬戶詳情</p>
										<dl class="dl-horizontal">
											<p><dt>接入權限等級</dt>
												{if $user->class!=0}
													<dd><i class="icon icon-md t4-text">stars</i>&nbsp;<code>等級{$user->class}</code></dd>
												{else}
													<dd><i class="icon icon-md t4-text">stars</i>&nbsp;無等級</dd>
												{/if}
											</p>
											{if $user->class!=0}
												<p><dt>Telegram頻道</dt>
													<dd><i class="mdi mdi-telegram"></i>&nbsp;<a href="https://t.me/wallink" target="view_window">點擊進入</a></dd>
												</p>
											{else}
											{/if}											
											<p><dt>接入權限過期時間</dt>
											{if $user->class_expire!="1989-06-04 00:05:00"}
												<dd><i class="icon icon-md">event</i>&nbsp;{$user->class_expire}</dd>
											{else}
												<dd><i class="icon icon-md">event</i>&nbsp;不过期</dd>
											{/if}
											</p>
											<p><dt>剩餘接入權限有效期</dt>
												<i class="icon icon-md">event</i>
												<span class="label-level-expire">剩余</span>
												<code><span id="days-level-expire"></span></code>
												<span class="label-level-expire">天</span>
											</p>
											<p><dt>賬戶過期時間</dt>
											  <dd><i class="icon icon-md">event</i>&nbsp;{$user->expire_in}</dd>
											</p>
											<p><dt>賬戶有效期</dt>
												<i class="icon icon-md">event</i>
												<span class="label-account-expire">剩余</span>
												<code><span id="days-account-expire"></span></code>
												<span class="label-account-expire">天</span>
											</p>
											<p><dt>鏈接速度限制</dt>
											{if $user->node_speedlimit!=0}
												<dd><i class="icon icon-md">swap_vert</i>&nbsp;<code>{$user->node_speedlimit}</code>Mbps</dd>
											{else}
												<dd><i class="icon icon-md">settings_input_component</i>&nbsp;不限速</dd>
											{/if}
											</p>
											<p><dt>在綫設備數量</dt>
										    {if $user->node_connector!=0}
												<dd><i class="icon icon-md">phonelink</i>&nbsp;{$user->online_ip_count()} / {$user->node_connector}</dd>
											{else}
												<dd><i class="icon icon-md">phonelink</i>&nbsp;{$user->online_ip_count()} / 無限制 </dd>
											{/if}
											</p>
											<p><dt>賬戶餘額</dt>
												<dd><i class="mdi mdi-currency-cny"></i>&nbsp;<code>{$user->money}</code> CNY</dd>
											</p>
											<p><dt>最後使用時間</dt>
												{if $user->lastSsTime()!="从未使用喵"}
														<dd><i class="icon icon-md">event</i>&nbsp;{$user->lastSsTime()}</dd>
                                          {else}
														<dd><i class="icon icon-md">event</i>&nbsp;尚未使用</dd>
                                          {/if}
											</p>
											<p><dt>最後簽到時間：</dt>
												<dd><i class="icon icon-md">event</i>&nbsp;{$user->lastCheckInTime()}</dd>
											</p>
											<p id="checkin-msg"></p>
											{if $geetest_html != null}
												<div id="popup-captcha"></div>
											{/if}
											<div class="card-action">
												<div class="card-action-btn pull-left">
													{if $user->isAbleToCheckin() }
														<p id="checkin-btn">
															<button id="checkin" class="btn btn-brand btn-flat waves-attach"><span class="icon">check</span>&nbsp;簽到&nbsp;
															</button>
														</p>
													{else}
														<p><a class="btn btn-brand disabled btn-flat waves-attach" href="#"><span class="icon">check</span>&nbsp;已簽到</a></p>
													{/if}
												</div>
											</div>
										</dl>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<div id="traffic_chart" style="height: 300px; width: 100%;"></div>
										<script src="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.4/public/assets/js/canvasjs.min.js"> </script>
										<script type="text/javascript">
											var chart = new CanvasJS.Chart("traffic_chart",
											{
                                         theme: "light1",
												title:{
													text: "傳輸量使用情況",
													fontFamily: "Impact",
													fontWeight: "normal"
													},
												legend:{
													verticalAlign: "bottom",
													horizontalAlign: "center"
												},
												data: [
												{
													startAngle: -15,
													indexLabelFontSize: 20,
													indexLabelFontFamily: "Garamond",
													indexLabelFontColor: "darkgrey",
													indexLabelLineColor: "darkgrey",
													indexLabelPlacement: "outside",
                                                    yValueFormatString: "##0.00\"%\"",
													type: "pie",
													showInLegend: true,
													dataPoints: [
														{if $user->transfer_enable != 0}
														{
															y: {$user->last_day_t/$user->transfer_enable*100},label: "總計已用", legendText:"總計已用 {number_format($user->last_day_t/$user->transfer_enable*100,2)}% {$user->LastusedTraffic()}", indexLabel: "總計已用 {number_format($user->last_day_t/$user->transfer_enable*100,2)}% {$user->LastusedTraffic()}"
														},
														{
															y: {($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100},label: "本日使用", legendText:"本日使用 {number_format(($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100,2)}% {$user->TodayusedTraffic()}", indexLabel: "本日使用 {number_format(($user->u+$user->d-$user->last_day_t)/$user->transfer_enable*100,2)}% {$user->TodayusedTraffic()}"
														},
														{
															y: {($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100},label: "剩餘可用", legendText:"剩餘可用 {number_format(($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100,2)}% {$user->unusedTraffic()}", indexLabel: "剩餘可用 {number_format(($user->transfer_enable-($user->u+$user->d))/$user->transfer_enable*100,2)}% {$user->unusedTraffic()}"
														}
														{/if}
													]
												}
												]
											});
											chart.render();
										</script>
									</div>
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

<script src="https://cdn.jsdelivr.net/gh/YihanH/ss-panel-v3-mod-mod@v1.2.0.4/public/theme/material/js/shake.js/shake.js"></script>
<script>
function DateParse(str_date) {
		var str_date_splited = str_date.split(/[^0-9]/);
		return new Date (str_date_splited[0], str_date_splited[1] - 1, str_date_splited[2], str_date_splited[3], str_date_splited[4], str_date_splited[5]);
}
/*
 * Author: neoFelhz & CloudHammer
 * https://github.com/CloudHammer/CloudHammer/make-sspanel-v3-mod-great-again
 * License: MIT license & SATA license
 */
function CountDown() {
    var levelExpire = DateParse("{$user->class_expire}");
    var accountExpire = DateParse("{$user->expire_in}");
    var nowDate = new Date();
    var a = nowDate.getTime();
    var b = levelExpire - a;
    var c = accountExpire - a;
    var levelExpireDays = Math.floor(b/(24*3600*1000));
    var accountExpireDays = Math.floor(c/(24*3600*1000));
    if (levelExpireDays < 0 || levelExpireDays > 315360000000) {
        document.getElementById('days-level-expire').innerHTML = "无限期";
        for (var i=0;i<document.getElementsByClassName('label-level-expire').length;i+=1){
            document.getElementsByClassName('label-level-expire')[i].style.display = 'none';
        }
    } else {
        document.getElementById('days-level-expire').innerHTML = levelExpireDays;
    }
    if (accountExpireDays < 0 || accountExpireDays > 315360000000) {
        document.getElementById('days-account-expire').innerHTML = "无限期";
        for (var i=0;i<document.getElementsByClassName('label-account-expire').length;i+=1){
            document.getElementsByClassName('label-account-expire')[i].style.display = 'none';
        }
    } else {
        document.getElementById('days-account-expire').innerHTML = accountExpireDays;
    }
}
</script>
<script>
$(function(){
	new Clipboard('.copy-text');
});
$(".copy-text").click(function () {
	$("#result").modal();
	$("#msg").html("已拷贝订阅链接，请您继续接下来的操作。");
});
$(function(){
	new Clipboard('.reset-link');
});
$(".reset-link").click(function () {
	$("#result").modal();
	$("#msg").html("已重置您的订阅链接，请变更或添加您的订阅链接！");
	window.setTimeout("location.href='/user/url_reset'", {$config['jump_delay']});
});
 {if $user->transfer_enable-($user->u+$user->d) == 0}
window.onload = function() {
    $("#result").modal();
    $("#msg").html("您的流量已经用完或账户已经过期了，如需继续使用，请进入商店选购新的套餐~");
};
 {/if}

{if $geetest_html == null}

window.onload = function() {
    var myShakeEvent = new Shake({
        threshold: 15
    });
    myShakeEvent.start();
  	CountDown()
    window.addEventListener('shake', shakeEventDidOccur, false);
    function shakeEventDidOccur () {
		if("vibrate" in navigator){
			navigator.vibrate(500);
		}
        $.ajax({
                type: "POST",
                url: "/user/checkin",
                dataType: "json",
                success: function (data) {
                    $("#checkin-msg").html(data.msg);
                    $("#checkin-btn").hide();
					$("#result").modal();
                    $("#msg").html(data.msg);
                },
                error: function (jqXHR) {
					$("#result").modal();
                    $("#msg").html("发生错误：" + jqXHR.status);
                }
            });
    }
};

$(document).ready(function () {
	$("#checkin").click(function () {
		$.ajax({
			type: "POST",
			url: "/user/checkin",
			dataType: "json",
			success: function (data) {
				$("#checkin-msg").html(data.msg);
				$("#checkin-btn").hide();
				$("#result").modal();
				$("#msg").html(data.msg);
			},
			error: function (jqXHR) {
				$("#result").modal();
				$("#msg").html("发生错误：" + jqXHR.status);
			}
		})
	})
})

{else}

window.onload = function() {
    var myShakeEvent = new Shake({
        threshold: 15
    });
    myShakeEvent.start();
    window.addEventListener('shake', shakeEventDidOccur, false);
    function shakeEventDidOccur () {
		if("vibrate" in navigator){
			navigator.vibrate(500);
		}

        c.show();
    }
};

var handlerPopup = function (captchaObj) {
	c = captchaObj;
	captchaObj.onSuccess(function () {
		var validate = captchaObj.getValidate();
		$.ajax({
			url: "/user/checkin", // 进行二次验证
			type: "post",
			dataType: "json",
			data: {
				// 二次验证所需的三个值
				geetest_challenge: validate.geetest_challenge,
				geetest_validate: validate.geetest_validate,
				geetest_seccode: validate.geetest_seccode
			},
			success: function (data) {
				$("#checkin-msg").html(data.msg);
				$("#checkin-btn").hide();
				$("#result").modal();
				$("#msg").html(data.msg);
			},
			error: function (jqXHR) {
				$("#result").modal();
				$("#msg").html("发生错误：" + jqXHR.status);
			}
		});
	});
	// 弹出式需要绑定触发验证码弹出按钮
	captchaObj.bindOn("#checkin");
	// 将验证码加到id为captcha的元素里
	captchaObj.appendTo("#popup-captcha");
	// 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
};

initGeetest({
	gt: "{$geetest_html->gt}",
	challenge: "{$geetest_html->challenge}",
	product: "popup", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
	offline: {if $geetest_html->success}0{else}1{/if} // 表示用户后台检测极验服务器是否宕机，与SDK配合，用户一般不需要关注
}, handlerPopup);

{/if}

</script>
