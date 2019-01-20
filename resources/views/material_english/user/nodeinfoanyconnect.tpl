





{include file='user/header_info.tpl'}







	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Server Details</h1>
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
										<p class="card-heading">Attention!</p>
										<p>Below is your Anyconnect configuration.</p>
									</div>
									
								</div>
							</div>
						</div>			
						<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading">Configuration information</p>
										<p>{$json_show}</p>
									</div>
									
								</div>
							</div>
						</div>
                        
                        	<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading">Client Downloads</p>
										<p>Downloads are not available here due to copyright issues.</p>
									</div>
									
								</div>
							</div>
						</div>
						
												<div class="col-lg-12 col-sm-12">
							<div class="card">
								<div class="card-main">
									<div class="card-inner margin-bottom-no">
										<p class="card-heading">Configuration method</p>
										<p>Windows: After downloading the client installation, open the window, click the facility (gear) button in the lower left corner, uncheck the Block connections to untrusted servers, then enter the server address in the box, click connect, if the prompt box clicks connext anyway Yes, and fill in the username and password connection in the authentication box that pops up later.</p>
                                        <p>Mac OS X: After downloading the client installation, open the window, click the facility (gear) button in the lower left corner, uncheck the Block connections to untrusted servers, then enter the server address in the box, click connect, if the prompt box clicks connext anyway Yes, and fill in the username and password connection in the authentication box that pops up later.</p>
                                        <p>android: Open after downloading the client installation. Click Connect - add a new VPN connection, enter the server address in it (as shown in the configuration information), click Finish and select the new connection on the previous page, then return to the first page, there is a switch in the anyconnect VPN line Click the switch to open it. If a security warning appears, click Continue. Then enter the username and password in the authentication box that pops up, and connect. </p>
                                        <p>iOS: Open after downloading the client installation from the app store. Click Connect - add a new VPN connection, enter the server address in it (as shown in the configuration information), click Finish and select the new connection on the previous page, then return to the first page, there is a switch in the anyconnect VPN line Click the switch to open it. If a security warning appears, click Continue. Then enter the username and password in the authentication box that pops up, and connect.</p>
										<p>Windows Phone: Open the settings after downloading the client installation in the app store - Network - VPN - Add VPN connection, select anyconnect in the VPN provider column, the connection name is arbitrary, and the server name or address is filled in as shown in the configuration information. Address, then click Save. When connected, click the corresponding new VPN, click Connect, and then you will be prompted to enter your username and password. </p>
												
											</div>
										</div>
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




