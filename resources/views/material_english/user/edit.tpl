


{include file='user/main.tpl'}

	<main class="content">
		<div class="content-header ui-content-header">
			<div class="container">
				<h1 class="content-heading">Edit account details</h1>
			</div>
		</div>
		<div class="container">
			<section class="content-inner margin-top-no">
             


					<div class="col-xx-12 col-sm-6">
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
									<div class="cardbtn-edit">
										<div class="card-heading">Change Login Password</div>
										<button class="btn btn-flat" id="pwd-update"><span class="icon">check</span>&nbsp;</button>
									</div>
										<div class="form-group form-group-label">
											<label class="floating-label" for="oldpwd">Current password</label>
											<input class="form-control maxwidth-edit" id="oldpwd" type="password">
										</div>

										<div class="form-group form-group-label">
											<label class="floating-label" for="pwd">New password</label>
											<input class="form-control maxwidth-edit" id="pwd" type="password">
										</div>

										<div class="form-group form-group-label">
											<label class="floating-label" for="repwd">Confirm new password</label>
											<input class="form-control maxwidth-edit" id="repwd" type="password">
										</div>
									</div>
									
								</div>
							</div>
						</div>

						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Change VPN/Shadowsocks Password</div>
												<button class="btn btn-flat" id="ss-pwd-update"><span class="icon">check</span>&nbsp;</button>
										</div>
										
										<p>Current password: <code id="ajax-user-passwd">{$user->passwd}</code><button class="kaobei copy-text btn btn-subscription" type="button" data-clipboard-text="{$user->passwd}">Copy</button></p>
										<div class="form-group form-group-label">
											<label class="floating-label" for="sspwd">New password</label>
											<input class="form-control maxwidth-edit" id="sspwd" type="text">
										</div>
									</div>
								</div>
							</div>
						</div>
                      
                    
                      


						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Change encryption type</div>
												<button class="btn btn-flat" id="method-update"><span class="icon">check</span>&nbsp;</button>
										</div>
										<p>Note: Not all encryption types work with both Shadowsocks and ShadowsocksR.</p>
										<p>Current encryption: <code id="ajax-user-method" data-default="method">[{if URL::CanMethodConnect($user->method) == 2}SS/SSD{else}SS/SSR{/if} Support] {$user->method}</code></p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="method">Encryption</label>
											<button id="method" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->method}"></button>
											<ul class="dropdown-menu" aria-labelledby="method">
												{$method_list = $config_service->getSupportParam('method')}
												{foreach $method_list as $method}
												<li><a href="#" class="dropdown-option" onclick="return false;" val="{$method}" data="method">[{if URL::CanMethodConnect($method) == 2}SS/SSD{else}SS/SSR{/if} Support] {$method}</a></li>
												{/foreach}
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>  

						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Change Contact Details</div>
												<button class="btn btn-flat" id="wechat-update"><span class="icon">check</span>&nbsp;</button>
										</div>
										<p>Current contact:
										<code id="ajax-im" data-default="imtype">
										{if $user->im_type==1}
										Wechat
										{/if}

										{if $user->im_type==2}
										QQ
										{/if}

										{if $user->im_type==3}
										Google+
										{/if}

										{if $user->im_type==4}
										Telegram
										{/if}
										</code>
										</p>
										<p>Current contact account:
										<code>{$user->im_value}</code>
										</p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="imtype">How may we reach you?</label>
											<button class="form-control maxwidth-edit" id="imtype" data-toggle="dropdown" value="{$user->im_type}">

											</button>
											<ul class="dropdown-menu" aria-labelledby="imtype">
                                                <li><a href="#" class="dropdown-option" onclick="return false;" val="1" data="imtype">Wechat</a></li>
                                                <li><a href="#" class="dropdown-option" onclick="return false;" val="2" data="imtype">QQ</a></li>
                                                <li><a href="#" class="dropdown-option" onclick="return false;" val="3" data="imtype">Facebook</a></li>
                                                <li><a href="#" class="dropdown-option" onclick="return false;" val="4" data="imtype">Telegram</a></li>
											</ul>
										</div>

										<div class="form-group form-group-label">
											<label class="floating-label" for="wechat">Enter your contact here</label>
											<input class="form-control maxwidth-edit" id="wechat" type="text">
										</div>
									</div>
								</div>
							</div>
						</div>



						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Protocol & Obfuscation</div>
												<button class="btn btn-flat" id="ssr-update"><span class="icon">check</span>&nbsp;</button>
										</div>
										<p>Current protocol: <code id="ajax-user-protocol" data-default="protocol">[{if URL::CanProtocolConnect($user->protocol) == 3}SS/SSD/SSR{else}SSR{/if} Support] {$user->protocol}</code></p>
										<p>Note 1: If you need the protocol to be compatible with the original version of Shadowsocks, Please only choose  _compatible options.</p>
										<p>Note 2: If you only use the original Shadowsocks client here please set it directly as origin.</p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="protocol">Protocol</label>
											<button id="protocol" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->protocol}"></button>
											<ul class="dropdown-menu" aria-labelledby="protocol">
												{$protocol_list = $config_service->getSupportParam('protocol')}
												{foreach $protocol_list as $protocol}
												<li><a href="#" class="dropdown-option" onclick="return false;" val="{$protocol}" data="protocol">[{if URL::CanProtocolConnect($protocol) == 3}SS/SSD/SSR{else}SSR{/if} Support] {$protocol}</a></li>
												{/foreach}
											</ul>
										</div>

									</div>

									<div class="card-inner">
										<p>Current obfuscation: <code id="ajax-user-obfs" data-default="obfs">[{if URL::CanObfsConnect($user->obfs) >= 3}SS/SSD/SSR{elseif URL::CanObfsConnect($user->obfs) == 1}SSR{else}SS/SSD{/if} Support] {$user->obfs}</code></p>
										<p>Note 1: If you need it to be compatible with the original version of Shadowsocks, Please only choose _compatible options.</p>
										<p>Note 2: SS and SSR support different obfuscation types, simple_obfs_ * types will only work with the original version of Shadowsocks, while the rest will only work on ShadowsocksR.</p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="obfs">Obfuscation type</label>
											<button id="obfs" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->obfs}"></button>
											<ul class="dropdown-menu" aria-labelledby="obfs">
											{$obfs_list = $config_service->getSupportParam('obfs')}
											{foreach $obfs_list as $obfs}
											<li><a href="#" class="dropdown-option" onclick="return false;" val="{$obfs}" data="obfs">[{if URL::CanObfsConnect($obfs) >= 3}SS/SSD/SSR{else}{if URL::CanObfsConnect($obfs) == 1}SSR{else}SS/SSD{/if}{/if} Support] {$obfs}</a></li>
											{/foreach}
										    </ul>
										</div>
									</div>

									<div class="card-inner">
										<p>Current obfuscation parameters: <code id="ajax-user-obfs-param">{$user->obfs_param}</code></p>
										<div class="form-group form-group-label">
											<label class="floating-label" for="obs-param">Input confusion parameter here</label>
											<input class="form-control maxwidth-edit" id="obfs-param" type="text">
										</div>
									</div>

								</div>
							</div>
						</div>  







						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Change website language</div>
												<button class="btn btn-flat" id="theme-update"><span class="icon">check</span>&nbsp;</button>
										</div>
										<p>Current theme: <code data-default="theme">{$user->theme}</code></p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="theme">theme</label>
											<button id="theme" type="button" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->theme}">
												
											</button>
											<ul class="dropdown-menu" aria-labelledby="mail">
												{foreach $themes as $theme}
												<li><a href="#" class="dropdown-option" onclick="return false;" val="{$theme}" data="theme">{$theme}</a></li>
												{/foreach}
											</ul>
										</div>
								        </div>
							        </div>
						        </div> 
                            </div>
				        </div>  


					<div class="col-xx-12 col-sm-6">

						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">IP Blocking</div>
												<button class="btn btn-flat" id="unblock"><span class="icon">not_interested</span>&nbsp;</button>
										</div>
										<p>Current state: <code id="ajax-block">{$Block}</code></p>

									</div>
								</div>
							</div>
						</div> 

	

						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Daily status updates by email</div>
												<button class="btn btn-flat" id="mail-update"><span class="icon">check</span>&nbsp;</button>
										</div>
										<p class="card-heading"></p>
										<p>Current setting: <code id="ajax-mail" data-default="mail">{if $user->sendDailyMail==1} Send {else} Do not send {/if}</code></p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="mail">Send settings</label>
											<button type="button" id="mail" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->sendDailyMail}">
												
											</button>
											<ul class="dropdown-menu" aria-labelledby="mail">
												<li><a href="#" class="dropdown-option" onclick="return false;" val="1" data="mail">Send</a> </li>
												<li><a href="#" class="dropdown-option" onclick="return false;" val="0" data="mail">Do not send</a></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>



						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
										<p class="card-heading">Two-step Verification</p>
										<p>Please download Goodle's two step verification engine, and scan the qr code below.</p>
										<p><i class="icon icon-lg" aria-hidden="true">android</i><a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">&nbsp;Android</a></p>
										<p><i class="icon icon-lg" aria-hidden="true">tablet_mac</i><a href="https://itunes.apple.com/cn/app/google-authenticator/id388497605?mt=8">&nbsp;iOS</a></p>
										<p>If you haven't completed the two step verificatoin procedure, please do not enable.</p>
										<p>Current settings: <code data-default="ga-enable">{if $user->ga_enable==1} enabled {else} disabled {/if}</code></p>
										<p>Current time: {date("Y-m-d H:i:s")}</p>
										<div class="form-group form-group-label control-highlight-custom dropdown">
											<label class="floating-label" for="ga-enable">Enable/disable</label>
											<button type="button" id="ga-enable" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->ga_enable}">

											</button>
											<ul class="dropdown-menu" aria-labelledby="ga-enable">
												<li><a href="#" class="dropdown-option" onclick="return false;" val="0" data="ga-enable">Disable</a> </li>
												<li><a href="#" class="dropdown-option" onclick="return false;" val="1" data="ga-enable">Enable</a></li>
											</ul>
										</div>


										<div class="form-group form-group-label">
											<div class="text-center">
												<div id="ga-qr" class="qr-center"></div>
												Verification Token: {$user->ga_token}
											</div>
										</div>


										<div class="form-group form-group-label">
											<label class="floating-label" for="code">Test</label>
											<input type="text" id="code" placeholder="Input the code here" class="form-control maxwidth-edit">
										</div>

									</div>
									<div class="card-action">
										<div class="card-action-btn pull-left">
											<a class="btn btn-brand-accent btn-flat waves-attach" href="/user/gareset"><span class="icon">format_color_reset</span>&nbsp;Reset</a>
											<button class="btn btn-flat waves-attach" id="ga-test" ><span class="icon">extension</span>&nbsp;Test</button>
											<button class="btn btn-brand btn-flat waves-attach" id="ga-set" ><span class="icon">perm_data_setting</span>&nbsp;Setup</button>
										</div>
									</div>
								</div>
							</div>
						</div>    

						{if $config['port_price']>=0 || $config['port_price_specify']>=0}
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									{if $config['port_price']>=0}
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Reset Port</div>
												<button class="btn btn-flat" id="portreset"><span class="icon">autorenew</span>&nbsp;</button>
										</div>
										<p>Randomly reset a port, price: <code>{$config['port_price']}</code>CNY</p>
										<p>Effective within 1 minute after reset</p>
										<p>Current port: <code id="ajax-user-port">{$user->port}</code></p>
									</div>
									{/if}

									{if $config['port_price_specify']>=0}
									<div class="card-inner">
										<div class="cardbtn-edit">
												<div class="card-heading">Special port</div>
												<button class="btn btn-flat" id="portspecify"><span class="icon">call_made</span>&nbsp;</button>
										</div>
										<p>Get a special port, price: <code>{$config['port_price_specify']}</code>CNY</p>
										<p>Port range: <code>{$config['min_port']}～{$config['max_port']}</code></p>
										<div class="form-group form-group-label">
											<label class="floating-label" for="port-specify">Input port you want</label>
											<input class="form-control maxwidth-edit" id="port-specify" type="num">
										</div>
									</div>
									
									{/if}
								</div>
							</div>
						</div>
						{/if}

						{if $config['enable_telegram'] == 'true'}
						<div class="card margin-bottom-no">
							<div class="card-main">
								<div class="card-inner">
									<div class="card-inner">
									{if $user->telegram_id != 0}
										<div class="cardbtn-edit">
												<div class="card-heading">Telegram Bind</div>
												<div><a class="btn btn-flat btn-brand-accent" href="/user/telegram_reset"><span class="icon">not_interested</span>&nbsp;</a></div>
										</div>{/if}
                                      {if $user->telegram_id == 0}
										<p>Telegram Add a robot account <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a>, Photographed the following two-dimensional code issued to it.</p>
										<div class="form-group form-group-label">
											<div class="text-center">
												<div id="telegram-qr" class="qr-center"></div>
												{elseif $user->telegram_id != 0}
												Current binding: <a href="https://t.me/{$user->im_value}">@{$user->im_value}</a>
												{/if}
									        </div>
									    </div>
								    </div>
							    </div>
						    </div>
					    </div>
						{/if}
					




					{include file='dialog.tpl'}

			</section>
		</div>
	</main>







