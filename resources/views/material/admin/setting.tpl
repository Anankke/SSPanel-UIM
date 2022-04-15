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
                                    <a data-toggle="tab" href="#mail_settings"><i class="icon icon-lg">email</i>&nbsp;邮件</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#customer_service_system_settings"><i class="icon icon-lg">message</i>&nbsp;客服</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#verification_code_settings"><i class="icon icon-lg">verified_user</i>&nbsp;验证</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#personalise_settings"><i class="icon icon-lg">color_lens</i>&nbsp;个性化</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#registration_settings"><i class="icon icon-lg">group_add</i>&nbsp;注册</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#invitation_settings"><i class="icon icon-lg">loyalty</i>&nbsp;邀请</a>
                                </li>
                            </ul>
                        </nav>
                                
                        <div class="card-inner">
                           <div class="tab-content">
                                <div class="tab-pane fade active in" id="mail_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#email_auth_settings"><i class="icon icon-lg">settings</i>&nbsp;设置</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#smtp"><i class="icon icon-lg">contact_mail</i>&nbsp;smtp</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#sendgrid"><i class="icon icon-lg">markunread_mailbox</i>&nbsp;sendgrid</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#mailgun"><i class="icon icon-lg">send</i>&nbsp;mailgun</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#ses"><i class="icon icon-lg">arrow_forward</i>&nbsp;ses</a>
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
                                            <p class="form-control-guide"><i class="material-icons">info</i>邮件配置保存完成后，如需验证是否可用，可在上方填写一个有效邮箱，系统将发送一封测试邮件到该邮箱。如果能够正常接收，则说明配置可用</p>
                                            {if $settings['mail_driver'] == "none"}
                                            <p class="form-control-guide"><i class="material-icons">info</i>如需使用发信测试功能，请先在上方选择一个发信方式，并配置有效的相关参数</p>
                                            {/if}
                                        </div>
                                        
                                        <button id="submit_email_test" type="submit" class="btn btn-brand btn-dense" {if $settings['mail_driver'] == "none"}disabled{/if}>测试</button>
                                    </div>
                                    <div class="tab-pane fade" id="smtp">
                                        <!-- smtp_host -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP主机地址</label>
                                            <input class="form-control maxwidth-edit" id="smtp_host" value="{$settings['smtp_host']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>例如：smtpdm-ap-southeast-1.aliyun.com</p>
                                        </div>
                                        <!-- smtp_username -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP账户名</label>
                                            <input class="form-control maxwidth-edit" id="smtp_username" value="{$settings['smtp_username']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>例如：no-reply@airport.com</p>
                                        </div>
                                        <!-- smtp_password -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP账户密码</label>
                                            <input class="form-control maxwidth-edit" id="smtp_password" value="{$settings['smtp_password']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>如果你使用 QQ 邮箱或 163 邮箱，此处应当填写单独的授权码</p>
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
                                            <p class="form-control-guide"><i class="material-icons">info</i>常见端口一般就这些</p>
                                        </div>
                                        <!-- smtp_name -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP发信名称</label>
                                            <input class="form-control maxwidth-edit" id="smtp_name" value="{$settings['smtp_name']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>这里的设置在邮箱的邮件列表中可见。你可以设置为网站名称</p>
                                        </div>
                                        <!-- smtp_sender -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">SMTP账户发信地址</label>
                                            <input class="form-control maxwidth-edit" id="smtp_sender" value="{$settings['smtp_sender']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>如不知道填什么，请与此项保持一致：SMTP账户名</p>
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
                                            <p class="form-control-guide"><i class="material-icons">info</i>如无需使用此功能，请留空</p>
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
                                            <label class="floating-label">密钥 ID</label>
                                            <input class="form-control maxwidth-edit" id="aws_access_key_id" value="{$settings['aws_access_key_id']}">
                                        </div>
                                        <!-- aws_secret_access_key -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">密钥 KEY</label>
                                            <input class="form-control maxwidth-edit" id="aws_secret_access_key" value="{$settings['aws_secret_access_key']}">
                                        </div>

                                        <button id="submit_ses" type="submit" class="btn btn-brand btn-dense">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="customer_service_system_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#web_customer_service_system"><i class="icon icon-lg">settings</i>&nbsp;网页客服</a>
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
                                            <p class="form-control-guide"><i class="material-icons">info</i>目前仅 Crisp 与 LiveChat 支持在聊天时传递用户部分账户信息（如账户余额、到期时间、已用流量和剩余流量等）</p>
                                        </div>
                                        <!-- tawk_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Tawk</label>
                                            <input class="form-control maxwidth-edit" id="tawk_id" value="{$settings['tawk_id']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>在 <a href="https://tawk.to" target="view_window">https://tawk.to</a> 申请，这应该是 24 位字符</p>
                                        </div>
                                        <!-- crisp_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">Crisp</label>
                                            <input class="form-control maxwidth-edit" id="crisp_id" value="{$settings['crisp_id']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>在 <a href="https://crisp.chat/en" target="view_window">https://crisp.chat/en</a> 申请，这应该是一个 UUID</p>
                                        </div>
                                        <!-- livechat_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">LiveChat</label>
                                            <input class="form-control maxwidth-edit" id="livechat_id" value="{$settings['livechat_id']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>在 <a href="https://www.livechat.com/cn" target="view_window">https://www.livechat.com/cn</a> 申请，这应该是 8 位数字</p>
                                        </div>
                                        <!-- mylivechat_id -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">MyLiveChat</label>
                                            <input class="form-control maxwidth-edit" id="mylivechat_id" value="{$settings['mylivechat_id']}">
                                            <p class="form-control-guide"><i class="material-icons">info</i>在 <a href="https://www.mylivechat.com" target="view_window">https://www.mylivechat.com</a> 申请，这个我不知道</p>
                                        </div>

                                        <button id="submit_web_customer_service_system" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="verification_code_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#verification_code_public_settings"><i class="icon icon-lg">settings</i>&nbsp;设置</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#recaptcha"><i class="icon icon-lg">face</i>&nbsp;reCAPTCHA</a>
                                            </li>
                                            <li>
                                                <a data-toggle="tab" href="#geetest"><i class="icon icon-lg">extension</i>&nbsp;Geetest</a>
                                            </li>
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="verification_code_public_settings">
                                        <!-- captcha_provider -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">验证码提供商</label>
                                            <select id="captcha_provider" class="form-control maxwidth-edit">
                                                <option value="recaptcha" {if $settings['captcha_provider'] == "recaptcha"}selected{/if}>reCaptcha</option>
                                                <option value="geetest" {if $settings['captcha_provider'] == "geetest"}selected{/if}>Geetest</option>
                                            </select>
                                        </div>
                                        <!-- enable_reg_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">注册验证码</label>
                                            <select id="enable_reg_captcha" class="form-control maxwidth-edit">
                                                <option value="0">关闭</option>
                                                <option value="1" disabled>开启（暂不可用）</option>
                                            </select>
                                        </div>
                                        <!-- enable_login_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">登录验证码</label>
                                            <select id="enable_login_captcha" class="form-control maxwidth-edit">
                                                <option value="0">关闭</option>
                                                <option value="1" disabled>开启（暂不可用）</option>
                                            </select>
                                        </div>
                                        <!-- enable_checkin_captcha -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">签到验证码</label>
                                            <select id="enable_checkin_captcha" class="form-control maxwidth-edit">
                                                <option value="0">关闭</option>
                                                <option value="1" disabled>开启（暂不可用）</option>
                                            </select>
                                        </div>

                                        <button id="submit_verify_code" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="recaptcha">
                                        <p class="form-control-guide"><i class="material-icons">info</i>在 <a href="https://www.google.com/recaptcha/admin/create" target="view_window">https://www.google.com/recaptcha/admin/create</a> 申请，选择【reCAPTCHA 第 2 版】的子选项【进行人机身份验证复选框】</p>
                                        <!-- recaptcha_sitekey -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">reCaptcha Site Key</label>
                                            <input class="form-control maxwidth-edit" id="recaptcha_sitekey" value="{$settings['recaptcha_sitekey']}">
                                        </div>
                                        <!-- recaptcha_secret -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">reCaptcha Secret</label>
                                            <input class="form-control maxwidth-edit" id="recaptcha_secret" value="{$settings['recaptcha_secret']}">
                                        </div>

                                        <button id="submit_recaptcha" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                    <div class="tab-pane fade" id="geetest">
                                        <p class="form-control-guide"><i class="material-icons">info</i>在 <a href="https://gtaccount.geetest.com/sensebot/overview" target="view_window">https://gtaccount.geetest.com/sensebot/overview</a> 申请</p>
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

                                <div class="tab-pane fade" id="personalise_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#custom_background_image"><i class="icon icon-lg">image</i>&nbsp;背景图像</a>
                                            </li>
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="custom_background_image">
                                        <p class="form-control-guide"><i class="material-icons">info</i>默认背景图片地址：/theme/material/css/images/bg/amber.jpg <a href="/theme/material/css/images/bg/amber.jpg">预览</a></p>
                                        <p class="form-control-guide"><i class="material-icons">info</i>自带背景图片一地址：/theme/material/css/images/bg/streak.jpg <a href="/theme/material/css/images/bg/streak.jpg">预览</a></p>
                                        <p class="form-control-guide"><i class="material-icons">info</i>自带背景图片二地址：/theme/material/css/images/bg/geometry.jpg <a href="/theme/material/css/images/bg/geometry.jpg">预览</a></p>
                                        <p class="form-control-guide"><i class="material-icons">info</i>如需自定义，图片地址可以指向 public 目录或图床图片地址</p>
                                        <!-- user_center_bg -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">是否启用自定义用户中心背景图片</label>
                                            <select id="user_center_bg" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['user_center_bg'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['user_center_bg'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- admin_center_bg -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">是否启用自定义管理中心背景图片</label>
                                            <select id="admin_center_bg" class="form-control maxwidth-edit">
                                                <option value="0" {if $settings['admin_center_bg'] == false}selected{/if}>关闭</option>
                                                <option value="1" {if $settings['admin_center_bg'] == true}selected{/if}>开启</option>
                                            </select>
                                        </div>
                                        <!-- user_center_bg_addr -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">用户中心背景图片地址</label>
                                            <input class="form-control maxwidth-edit" id="user_center_bg_addr" value="{$settings['user_center_bg_addr']}">
                                        </div>
                                        <!-- admin_center_bg_addr -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">管理中心背景图片地址</label>
                                            <input class="form-control maxwidth-edit" id="admin_center_bg_addr" value="{$settings['admin_center_bg_addr']}">
                                        </div>

                                        <button id="submit_custom_background_image" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="registration_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#reg_mode_and_verify"><i class="icon icon-lg">vpn_key</i>&nbsp;注册模式与验证</a>
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

                                        <button id="submit_reg_mode_and_verify" type="submit" class="btn btn-block btn-brand">提交</button>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="invitation_settings">
                                    <nav class="tab-nav margin-top-no">
                                        <ul class="nav nav-list">
                                            <li class="active">
                                                <a data-toggle="tab" href="#rebate_mode"><i class="icon icon-lg">developer_mode</i>&nbsp;模式</a>
                                            </li>
                                        </ul>
                                    </nav>
                                            
                                    <div class="tab-pane fade active in" id="rebate_mode">
                                        <!-- invitation_mode -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">邀请模式</label>
                                            <select id="invitation_mode" class="form-control maxwidth-edit">
                                                <option value="registration_only" {if $settings['invitation_mode'] == 'registration_only'}selected{/if}>
                                                仅使用邀请注册功能，不返利</option>
                                                <option value="after_purchase" {if $settings['invitation_mode'] == 'after_purchase'}selected{/if}>
                                                使用邀请注册功能，并在被邀请用户支付账单后返利</option>
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
                                        <p class="form-control-guide"><i class="material-icons">info</i>以下设置项仅在选择对应返利限制模式时生效</p>
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
                                        <p class="form-control-guide"><i class="material-icons">info</i>例如：设置为 3 时，一个被邀请用户先后购买了售价为 10，20，50，100 的商品，则只对前三笔订单返利（假设设置为在购买时返利）</p>
                                        <!-- rebate_amount_limit -->
                                        <div class="form-group form-group-label">
                                            <label class="floating-label">返利总金额限制</label>
                                            <input class="form-control maxwidth-edit" id="rebate_amount_limit" value="{$settings['rebate_amount_limit']}">
                                        </div>
                                        <p class="form-control-guide"><i class="material-icons">info</i>例如：设置为 10 时，一个被邀请用户先后购买了售价为 10，20，50，100 的商品，若返点设置为 20% ，则第一次购买返利为 2；第二次为 4；第三次为 4；第四次及之后的购买，邀请人所能获得的返利均为 0（假设设置为在购买时返利）</p>
                                        <p class="form-control-guide"><i class="material-icons">info</i>在进行第三次返利计算时，按设置应返利订单金额的 20% ，即 10 元。但因已获得历史返利 6 元，则只能获得返利总金额限制与历史返利的差值</p>
                                        
                                        <br/><button id="submit_rebate_mode" type="submit" class="btn btn-block btn-brand">提交</button>
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
                    enable_checkin_captcha: $$getValue('enable_checkin_captcha')
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
        $$.getElementById('submit_recaptcha').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'verify_code_recaptcha',
                    recaptcha_sitekey: $$getValue('recaptcha_sitekey'),
                    recaptcha_secret: $$getValue('recaptcha_secret')
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
                    aws_secret_access_key: $$getValue('aws_secret_access_key')
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
        $$.getElementById('submit_custom_background_image').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'background_image',
                    user_center_bg: $$getValue('user_center_bg'),
                    admin_center_bg: $$getValue('admin_center_bg'),
                    user_center_bg_addr: $$getValue('user_center_bg_addr'),
                    admin_center_bg_addr: $$getValue('admin_center_bg_addr')
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
        $$.getElementById('submit_reg_mode_and_verify').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'register',
                    reg_mode: $$getValue('reg_mode'),
                    reg_email_verify: $$getValue('reg_email_verify'),
                    email_verify_ttl: $$getValue('email_verify_ttl'),
                    email_verify_ip_limit: $$getValue('email_verify_ip_limit')
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
        $$.getElementById('submit_rebate_mode').addEventListener('click', () => {
            $.ajax({
                type: "POST",
                url: "/admin/setting",
                dataType: "json",
                data: {
                    class: 'rebate_mode',
                    invitation_mode: $$getValue('invitation_mode'),
                    invite_rebate_mode: $$getValue('invite_rebate_mode'),
                    rebate_ratio: $$getValue('rebate_ratio'),
                    rebate_frequency_limit: $$getValue('rebate_frequency_limit'),
                    rebate_amount_limit: $$getValue('rebate_amount_limit'),
                    rebate_time_range_limit: $$getValue('rebate_time_range_limit')
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