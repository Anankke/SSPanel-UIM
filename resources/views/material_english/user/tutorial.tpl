{include file='user/main.tpl'}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Use tutorial</h1>
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
													<a class="" data-toggle="tab" href="#all_ssr_router"><i class="icon icon-lg">router</i>&nbsp;Router</a>
												</li>
												<li>
													<a class="" data-toggle="tab" href="#all_ssr_game"><i class="icon icon-lg">videogame_asset</i>&nbsp;Game</a>
												</li>
											</ul>
										</nav>
                                      
										<div class="tab-pane fade active in page-course" id="all_ssr_windows">
											<ul>
												<h3><li>Download software</li> </h3>
												<ol>
													<li>Click on the left user center (the phone needs to click the upper left button to bring up the navigation menu)</li>
													<li>Find the quick add node</li>
													<li>Click to download the client</li>
													<p><img src="/images/c_win_1.png"/></p>
												</ol>
											</ul>
											<ul>
												<h3><li>Import node</li> </h3>
												<ul>
													<li>Unzip the client and double-click the client of shadowsocksr4.0</li>
													<li>Method One:</li>
													<ol>
														<li>Find the [alternate node import method] in the quick add node.</li>
														<li>Click on the link</li>
														<p><img src="/images/c_win_2.png"/></p>
														<li>Find the SSR aircraft icon in the system tray menu and right click to bring up the menu.</li>
														<li>Click on the clipboard to import ssr://links in bulk</li>
														<p><img src="/images/c_win_3.png"/></p>
													</ol>
													<li>Method Two (recommended):</li>
													<ol>
														<li>Find the node subscription address in the quick add node</li>
														<li>Click the button to copy the subscription link</li>
														<p><img src="/images/c_win_4.png"/></p>
														<li>Find the SSR aircraft icon in the system tray menu and right click to bring up the menu.</li>
														<li>Open the SSR server subscription link settings</li>
														<p><img src="/images/c_win_5.png"/></p>
														<li>Click add to add a subscription, fill the copied link into the box on the right and click OK.</li>
														<p><img src="/images/c_win_6.png"/></p>
														<li>Find the SSR aircraft icon in the system tray menu and right click to bring up the menu.</li>
														<li>Click to update SSR server subscription (not through proxy)</li>
														<p><img src="/images/c_win_7.png"/></p>
													</ol>
												</ul>
											</ul>
											<ul>
												<h3><li>选择节点</li></h3>
												<ol>
													<li>Find the SSR aircraft icon in the system tray menu and right click to bring up the menu.</li>
													<li>Server -> find the node group of this site -> select a node</li>
													<p><img src="/images/c_win_8.png"/></p>
													<li>Open your browser to access www.google.com!</li>
												</ol>
												<ul>The above tutorials are all steps in the computer that have not installed any proxy software. If other proxy software is installed, conflicts may occur.</ul>
											</ul>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_mac">
											<p>1：Put the downloaded DMG package into the application list</p>
											<p><img src="/images/c_mac_1.png"/></p>
											<p>2：Open program</p>
											<p><img src="/images/c_mac_2.png"/></p>
											<p>3：If the prompt is not safe, please go to the system preferences to open the program.</p>
											<p><img src="/images/c_mac_3.png"/></p>
											<p>4：Server-edit subscription</p>
											<p><img src="/images/c_mac_4.png"/></p>
											<p>5：Click the + and fill in the subscription link to manually update the subscription.</p>
											<p><img src="/images/c_mac_5.png"/></p>
											<p><img src="/images/c_mac_4.png"/></p>
											<p>6：Select a node</p>
											<p><img src="/images/c_mac_6.png"/></p>
											<p>7：Open your browser to access www.google.com!</p>
											<p><img src="/images/c_mac_7.png"/></p>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_linux">
											<h3>Ubuntu uses Shadowsocks-qt5 to access the Internet</h3>
											<h4>Description: shadowsocks-qt5 is a visual version of Ubuntu</h4>
											<hr/>
											<h5>Install shadowsocks-qt5</h5>
											<pre><code>1.$ sudo add-apt-repository ppa:hzwhuang/ss-qt5
												2.$ sudo apt-get update
												3.$ sudo apt-get install shadowsocks-qt5</code></pre>
											<h5>If the installation is successful, press <code>win</code> to search for the software, as shown below:</h5>
											<p><img src="/images/c-linux-1.png"/></p>
											<h5>Configure shadowsocks-qt5</h5>
											<h6>Fill in the corresponding server IP, port, password, encryption method, red marked place, please like the picture</h6>
											<p><img src="/images/c-linux-4.png"/></p>
											<h5>Configuring system proxy mode</h5>
											<p><img src="/images/c-linux-5.png"/></p>
											<h5>Configure browser proxy mode (this is an example of Ubuntu's own FireFox browser)</h5>
											<p><img src="/images/c-linux-6.png"/></p>
											<h5>Connect and start surfing</h5>
											<p><img src="/images/c-linux-7.png"/></p>
											<hr/>
											<p>This tutorial is compiled by 仟佰星云试验. Please reprint this link.</p>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_ios">
											<p>1：Go to the User Center to view the App Store account, the China App Store has been removed</p>
											<p><img src="/images/c_ios_1.jpg"/></p>
											<p>2：Open the App Store to switch accounts and download apps</p>
											<p><img src="/images/c_ios_2.jpg"/></p>
											<p>3：Open Safari and log in to the User Center Import node of {$config["appName"]}</p>
											<p><img src="/images/c_ios_3.jpg"/></p>
											<p>Additional: iOS Quick Connect</p>
											<p><img src="/images/c_ios_4.jpg"/></p>
										</div>

										<div class="tab-pane fade page-course" id="all_ssr_android">
											<p>1：Download app</p>
											<p><img src="/images/c_android_1.jpg"/></p>
											<p>2：Add a subscription and update</p>
											<p><img src="/images/c_android_2.jpg"/></p>
											<p><img src="/images/c_android_3.jpg"/></p>
											<p><img src="/images/c_android_4.jpg"/></p>
											<p><img src="/images/c_android_5.jpg"/></p>
											<p>3：Select a node and set up routing</p>
											<p><img src="/images/c_android_6.jpg"/></p>
											<p><img src="/images/c_android_7.jpg"/></p>
											<p>4：Connect</p>
											<p><img src="/images/c_android_8.jpg"/></p>
											<p>Note: The Chinese Android system is a customized system. If you need Youtube, Google Suite, etc., you need to install the Google framework. How to install the specific models is different, please find the tutorial directly.</p>
										</div>

										<div class="tab-pane fade" id="all_ssr_router">
											<h2 class="major">Router</h2>
										</div>  
										
										<div class="tab-pane fade" id="all_ssr_game">
											<h2 class="major">Game</h2>
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