{include file='user/footer.tpl'}

<script>
$(function(){
	new Clipboard('.copy-text');
});

$(".copy-text").click(function () {
	$("#result").modal();
	$("#msg").html("It has been copied to your clipboard.");
});


</script>

<script>
    $(document).ready(function () {
        $("#portreset").click(function () {
            $.ajax({
                type: "POST",
                url: "resetport",
                dataType: "json",
                data: {

                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-user-port").html(data.msg);
						$("#msg").html("Success, the new port is"+data.msg);
						
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#portspecify").click(function () {
            $.ajax({
                type: "POST",
                url: "specifyport",
                dataType: "json",
                data: {
					port: $("#port-specify").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-user-port").html($("#port-specify").val());
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#setpac").click(function () {
            $.ajax({
                type: "POST",
                url: "pacset",
                dataType: "json",
                data: {
                   pac: $("#pac").text()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $("#pwd-update").click(function () {
            $.ajax({
                type: "POST",
                url: "password",
                dataType: "json",
                data: {
                    oldpwd: $("#oldpwd").val(),
                    pwd: $("#pwd").val(),
                    repwd: $("#repwd").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>

<script>
	var ga_qrcode = '{$user->getGAurl()}',
	qrcode1 = new QRCode(document.getElementById("ga-qr"));
	
    qrcode1.clear();
    qrcode1.makeCode(ga_qrcode);

	{if $config['enable_telegram'] == 'true'}

	var telegram_qrcode = 'mod://bind/{$bind_token}';

	if ($$.getElementById("telegram-qr")) {
		let qrcode2 = new QRCode(document.getElementById("telegram-qr"));
		qrcode2.clear();
		qrcode2.makeCode(telegram_qrcode);
	}

	{/if}
</script>


<script>
    $(document).ready(function () {
        $("#wechat-update").click(function () {
            $.ajax({
                type: "POST",
                url: "wechat",
                dataType: "json",
                data: {
                    wechat: $("#wechat").val(),
					imtype: $("#imtype").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-im").html($("#imtype").find("option:selected").text()+" "+$("#wechat").val());
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $("#ssr-update").click(function () {
            $.ajax({
                type: "POST",
                url: "ssr",
                dataType: "json",
                data: {
                    protocol: $("#protocol").val(),
					obfs: $("#obfs").val(),
					obfs_param: $("#obfs-param").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-user-protocol").html($("#protocol").val());
						$("#ajax-user-obfs").html($("#obfs").val());
						$("#ajax-user-obfs-param").html($("#obfs-param").val());
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>


<script>
    $(document).ready(function () {
        $("#relay-update").click(function () {
            $.ajax({
                type: "POST",
                url: "relay",
                dataType: "json",
                data: {
                    relay_enable: $("#relay_enable").val(),
					relay_info: $("#relay_info").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $("#unblock").click(function () {
            $.ajax({
                type: "POST",
                url: "unblock",
                dataType: "json",
                data: {
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-block").html("IP: "+data.msg+" is not banned");
						$("#msg").html("Send unblock command to unpack "+data.msg+" successfully");
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>


<script>
    $(document).ready(function () {
        $("#ga-test").click(function () {
            $.ajax({
                type: "POST",
                url: "gacheck",
                dataType: "json",
                data: {
                    code: $("#code").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>


<script>
    $(document).ready(function () {
        $("#ga-set").click(function () {
            $.ajax({
                type: "POST",
                url: "gaset",
                dataType: "json",
                data: {
                    enable: $("#ga-enable").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>


<script>
    $(document).ready(function () {
        $("#ss-pwd-update").click(function () {
            $.ajax({
                type: "POST",
                url: "sspwd",
                dataType: "json",
                data: {
                    sspwd: $("#sspwd").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-user-passwd").html($("#sspwd").val());
						$("#msg").html("Success");
                    } else {
                        $("#result").modal();
						$("#msg").html("Fail");
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>


<script>
    $(document).ready(function () {
        $("#mail-update").click(function () {
            $.ajax({
                type: "POST",
                url: "mail",
                dataType: "json",
                data: {
                    mail: $("#mail").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#ajax-mail").html($("#mail").val()=="1"?"Send":"Do not send");
						$("#msg").html(data.msg);
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>

<script>
    $(document).ready(function () {
        $("#theme-update").click(function () {
            $.ajax({
                type: "POST",
                url: "theme",
                dataType: "json",
                data: {
                    theme: $("#theme").val()
                },
                success: function (data) {
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html(data.msg);
						window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>



<script>
    $(document).ready(function () {
        $("#method-update").click(function () {
            $.ajax({
                type: "POST",
                url: "method",
                dataType: "json",
                data: {
                    method: $("#method").val()
                },
                success: function (data) {
					$("#ajax-user-method").html($("#method").val());
                    if (data.ret) {
                        $("#result").modal();
						$("#msg").html("Success");
                    } else {
                        $("#result").modal();
						$("#msg").html(data.msg);
                    }
                },
                error: function (jqXHR) {
                    $("#result").modal();
					$("#msg").html(data.msg+"     An error occurred.");
                }
            })
        })
    })
</script>

