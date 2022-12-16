{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">设置中心</h1>
        </div>
    </div>

    <div class="container">
        <div class="col-xx-12 col-sm-12">
            <div class="card quickadd">
                <div class="card-main">
                    <div class="card-inner">
                        <nav class="tab-nav margin-top-no">
                            <ul class="nav nav-list">
                                <li class="active">
                                    <a data-toggle="tab" href="#payment_settings"><i class="mdi mdi-credit-card-outline icon-lg"></i>&nbsp;支付</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#mail_settings"><i class="mdi mdi-email icon-lg"></i>&nbsp;邮件</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#customer_service_system_settings"><i class="mdi mdi-face-agent icon-lg"></i>&nbsp;客服</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#verification_code_settings"><i class="mdi mdi-shield-check icon-lg"></i>&nbsp;验证</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#registration_settings"><i class="mdi mdi-account-plus icon-lg"></i>&nbsp;注册</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#invitation_settings"><i class="mdi mdi-account-multiple-plus icon-lg"></i>&nbsp;邀请</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#telegram_settings"><i class="mdi mdi-send-circle icon-lg"></i>&nbsp;Telegram</a>
                                </li>
                            </ul>
                        </nav>
                                
                        <div class="card-inner">
                           <div class="tab-content">
                                <div class="tab-pane fade" id="mail_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#email_auth_settings">&nbsp;设置</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#smtp">&nbsp;smtp</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#sendgrid">&nbsp;sendgrid</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#mailgun">&nbsp;mailgun</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#ses">&nbsp;ses</a>
                                            </li>
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="email_auth_settings">
                                        <!-- mail_driver -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">邮件服务</label>
                                            <select id="mail_driver" class="form-control maxwidth-edit">
                                                <option value="none" {if $settings['mail_driver'] == "none"}selected{/if}>none</option>
                                                <option value="mailgun" {if $settings['mail_driver'] == "mailgun"}selected{/if}>mailgun</option>
                                                <option value="sendgrid" {if $settings['mail_driver'] == "sendgrid"}selected{/if}>sendgrid</option>
                                                <option value="ses" {if $settings['mail_driver'] == "ses"}selected{/if}>ses</option>
                                                <option value="smtp" {if $settings['mail_driver'] == "smtp"}selected{/if}>smtp</option>
                                            </select>
                                        </div>

                                        <button id="submit_mail" type="submit" class="btn  btn-brand btn-dense">提交</button>

                                        <!-- smtp_test_recipient -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">测试邮件收件人</label>
                                            <input class="form-control maxwidth-edit" id="testing_email_recipients">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>邮件配置保存完成后，如需验证是否可用，可在上方填写一个有效邮箱，系统将发送一封测试邮件到该邮箱。如果能够正常接收，则说明配置可用</p>
                                            {if $settings['mail_driver'] == "none"}
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>如需使用发信测试功能，请先在上方选择一个发信方式，并配置有效的相关参数</p>
                                            {/if}
                                        </div>
                                        
                                        <button id="submit_email_test" type="submit" class="btn btn-brand btn-dense" {if $settings['mail_driver'] == "none"}disabled{/if}>测试</button>
                                    </div>
                                    <div class="tab-pane fade" id="smtp">
                                        <!-- smtp_host -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP主机地址</label>
                                            <input class="form-control maxwidth-edit" id="smtp_host" value="{$settings['smtp_host']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>例如：smtpdm-ap-southeast-1.aliyun.com</p>
                                        </div>
                                        <!-- smtp_username -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP账户名</label>
                                            <input class="form-control maxwidth-edit" id="smtp_username" value="{$settings['smtp_username']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>例如：no-reply@airport.com</p>
                                        </div>
                                        <!-- smtp_password -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP账户密码</label>
                                            <input class="form-control maxwidth-edit" id="smtp_password" value="{$settings['smtp_password']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>如果你使用 QQ 邮箱或 163 邮箱，此处应当填写单独的授权码</p>
                                        </div>
                                        <!-- smtp_port -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP端口</label>
                                            <select id="smtp_port" class="form-control maxwidth-edit">
                                                <option value="465" {if $settings['smtp_port'] == "465"}selected{/if}>465</option>
                                                <option value="587" {if $settings['smtp_port'] == "587"}selected{/if}>587</option>
                                                <option value="2525" {if $settings['smtp_port'] == "2525"}selected{/if}>2525</option>
                                                <option value="25" {if $settings['smtp_port'] == "25"}selected{/if}>25</option>
                                            </select>
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>常见端口一般就这些</p>
                                        </div>
                                        <!-- smtp_name -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP发信名称</label>
                                            <input class="form-control maxwidth-edit" id="smtp_name" value="{$settings['smtp_name']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>这里的设置在邮箱的邮件列表中可见。你可以设置为网站名称</p>
                                        </div>
                                        <!-- smtp_sender -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP账户发信地址</label>
                                            <input class="form-control maxwidth-edit" id="smtp_sender" value="{$settings['smtp_sender']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>如不知道填什么，请与此项保持一致：SMTP账户名</p>
                                        </div>
                                        <!-- smtp_ssl -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">是否使用 TLS/SSL 发信</label>
                                            <select id="smtp_ssl" class="form-control maxwidth-edit">
                                                <option value="1" {if $settings['smtp_ssl'] == true}selected{/if}>开启</option>
                                                <option value="0" {if $settings['smtp_ssl'] == false}selected{/if}>关闭</option>
                                            </select>
                                        </div>
                                        <!-- smtp_bbc -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">发给用户的邮件密送给指定邮箱备份</label>
                                            <input class="form-control maxwidth-edit" id="smtp_bbc" value="{$settings['smtp_bbc']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>如无需使用此功能，请留空</p>
                                        </div>

                                        <button id="submit_smtp" type="submit" class="btn  btn-brand btn-dense">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="sendgrid">
                                        <!-- sendgrid_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Sendgrid 密钥</label>
                                            <input class="form-control maxwidth-edit" id="sendgrid_key" value="{$settings['sendgrid_key']}">
                                        </div>
                                        <!-- sendgrid_sender -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Sendgrid 发件邮箱</label>
                                            <input class="form-control maxwidth-edit" id="sendgrid_sender" value="{$settings['sendgrid_sender']}">
                                        </div>
                                        <!-- sendgrid_name -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Sendgrid 发件人名称</label>
                                            <input class="form-control maxwidth-edit" id="sendgrid_name" value="{$settings['sendgrid_name']}">
                                        </div>

                                        <button id="submit_sendgrid" type="submit" class="btn btn-brand btn-dense">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="mailgun">
                                        <!-- mailgun_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Mailgun 密钥</label>
                                            <input class="form-control maxwidth-edit" id="mailgun_key" value="{$settings['mailgun_key']}">
                                        </div>
                                        <!-- mailgun_domain -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Mailgun 域名</label>
                                            <input class="form-control maxwidth-edit" id="mailgun_domain" value="{$settings['mailgun_domain']}">
                                        </div>
                                        <!-- mailgun_sender -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Mailgun 发送者</label>
                                            <input class="form-control maxwidth-edit" id="mailgun_sender" value="{$settings['mailgun_sender']}">
                                        </div>

                                        <button id="submit_mailgun" type="submit" class="btn btn-brand btn-dense">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="ses">
                                        <!-- aws_access_key_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">AWS 密钥 ID</label>
                                            <input class="form-control maxwidth-edit" id="aws_access_key_id" value="{$settings['aws_access_key_id']}">
                                        </div>
                                        <!-- aws_secret_access_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">AWS 密钥 KEY</label>
                                            <input class="form-control maxwidth-edit" id="aws_secret_access_key" value="{$settings['aws_secret_access_key']}">
                                        </div>
                                        <!-- aws_region -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">AWS 区域</label>
                                            <input class="form-control maxwidth-edit" id="aws_region" value="{$settings['aws_region']}">
                                        </div>
                                        <!-- aws_ses_sender -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">AWS SES 发送者</label>
                                            <input class="form-control maxwidth-edit" id="aws_ses_sender" value="{$settings['aws_ses_sender']}">
                                        </div>

                                        <button id="submit_ses" type="submit" class="btn btn-brand btn-dense">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade active in" id="payment_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#public_payment_settings">&nbsp;设置</a>
                                            </li>
                                            {foreach $payment_gateways as $key => $value}
                                            <li>
                                                <a data-toggle="tab" href="#{$value}">{$key}</a>
                                            </li>
                                            {/foreach}
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="public_payment_settings">
                                        <div class="form-group form-group-label">
                                        {foreach $payment_gateways as $key => $value}
                                            <div class="checkbox switch">
                                                <label for="{$value}_switch">
                                                    <input class="access-hide" type="checkbox" id="{$value}_switch" name="{$value}_switch"
                                                    {if in_array($value, $active_payment_gateway)}
                                                    checked
                                                    {/if}
                                                    ><span class="switch-toggle"></span>{$key}
                                                </label>
                                            </div>
                                        {/foreach}
                                        </div>

                                        <button id="submit_payment" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>

                                    <div class="tab-pane fade" id="payjs">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>此处申请： <a href="https://payjs.cn" target="view_window">https://payjs.cn</a></p>
                                        <!-- payjs_url -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">payjs_url</label>
                                            <input class="form-control maxwidth-edit" id="payjs_url" value="{$settings['payjs_url']}">
                                        </div>
                                        <!-- payjs_mchid -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">payjs_mchid</label>
                                            <input class="form-control maxwidth-edit" id="payjs_mchid" value="{$settings['payjs_mchid']}">
                                        </div>
                                        <!-- payjs_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">payjs_key</label>
                                            <input class="form-control maxwidth-edit" id="payjs_key" value="{$settings['payjs_key']}">
                                        </div>

                                        <button id="submit_payjs_pay" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>

                                    <div class="tab-pane fade" id="paymentwall">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>此处申请： <a href="https://www.paymentwall.com/cn" target="view_window">https://www.paymentwall.com/cn</a></p>
                                        <!-- pmw_publickey -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">pmw公钥</label>
                                            <textarea class="form-control maxwidth-edit" id="pmw_publickey" rows="5">{$settings['pmw_publickey']}</textarea>
                                        </div>
                                        <!-- pmw_privatekey -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">pmw私钥</label>
                                            <textarea class="form-control maxwidth-edit" id="pmw_privatekey" rows="7">{$settings['pmw_privatekey']}</textarea>
                                        </div>
                                        <!-- pmw_widget -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">pmw_widget</label>
                                            <input class="form-control maxwidth-edit" id="pmw_widget" value="{$settings['pmw_widget']}">
                                        </div>
                                        <!-- pmw_height -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">pmw_height</label>
                                            <input class="form-control maxwidth-edit" id="pmw_height" value="{$settings['pmw_height']}">
                                        </div>

                                        <button id="submit_paymentwall" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    
                                    <div class="tab-pane fade" id="theadpay">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>此处申请：<a href="https://theadpay.com" target="view_window">https://theadpay.com</a></p>
                                        <!-- theadpay_url -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">theadpay_url</label>
                                            <input class="form-control maxwidth-edit" id="theadpay_url" value="{$settings['theadpay_url']}">
                                        </div>
                                        <!-- theadpay_mchid -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">theadpay_mchid</label>
                                            <input class="form-control maxwidth-edit" id="theadpay_mchid" value="{$settings['theadpay_mchid']}">
                                        </div>
                                        <!-- theadpay_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">theadpay_key</label>
                                            <input class="form-control maxwidth-edit" id="theadpay_key" value="{$settings['theadpay_key']}">
                                        </div>

                                        <button id="submit_theadpay" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>

                                    <div class="tab-pane fade" id="stripe_card">
                                        <p class="form-control-guide"><i class="mdi mdi-alert"></i>提供虚拟专用网络业务符合 Stripe 用户协议，但可能不符合 Stripe 提供的部分支付通道（如支付宝、微信）用户协议，相关支付通道可能存在被关闭的风险</p>
                                        <h5>支付渠道</h5>
                                        <!-- stripe_card_select -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">银行卡支付</label>
                                            <select id="stripe_card_select" class="form-control maxwidth-edit">
                                                <option value="0">停用</option>
                                                <option value="1" {if $settings['stripe_card'] == true}selected{/if}>
                                                    启用
                                                </option>
                                            </select>
                                        </div>
                                        <h5>支付设置</h5>
                                        <!-- stripe_currency -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">货币单位</label>
                                            <input class="form-control maxwidth-edit" id="stripe_currency" value="{$settings['stripe_currency']}">
                                        </div>
                                        <!-- stripe_min_recharge -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">最低充值限额（整数）</label>
                                            <input class="form-control maxwidth-edit" id="stripe_min_recharge" value="{$settings['stripe_min_recharge']}">
                                        </div>
                                        <!-- stripe_max_recharge -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">最高充值限额（整数）</label>
                                            <input class="form-control maxwidth-edit" id="stripe_max_recharge" value="{$settings['stripe_max_recharge']}">
                                        </div>
                                        <!-- stripe_pk -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">stripe_pk</label>
                                            <input class="form-control maxwidth-edit" id="stripe_pk" value="{$settings['stripe_pk']}">
                                        </div>
                                        <!-- stripe_sk -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">stripe_sk</label>
                                            <input class="form-control maxwidth-edit" id="stripe_sk" value="{$settings['stripe_sk']}">
                                        </div>
                                        <!-- stripe_webhook_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">WebHook密钥</label>
                                            <input class="form-control maxwidth-edit" id="stripe_webhook_key" value="{$settings['stripe_webhook_key']}">
                                        </div>

                                        <button id="submit_stripe" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    
                                    <div class="tab-pane fade" id="vmqpay">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>此支付方式需自建网关并配置各项参数。访问 <a href="https://github.com/szvone/vmqphp" target="view_window">https://github.com/szvone/vmqphp</a> 了解更多</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>开源的 Android 监听端（推荐）：<a href="https://gitee.com/yuniks/VMQAPK" target="view_window">https://gitee.com/yuniks/VMQAPK</a></p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>不开源的 Windows 监听端（不推荐）：<a href="https://toscode.gitee.com/pmhw/Vpay" target="view_window">https://toscode.gitee.com/pmhw/Vpay</a></p>
                                        <!-- vmq_gateway -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">V免签网关</label>
                                            <input class="form-control maxwidth-edit" id="vmq_gateway" value="{$settings['vmq_gateway']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>形如：https://pay.com</p>
                                        </div>
                                        <!-- vmq_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">V免签密钥</label>
                                            <input class="form-control maxwidth-edit" id="vmq_key" value="{$settings['vmq_key']}">
                                        </div>
                                        
                                        <button id="submit_vmq_pay" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    
                                    <div class="tab-pane fade" id="f2fpay">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>此处申请： <a href="https://b.alipay.com/signing/productDetailV2.htm?productId=I1011000290000001003" target="view_window">https://b.alipay.com/signing/productDetailV2.htm?productId=I1011000290000001003</a></p>
                                        <!-- f2f_pay_app_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">App ID</label>
                                            <input class="form-control maxwidth-edit" id="f2f_pay_app_id" value="{$settings['f2f_pay_app_id']}">
                                        </div>
                                        <!-- f2f_pay_pid -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">PID</label>
                                            <input class="form-control maxwidth-edit" id="f2f_pay_pid" value="{$settings['f2f_pay_pid']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>此项可留空，不影响使用</p>
                                        </div>
                                        <!-- f2f_pay_public_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">公钥</label>
                                            <textarea class="form-control maxwidth-edit" id="f2f_pay_public_key" rows="4">{$settings['f2f_pay_public_key']}</textarea>
                                        </div>
                                        <!-- f2f_pay_private_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">私钥</label>
                                            <textarea class="form-control maxwidth-edit" id="f2f_pay_private_key" rows="12">{$settings['f2f_pay_private_key']}</textarea>
                                        </div>
                                        <!-- f2f_pay_notify_url -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">自定义回调地址</label>
                                            <input class="form-control maxwidth-edit" id="f2f_pay_notify_url" value="{$settings['f2f_pay_notify_url']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>此项可留空，不影响使用</p>
                                        </div>
                                        
                                        <button id="submit_f2f_pay" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
								
								<div class="tab-pane fade" id="epay">
                                        <!-- epay_url -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">易支付URL</label>
                                            <input class="form-control maxwidth-edit" id="epay_url" value="{$settings['epay_url']}">
											<p class="form-control-guide"><i class="mdi mdi-information"></i>不同易支付url后缀不同，1：域名后面带/ 2：域名后面带submit.php/</p>
                                        </div>
                                        <!-- epay_pid -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">商户ID</label>
                                            <input class="form-control maxwidth-edit" id="epay_pid" value="{$settings['epay_pid']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>必填</p>
                                        </div>
                                        <!-- epay_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">商户Key</label>
											<input class="form-control maxwidth-edit" id="epay_key" value="{$settings['epay_key']}">
                                        
											<p class="form-control-guide"><i class="mdi mdi-information"></i>必填</p>
                                        </div>
                                        <!-- epay_alipay -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">支付宝</label>
                                            <select id="epay_alipay" class="form-control maxwidth-edit">
                                                <option value="0">停用</option>
                                                <option value="1" {if $settings['epay_alipay'] == true}selected{/if}>
                                                    启用
                                                </option>
                                            </select>
                                        </div>
                                        <!-- epay_wechat -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">微信支付</label>
                                            <select id="epay_wechat" class="form-control maxwidth-edit">
                                                <option value="0">停用</option>
                                                <option value="1" {if $settings['epay_wechat'] == true}selected{/if}>
                                                    启用
                                                </option>
                                            </select>
                                        </div>
                                        <!-- epay_qq -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">QQ钱包</label>
                                            <select id="epay_qq" class="form-control maxwidth-edit">
                                                <option value="0">停用</option>
                                                <option value="1" {if $settings['epay_qq'] == true}selected{/if}>
                                                    启用
                                                </option>
                                            </select>
                                        </div>
                                        <!-- epay_usdt -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">USDT</label>
                                            <select id="epay_usdt" class="form-control maxwidth-edit">
                                                <option value="0">停用</option>
                                                <option value="1" {if $settings['epay_usdt'] == true}selected{/if}>
                                                    启用
                                                </option>
                                            </select>
                                        </div>
                                        <button id="submit_e_pay" type="submit" class="btn btn-block btn-brand">提交</button>
                                     </div>
                                </div>

                                <div class="tab-pane fade" id="customer_service_system_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#web_customer_service_system">&nbsp;网页客服</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#admin_contact">&nbsp;联系站长</a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <div class="tab-pane fade active in" id="web_customer_service_system">
                                        <!-- live_chat -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">网页客服系统</label>
                                            <select id="live_chat" class="form-control maxwidth-edit">
                                                <option value="none" {if $settings['live_chat'] == "none"}selected{/if}>不启用</option>
                                                <option value="tawk" {if $settings['live_chat'] == "tawk"}selected{/if}>Tawk</option>
                                                <option value="crisp" {if $settings['live_chat'] == "crisp"}selected{/if}>Crisp</option>
                                                <option value="livechat" {if $settings['live_chat'] == "livechat"}selected{/if}>LiveChat</option>
                                                <option value="mylivechat" {if $settings['live_chat'] == "mylivechat"}selected{/if}>MyLiveChat</option>
                                            </select>
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>目前仅 Crisp 与 LiveChat 支持在聊天时传递用户部分账户信息（如账户余额、到期时间、已用流量和剩余流量等）</p>
                                        </div>
                                        <!-- tawk_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Tawk</label>
                                            <input class="form-control maxwidth-edit" id="tawk_id" value="{$settings['tawk_id']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>在 <a href="https://tawk.to" target="view_window">https://tawk.to</a> 申请，这应该是 24 位字符</p>
                                        </div>
                                        <!-- crisp_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Crisp</label>
                                            <input class="form-control maxwidth-edit" id="crisp_id" value="{$settings['crisp_id']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>在 <a href="https://crisp.chat/en" target="view_window">https://crisp.chat/en</a> 申请，这应该是一个 UUID</p>
                                        </div>
                                        <!-- livechat_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">LiveChat</label>
                                            <input class="form-control maxwidth-edit" id="livechat_id" value="{$settings['livechat_id']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>在 <a href="https://www.livechat.com/cn" target="view_window">https://www.livechat.com/cn</a> 申请，这应该是 8 位数字</p>
                                        </div>
                                        <!-- mylivechat_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">MyLiveChat</label>
                                            <input class="form-control maxwidth-edit" id="mylivechat_id" value="{$settings['mylivechat_id']}">
                                            <p class="form-control-guide"><i class="mdi mdi-information"></i>在 <a href="https://www.mylivechat.com" target="view_window">https://www.mylivechat.com</a> 申请，这个我不知道</p>
                                        </div>

                                        <button id="submit_web_customer_service_system" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="admin_contact">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>注意：留空的联系方式将不显示</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>支持使用 HTML 标签。你可以通过配置 a 标签，达到点击即可唤起对应app会话窗口的效果</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>若开启此功能，此页面展示的联系方式将显示在：</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>1. 注册或重置密码页面点击【无法收到验证码】按钮</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>2. 用户账户被停用的告知页面</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>3. 充值页面提示充值未到账的用户</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>4. 用户中心首页公告栏下方</p>
                                        <!-- enable_admin_contact -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">是否显示站长联系方式</label>
                                            <select id="enable_admin_contact" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['enable_admin_contact'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['enable_admin_contact'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- admin_contact1 -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">站长联系方式一</label>
                                            <input class="form-control maxwidth-edit" id="admin_contact1" value="{htmlspecialchars($settings['admin_contact1'])}">
                                        </div>
                                        <!-- admin_contact2 -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">站长联系方式二</label>
                                            <input class="form-control maxwidth-edit" id="admin_contact2" value="{htmlspecialchars($settings['admin_contact2'])}">
                                        </div>
                                        <!-- admin_contact3 -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">站长联系方式三</label>
                                            <input class="form-control maxwidth-edit" id="admin_contact3" value="{htmlspecialchars($settings['admin_contact3'])}">
                                        </div>

                                        <button id="submit_admin_contact" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="verification_code_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#verification_code_public_settings">&nbsp;设置</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#turnstile">&nbsp;Turnstile</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#geetest">&nbsp;Geetest</a>
                                            </li>
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="verification_code_public_settings">
                                        <!-- captcha_provider -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">验证码提供商</label>
                                            <select id="captcha_provider" class="form-control maxwidth-edit">
                                                <option value="turnstile" {if $settings['captcha_provider'] == "turnstile"}selected{/if}>Turnstile</option>
                                                <option value="geetest" {if $settings['captcha_provider'] == "geetest"}selected{/if}>Geetest</option>
                                            </select>
                                        </div>
                                        <!-- enable_reg_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册验证码</label>
                                            <select id="enable_reg_captcha" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['enable_reg_captcha'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['enable_reg_captcha'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- enable_login_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">登录验证码</label>
                                            <select id="enable_login_captcha" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['enable_login_captcha'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['enable_login_captcha'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- enable_checkin_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">签到验证码</label>
                                            <select id="enable_checkin_captcha" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['enable_checkin_captcha'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['enable_checkin_captcha'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- enable_reset_password_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">重置密码验证码</label>
                                            <select id="enable_reset_password_captcha" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['enable_reset_password_captcha'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['enable_reset_password_captcha'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>

                                        <button id="submit_verify_code" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="turnstile">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>在 <a href="https://developers.cloudflare.com/turnstile/get-started/#sitekey-and-secret-key" target="view_window">https://developers.cloudflare.com/turnstile/get-started/#sitekey-and-secret-key</a> 申请</p>
                                        <!-- turnstile_sitekey -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Turnstile Site Key</label>
                                            <input class="form-control maxwidth-edit" id="turnstile_sitekey" value="{$settings['turnstile_sitekey']}">
                                        </div>
                                        <!-- turnstile_secret -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Turnstile Secret</label>
                                            <input class="form-control maxwidth-edit" id="turnstile_secret" value="{$settings['turnstile_secret']}">
                                        </div>

                                        <button id="submit_turnstile" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="geetest">
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>在 <a href="https://gtaccount.geetest.com/sensebot/overview" target="view_window">https://gtaccount.geetest.com/sensebot/overview</a> 申请</p>
                                        <!-- geetest_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Geetest ID</label>
                                            <input class="form-control maxwidth-edit" id="geetest_id" value="{$settings['geetest_id']}">
                                        </div>
                                        <!-- geetest_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Geetest Key</label>
                                            <input class="form-control maxwidth-edit" id="geetest_key" value="{$settings['geetest_key']}">
                                        </div>

                                        <button id="submit_geetest" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="registration_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#reg_mode_and_verify">&nbsp;注册模式与验证</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#register_default_value">&nbsp;默认值</a>
                                            </li>
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="reg_mode_and_verify">
                                        <!-- reg_mode -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册模式设置</label>
                                            <select id="reg_mode" class="form-control maxwidth-edit">
                                                <option value="close" {if $settings['reg_mode'] == 'close'}selected{/if}>关闭公共注册</option>
                                                <option value="open" {if $settings['reg_mode'] == 'open'}selected{/if}>开启公共注册</option>
                                                <option value="invite" {if $settings['reg_mode'] == 'invite'}selected{/if}>仅限用户邀请注册</option>
                                            </select>
                                        </div>
                                        <!-- reg_email_verify -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册邮箱验证码验证</label>
                                            <select id="reg_email_verify" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['reg_email_verify'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['reg_email_verify'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- email_verify_ttl -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册邮箱验证码有效期（单位：秒）</label>
                                            <input class="form-control maxwidth-edit" id="email_verify_ttl" value="{$settings['email_verify_ttl']}">
                                        </div>
                                        <!-- email_verify_ip_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">验证码有效期内单个ip可请求的发件次数</label>
                                            <input class="form-control maxwidth-edit" id="email_verify_ip_limit" value="{$settings['email_verify_ip_limit']}">
                                        </div>

                                        <button type="submit" class="btn btn-block btn-brand submit_register_settings">提交</button>
                                    </div>

                                    <div class="tab-pane fade" id="register_default_value">
                                        <h5>注册默认</h5>
                                        <!-- random_group -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时随机分配到的分组，多个分组请用英文半角逗号分隔</label>
                                            <input class="form-control maxwidth-edit" id="random_group" value="{$settings['random_group']}">
                                        </div>
                                        <!-- min_port -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">用户端口池最小值，0为用户在注册的时候不会被分配多用户端口</label>
                                            <input class="form-control maxwidth-edit" id="min_port" value="{$settings['min_port']}">
                                        </div>
                                        <!-- max_port -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">用户端口池最大值，0为用户在注册的时候不会被分配多用户端口</label>
                                            <input class="form-control maxwidth-edit" id="max_port" value="{$settings['max_port']}">
                                        </div>
                                        <!-- sign_up_for_free_traffic -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时赠送的流量（单位：GB）</label>
                                            <input class="form-control maxwidth-edit" id="sign_up_for_free_traffic" value="{$settings['sign_up_for_free_traffic']}">
                                        </div>
                                        <!-- free_user_reset_day -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">免费用戶的流量重置日，0为不重置</label>
                                            <input class="form-control maxwidth-edit" id="free_user_reset_day" value="{$settings['free_user_reset_day']}">
                                        </div>
                                        <!-- free_user_reset_bandwidth -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">需要重置的免费流量，0为不重置</label>
                                            <input class="form-control maxwidth-edit" id="free_user_reset_bandwidth" value="{$settings['free_user_reset_bandwidth']}">
                                        </div>
                                        <!-- sign_up_for_free_time -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时赠送的时长（单位：天）</label>
                                            <input class="form-control maxwidth-edit" id="sign_up_for_free_time" value="{$settings['sign_up_for_free_time']}">
                                        </div>
                                        <!-- sign_up_for_class -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时设定的等级</label>
                                            <input class="form-control maxwidth-edit" id="sign_up_for_class" value="{$settings['sign_up_for_class']}">
                                        </div>
                                        <!-- sign_up_for_class_time -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时设定的等级过期时间（单位：天）</label>
                                            <input class="form-control maxwidth-edit" id="sign_up_for_class_time" value="{$settings['sign_up_for_class_time']}">
                                        </div>
                                        <h5>注册限制</h5>
                                        <!-- sign_up_for_invitation_codes -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">初始邀请注册链接使用次数限制</label>
                                            <input class="form-control maxwidth-edit" id="sign_up_for_invitation_codes" value="{$settings['sign_up_for_invitation_codes']}">
                                        </div>
                                        <!-- connection_device_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">连接设备限制</label>
                                            <input class="form-control maxwidth-edit" id="connection_device_limit" value="{$settings['connection_device_limit']}">
                                        </div>
                                        <!-- connection_rate_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">使用速率限制</label>
                                            <input class="form-control maxwidth-edit" id="connection_rate_limit" value="{$settings['connection_rate_limit']}">
                                        </div>
                                        <h5>Shadowsocks 设置</h5>
                                        <!-- sign_up_for_method -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">默认加密</label>
                                            <input class="form-control maxwidth-edit" id="sign_up_for_method" value="{$settings['sign_up_for_method']}">
                                        </div>
                                        <h5>其他</h5>
                                        <!-- reg_forbidden_ip -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时默认禁止访问IP列表</label>
                                            <input class="form-control maxwidth-edit" id="reg_forbidden_ip" value="{$settings['reg_forbidden_ip']}">
                                        </div>
                                        <!-- reg_forbidden_port -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时默认禁止访问端口列表</label>
                                            <input class="form-control maxwidth-edit" id="reg_forbidden_port" value="{$settings['reg_forbidden_port']}">
                                        </div>
                                        <!-- sign_up_for_daily_report -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册后是否默认接收每日用量邮件推送</label>
                                            <select id="sign_up_for_daily_report" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['sign_up_for_daily_report'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['sign_up_for_daily_report'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- enable_reg_im -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册时是否要求用户输入IM联系方式</label>
                                            <select id="enable_reg_im" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['enable_reg_im'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['enable_reg_im'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-block btn-brand submit_register_settings">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="invitation_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#invite_gernal_settings">&nbsp;设置</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#rebate_mode">&nbsp;模式</a>
                                            </li>
                                        </ul>
                                    </nav>

                                    <div class="tab-pane fade active in" id="invite_gernal_settings">
                                        <!-- invitation_to_register_balance_reward -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">若有人使用现存用户的邀请链接注册，被邀请人所能获得的余额奖励（单位：元）</label>
                                            <input class="form-control maxwidth-edit" id="invitation_to_register_balance_reward" value="{$settings['invitation_to_register_balance_reward']}">
                                        </div>
                                        <!-- invitation_to_register_traffic_reward -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">若有人使用现存用户的邀请链接注册，邀请人所能获得的流量奖励（单位：GB）</label>
                                            <input class="form-control maxwidth-edit" id="invitation_to_register_traffic_reward" value="{$settings['invitation_to_register_traffic_reward']}">
                                        </div>
                                        <!-- invite_price -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">用户购买邀请码所需要的价格，价格小于0时视为不开放购买</label>
                                            <input class="form-control maxwidth-edit" id="invite_price" value="{$settings['invite_price']}">
                                        </div>
                                        <!-- custom_invite_price -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">用户定制邀请码所需要的价格，价格小于0时视为不开放购买</label>
                                            <input class="form-control maxwidth-edit" id="custom_invite_price" value="{$settings['custom_invite_price']}">
                                        </div>

                                        <br/>
                                        
                                        <button type="submit" class="btn btn-block btn-brand submit_invite_settings">提交</button>
                                    </div>
                                            
                                    <div class="tab-pane fade" id="rebate_mode">
                                        <!-- invitation_mode -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">邀请模式</label>
                                            <select id="invitation_mode" class="form-control maxwidth-edit">
                                                <option value="registration_only" {if $settings['invitation_mode'] == 'registration_only'}selected{/if}>
                                                仅使用邀请注册功能，不返利</option>
                                                <option value="after_recharge" {if $settings['invitation_mode'] == 'after_recharge'}selected{/if}>
                                                使用邀请注册功能，并在被邀请用户充值时返利</option>
                                                <option value="after_purchase" {if $settings['invitation_mode'] == 'after_purchase'}selected{/if}>
                                                使用邀请注册功能，并在被邀请用户购买时返利</option>
                                            </select>
                                        </div>
                                        <!-- invite_rebate_mode -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">返利模式</label>
                                            <select id="invite_rebate_mode" class="form-control maxwidth-edit">
                                                <option value="continued" {if $settings['invite_rebate_mode'] == 'continued'}selected{/if}>
                                                持续返利</option>
                                                <option value="limit_frequency" {if $settings['invite_rebate_mode'] == 'limit_frequency'}selected{/if}>
                                                限制邀请人能从被邀请人身上获得的总返利次数</option>
                                                <option value="limit_amount" {if $settings['invite_rebate_mode'] == 'limit_amount'}selected{/if}>
                                                限制邀请人能从被邀请人身上获得的总返利金额</option>
                                                <option value="limit_time_range" {if $settings['invite_rebate_mode'] == 'limit_time_range'}selected{/if}>
                                                限制邀请人能从被邀请人身上获得返利的时间范围</option>
                                            </select>
                                        </div>
                                        <!-- rebate_ratio -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">返利比例。10 元商品反 2 元就填 0.2</label>
                                            <input class="form-control maxwidth-edit" id="rebate_ratio" value="{$settings['rebate_ratio']}">
                                        </div>
                                        <h5>返利限制模式</h5>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>以下设置项仅在选择对应返利限制模式时生效</p>
                                        <!-- rebate_time_range_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">返利时间范围限制（单位：天）</label>
                                            <input class="form-control maxwidth-edit" id="rebate_time_range_limit" value="{$settings['rebate_time_range_limit']}">
                                        </div>
                                        <!-- rebate_frequency_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">返利总次数限制</label>
                                            <input class="form-control maxwidth-edit" id="rebate_frequency_limit" value="{$settings['rebate_frequency_limit']}">
                                        </div>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>例如：设置为 3 时，一个被邀请用户先后购买了售价为 10，20，50，100 的商品，则只对前三笔订单返利（假设设置为在购买时返利）</p>
                                        <!-- rebate_amount_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">返利总金额限制</label>
                                            <input class="form-control maxwidth-edit" id="rebate_amount_limit" value="{$settings['rebate_amount_limit']}">
                                        </div>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>例如：设置为 10 时，一个被邀请用户先后购买了售价为 10，20，50，100 的商品，若返点设置为 20% ，则第一次购买返利为 2；第二次为 4；第三次为 4；第四次及之后的购买，邀请人所能获得的返利均为 0（假设设置为在购买时返利）</p>
                                        <p class="form-control-guide"><i class="mdi mdi-information"></i>在进行第三次返利计算时，按设置应返利订单金额的 20% ，即 10 元。但因已获得历史返利 6 元，则只能获得返利总金额限制与历史返利的差值</p>
                                        
                                        <br/>
                                        
                                        <button type="submit" class="btn btn-block btn-brand submit_invite_settings">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="telegram_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#telegram_gernal_settings">&nbsp;设置</a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <div class="tab-pane fade active in" id="telegram_gernal_settings">
                                        <!-- telegram_add_node -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">添加节点通知</label>
                                            <select id="telegram_add_node" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_add_node'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_add_node'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_add_node_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">添加节点通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_add_node_text" value="{$settings['telegram_add_node_text']}">
                                        </div>
                                        <!-- telegram_update_node -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">修改节点通知</label>
                                            <select id="telegram_update_node" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_update_node'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_update_node'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_update_node_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">修改节点通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_update_node_text" value="{$settings['telegram_update_node_text']}">
                                        </div>
                                        <!-- telegram_delete_node -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">删除节点通知</label>
                                            <select id="telegram_delete_node" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_delete_node'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_delete_node'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_delete_node_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">删除节点通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_delete_node_text" value="{$settings['telegram_delete_node_text']}">
                                        </div>
                                        <!-- telegram_node_gfwed -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点被墙通知</label>
                                            <select id="telegram_node_gfwed" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_node_gfwed'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_node_gfwed'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_node_gfwed_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点被墙通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_node_gfwed_text" value="{$settings['telegram_node_gfwed_text']}">
                                        </div>
                                        <!-- telegram_node_ungfwed -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点被墙恢复通知</label>
                                            <select id="telegram_node_ungfwed" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_node_ungfwed'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_node_ungfwed'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_node_ungfwed_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点被墙恢复通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_node_ungfwed_text" value="{$settings['telegram_node_ungfwed_text']}">
                                        </div>
                                        <!-- telegram_node_online -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点恢复上线通知</label>
                                            <select id="telegram_node_online" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_node_online'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_node_online'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_node_online_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点恢复上线通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_node_online_text" value="{$settings['telegram_node_online_text']}">
                                        </div>
                                        <!-- telegram_node_offline -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点离线通知</label>
                                            <select id="telegram_node_offline" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_node_offline'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_node_offline'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_node_offline_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">节点离线通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_node_offline_text" value="{$settings['telegram_node_offline_text']}">
                                        </div>
                                        <!-- telegram_daily_job -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">每日任务通知</label>
                                            <select id="telegram_daily_job" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_daily_job'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_daily_job'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_daily_job_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">每日任务通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_daily_job_text" value="{$settings['telegram_daily_job_text']}">
                                        </div>
                                        <!-- telegram_diary -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">系统运行状况通知</label>
                                            <select id="telegram_diary" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_diary'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_diary'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_diary_text -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">系统运行状况通知文本</label>
                                            <input class="form-control maxwidth-edit" id="telegram_diary_text" value="{$settings['telegram_diary_text']}">
                                        </div>
                                        <!-- telegram_unbind_kick_member -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">解绑Telegram账户后自动踢出群组</label>
                                            <select id="telegram_unbind_kick_member" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_unbind_kick_member'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_unbind_kick_member'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_group_bound_user -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">仅允许已绑定Telegram账户的用户加入群组</label>
                                            <select id="telegram_group_bound_user" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_group_bound_user'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_group_bound_user'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_show_group_link -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">启用Telegram机器人显示用户群组链接</label>
                                            <select id="telegram_show_group_link" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['telegram_show_group_link'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['telegram_show_group_link'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- telegram_group_link -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">用户群组链接</label>
                                            <input class="form-control maxwidth-edit" id="telegram_group_link" value="{$settings['telegram_group_link']}">
                                        </div>

                                        <button id="submit_telegram_gernal_settings" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {include file='dialog.tpl'}
    </div>
</main>

{include file='admin/footer.tpl'}

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_f2f_pay').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'f2f_pay',
                    f2f_pay_app_id: $$getValue('f2f_pay_app_id'),
                    f2f_pay_pid: $$getValue('f2f_pay_pid'),
                    f2f_pay_public_key: $$getValue('f2f_pay_public_key'),
                    f2f_pay_private_key: $$getValue('f2f_pay_private_key'),
                    f2f_pay_notify_url: $$getValue('f2f_pay_notify_url')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>
<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_e_pay').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'e_pay',
                    epay_url: $$getValue('epay_url'),
                    epay_pid: $$getValue('epay_pid'),
                    epay_key: $$getValue('epay_key'),
                    epay_alipay: $$getValue('epay_alipay'),
                    epay_wechat: $$getValue('epay_wechat'),
                    epay_qq: $$getValue('epay_qq'),
                    epay_usdt: $$getValue('epay_usdt')
                    
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>
<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_payment').addEventListener('click', () => {
            {foreach $payment_gateways as $key => $value}
            if ($$.getElementById("{$value}_switch").checked) {
                var {$value} = 1;
            } else {
                var {$value} = 0;
            }
            {/foreach}
            
            $.ajax({
                type: "POST",
                url: "/admin/setting/payment",
                dataType: "json",
                data: {
                    {foreach $payment_gateways as $key => $value}
                    {$value},
                    {/foreach}
                    class: 'payment'
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_vmq_pay').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'vmq_pay',
                    vmq_gateway: $$getValue('vmq_gateway'),
                    vmq_key: $$getValue('vmq_key')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_mail').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'mail',
                    mail_driver: $$getValue('mail_driver')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_smtp').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'smtp',
                    smtp_host: $$getValue('smtp_host'),
                    smtp_username: $$getValue('smtp_username'),
                    smtp_password: $$getValue('smtp_password'),
                    smtp_port: $$getValue('smtp_port'),
                    smtp_name: $$getValue('smtp_name'),
                    smtp_sender: $$getValue('smtp_sender'),
                    smtp_ssl: $$getValue('smtp_ssl'),
                    smtp_bbc: $$getValue('smtp_bbc')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_email_test').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting/email",
                dataType: "json",
                data: {
                    recipient: $$getValue('testing_email_recipients')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_verify_code').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'verify_code',
                    captcha_provider: $$getValue('captcha_provider'),
                    enable_reg_captcha: $$getValue('enable_reg_captcha'),
                    enable_login_captcha: $$getValue('enable_login_captcha'),
                    enable_checkin_captcha: $$getValue('enable_checkin_captcha'),
                    enable_reset_password_captcha: $$getValue('enable_reset_password_captcha')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_geetest').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'verify_code_geetest',
                    geetest_id: $$getValue('geetest_id'),
                    geetest_key: $$getValue('geetest_key')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_turnstile').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'verify_code_turnstile',
                    turnstile_sitekey: $$getValue('turnstile_sitekey'),
                    turnstile_secret: $$getValue('turnstile_secret')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_mailgun').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'mailgun',
                    mailgun_key: $$getValue('mailgun_key'),
                    mailgun_domain: $$getValue('mailgun_domain'),
                    mailgun_sender: $$getValue('mailgun_sender')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_sendgrid').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'sendgrid',
                    sendgrid_key: $$getValue('sendgrid_key'),
                    sendgrid_sender: $$getValue('sendgrid_sender'),
                    sendgrid_name: $$getValue('sendgrid_name')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_ses').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'ses',
                    aws_access_key_id: $$getValue('aws_access_key_id'),
                    aws_secret_access_key: $$getValue('aws_secret_access_key'),
                    aws_region: $$getValue('aws_region'),
                    aws_ses_sender: $$getValue('aws_ses_sender')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_payjs_pay').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'payjs_pay',
                    payjs_url: $$getValue('payjs_url'),
                    payjs_mchid: $$getValue('payjs_mchid'),
                    payjs_key: $$getValue('payjs_key')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_paymentwall').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'paymentwall',
                    pmw_publickey: $$getValue('pmw_publickey'),
                    pmw_privatekey: $$getValue('pmw_privatekey'),
                    pmw_widget: $$getValue('pmw_widget'),
                    pmw_height: $$getValue('pmw_height')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_admin_contact').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'admin_contact',
                    enable_admin_contact: $$getValue('enable_admin_contact'),
                    admin_contact1: $$getValue('admin_contact1'),
                    admin_contact2: $$getValue('admin_contact2'),
                    admin_contact3: $$getValue('admin_contact3')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_web_customer_service_system').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'web_customer_service_system',
                    live_chat: $$getValue('live_chat'),
                    tawk_id: $$getValue('tawk_id'),
                    crisp_id: $$getValue('crisp_id'),
                    livechat_id: $$getValue('livechat_id'),
                    mylivechat_id: $$getValue('mylivechat_id')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_theadpay').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'theadpay',
                    theadpay_url: $$getValue('theadpay_url'),
                    theadpay_mchid: $$getValue('theadpay_mchid'),
                    theadpay_key: $$getValue('theadpay_key')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_stripe').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'stripe',
                    stripe_card: $$getValue('stripe_card_select'),
                    stripe_currency: $$getValue('stripe_currency'),
                    stripe_min_recharge: $$getValue('stripe_min_recharge'),
                    stripe_max_recharge: $$getValue('stripe_max_recharge'),
                    stripe_pk: $$getValue('stripe_pk'),
                    stripe_sk: $$getValue('stripe_sk'),
                    stripe_webhook_key: $$getValue('stripe_webhook_key')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $('.submit_register_settings').click( () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'register',
                    reg_mode: $$getValue('reg_mode'),
                    reg_email_verify: $$getValue('reg_email_verify'),
                    email_verify_ttl: $$getValue('email_verify_ttl'),
                    email_verify_ip_limit: $$getValue('email_verify_ip_limit'),
                    random_group: $$getValue('random_group'),
                    min_port: $$getValue('min_port'),
                    max_port: $$getValue('max_port'),
                    sign_up_for_free_traffic: $$getValue('sign_up_for_free_traffic'),
                    free_user_reset_day: $$getValue('free_user_reset_day'),
                    free_user_reset_bandwidth: $$getValue('free_user_reset_bandwidth'),
                    sign_up_for_free_time: $$getValue('sign_up_for_free_time'),
                    sign_up_for_class: $$getValue('sign_up_for_class'),
                    sign_up_for_class_time: $$getValue('sign_up_for_class_time'),
                    sign_up_for_invitation_codes: $$getValue('sign_up_for_invitation_codes'),
                    connection_device_limit: $$getValue('connection_device_limit'),
                    connection_rate_limit: $$getValue('connection_rate_limit'),
                    sign_up_for_method: $$getValue('sign_up_for_method'),
                    reg_forbidden_ip: $$getValue('reg_forbidden_ip'),
                    reg_forbidden_port: $$getValue('reg_forbidden_port'),
                    sign_up_for_daily_report: $$getValue('sign_up_for_daily_report'),
                    enable_reg_im: $$getValue('enable_reg_im')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>

<script>
    window.addEventListener('load', () => {
        $('.submit_invite_settings').click( () => {
            $.ajax( {
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'invite',
                    invitation_mode: $$getValue('invitation_mode'),
                    invite_rebate_mode: $$getValue('invite_rebate_mode'),
                    rebate_ratio: $$getValue('rebate_ratio'),
                    rebate_frequency_limit: $$getValue('rebate_frequency_limit'),
                    rebate_amount_limit: $$getValue('rebate_amount_limit'),
                    rebate_time_range_limit: $$getValue('rebate_time_range_limit'),
                    invitation_to_register_balance_reward: $$getValue('invitation_to_register_balance_reward'),
                    invitation_to_register_traffic_reward: $$getValue('invitation_to_register_traffic_reward'),
                    invite_price: $$getValue('invite_price'),
                    custom_invite_price: $$getValue('custom_invite_price')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            } )
        } )
    } )
</script>

<script>
    window.addEventListener('load', () => {
        $$.getElementById('submit_telegram_gernal_settings').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'telegram',
                    telegram_add_node: $$getValue('telegram_add_node'),
                    telegram_add_node_text: $$getValue('telegram_add_node_text'),
                    telegram_update_node: $$getValue('telegram_update_node'),
                    telegram_update_node_text: $$getValue('telegram_update_node_text'),
                    telegram_delete_node: $$getValue('telegram_delete_node'),
                    telegram_delete_node_text: $$getValue('telegram_delete_node_text'),
                    telegram_node_gfwed: $$getValue('telegram_node_gfwed'),
                    telegram_node_gfwed_text: $$getValue('telegram_node_gfwed_text'),
                    telegram_node_ungfwed: $$getValue('telegram_node_ungfwed'),
                    telegram_node_ungfwed_text: $$getValue('telegram_node_ungfwed_text'),
                    telegram_node_online: $$getValue('telegram_node_online'),
                    telegram_node_online_text: $$getValue('telegram_node_online_text'),
                    telegram_node_offline: $$getValue('telegram_node_offline'),
                    telegram_node_offline_text: $$getValue('telegram_node_offline_text'),
                    telegram_daily_job: $$getValue('telegram_daily_job'),
                    telegram_daily_job_text: $$getValue('telegram_daily_job_text'),
                    telegram_diary: $$getValue('telegram_diary'),
                    telegram_diary_text: $$getValue('telegram_diary_text'),
                    telegram_unbind_kick_member: $$getValue('telegram_unbind_kick_member'),
                    telegram_group_bound_user: $$getValue('telegram_group_bound_user'),
                    telegram_show_group_link: $$getValue('telegram_show_group_link'),
                    telegram_group_link: $$getValue('telegram_group_link')
                },
                success: data => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    if (data.ret) {
                        window.setTimeout("location.href='/admin/setting'", {$config['jump_delay']});
                    }
                },
                error: jqXHR => {
                    alert(`发生错误：${
                            jqXHR.status
                            }`);
                }
            })
        })
    })
</script>