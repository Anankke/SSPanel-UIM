{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">资料编辑</h1>
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
                                    <div class="card-heading">重置连接密码</div>
                                    <button class="btn btn-flat" id="passwd_reset"><span class="mdi mdi-autorenew"></span>&nbsp;</button>
                                </div>
                                <p>当前连接密码：<code>{$user->passwd}</code>
                                    <button class="kaobei copy-text btn btn-subscription" type="button" data-clipboard-text="{$user->passwd}">
                                        点击拷贝
                                    </button>
                                </p>
                                <p>点击重置按钮将会自动生成由随机字母和数字组成的连接密码。</p>
                                <p>重置连接密码同时也会自动为您重新生成 V2Ray 和 Trojan 节点的 UUID。</p>
                                <p>重置连接密码后，您需要更新订阅或修改客户端配置方可继续使用。</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card margin-bottom-no">
					<div class="card-main">
						<div class="card-inner">
							<div class="card-inner">
								<div class="cardbtn-edit">
									<div class="card-heading">重置订阅链接</div>
                                    <button class="btn btn-flat" id="url_reset"><span class="mdi mdi-autorenew">autorenew</span>&nbsp;</button>
								</div>
                                <p>点击会重置您的订阅链接，您需要更新客户端中所配置的订阅地址方可继续使用。</p>
							</div>
						</div>
					</div>
                </div>
                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="card-heading">选择客户端</div>
                                <p>SS/SSR 支持的加密方式和混淆方式有所不同，请根据实际情况来进行选择</p>
                                <p>在这里选择你需要使用的客户端可以帮助你筛选加密方式和混淆方式</p>
                                <p>auth_chain 系为实验性协议，可能造成不稳定或无法使用</p>
                                <br>
                                <button class="btn btn-subscription" type="button" id="filter-btn-ss">SS</button>
                                <button class="btn btn-subscription" type="button" id="filter-btn-ssr">SSR</button>
                                <button class="btn btn-subscription" type="button" id="filter-btn-universal">通用</button>
                            </div>
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">加密方式修改</div>
                                    <button class="btn btn-flat" id="method-update"><span class="mdi mdi-check"></span>&nbsp</button>
                                </div>
                                <p>
                                    当前加密方式：<code id="ajax-user-method" data-default="method">[{if URL::canMethodConnect($user->method) == 2}SS{else}SS/SSR{/if}可连接] {$user->method}</code>
                                </p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="method">加密方式</label>
                                    <button id="method" class="form-control maxwidth-edit" data-toggle="dropdown"
                                            value="{$user->method}"></button>
                                    <ul class="dropdown-menu" aria-labelledby="method">
                                        {$method_list = Config::getSupportParam('method')}
                                        {foreach $method_list as $method}
                                            <li class="{if URL::canMethodConnect($user->method) == 2}filter-item-ss{else}filter-item-universal{/if}">
                                                <a href="#" class="dropdown-option" onclick="return false;"
                                                   val="{$method}"
                                                   data="method">[{if URL::canMethodConnect($method) == 2}SS{else}SS/SSR{/if}
                                                    可连接] {$method}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">协议&混淆设置</div>
                                    <button class="btn btn-flat" id="ssr-update"><span class="mdi mdi-check"></span>&nbsp;</button>
                                </div>
                                <p>当前协议：<code id="ajax-user-protocol" data-default="protocol">[{if URL::canProtocolConnect($user->protocol) == 3}SS/SSR{else}SSR{/if}可连接] {$user->protocol}</code></p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="protocol">协议</label>
                                    <button id="protocol" class="form-control maxwidth-edit" data-toggle="dropdown"
                                            value="{$user->protocol}"></button>
                                    <ul class="dropdown-menu" aria-labelledby="protocol">
                                        {$protocol_list = Config::getSupportParam('protocol')}
                                        {foreach $protocol_list as $protocol}
                                            <li class="{if URL::canProtocolConnect($protocol) == 3}filter-item-universal{else}filter-item-ssr{/if}">
                                                <a href="#" class="dropdown-option" onclick="return false;" val="{$protocol}" data="protocol">
                                                    [{if URL::canProtocolConnect($protocol) == 3}SS/SSR{else}SSR{/if}
                                                    可连接] {$protocol}
                                                </a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                            <div class="card-inner">
                                <p>当前混淆方式：<code id="ajax-user-obfs" data-default="obfs">[{if URL::canObfsConnect($user->obfs) >= 3}SS/SSR{elseif URL::canObfsConnect($user->obfs) == 1}SSR{else}SS{/if}可连接] {$user->obfs}</code></p>
                                <p>SS 和 SSR 支持的混淆类型有所不同，simple_obfs_* 为 SS 的混淆方式，其他为 SSR 的混淆方式</p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="obfs">混淆方式</label>
                                    <button id="obfs" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->obfs}"></button>
                                    <ul class="dropdown-menu" aria-labelledby="obfs">
                                        {$obfs_list = Config::getSupportParam('obfs')}
                                        {foreach $obfs_list as $obfs}
                                            <li class="{if URL::canObfsConnect($obfs) >= 3}filter-item-universal{else}{if URL::canObfsConnect($obfs) == 1}filter-item-ssr{else}filter-item-ss{/if}{/if}">
                                                <a href="#" class="dropdown-option" onclick="return false;" val="{$obfs}" data="obfs">
                                                    [{if URL::canObfsConnect($obfs) >= 3}SS/SSR{else}{if URL::canObfsConnect($obfs) == 1}SSR{else}SS{/if}{/if}可连接] {$obfs}
                                                </a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                            <div class="card-inner">
                                <p>当前混淆参数：<code id="ajax-user-obfs-param">{$user->obfs_param}</code></p>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="obs-param">在这输入混淆参数</label>
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
                                <p class="card-heading">两步验证</p>
                                <p>请使用 TOTP 两步验证器扫描下面的二维码。</p>
                                <p><i class="mdi mdi-android icon-lg" aria-hidden="true"></i><a
                                            href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2">&nbsp;Android</a>
                                </p>
                                <p><i class="mdi mdi-apple-ios icon-lg" aria-hidden="true"></i><a
                                            href="https://itunes.apple.com/cn/app/google-authenticator/id388497605?mt=8">&nbsp;iOS</a>
                                </p>
                                <p>在没有测试完成绑定成功之前请不要启用。</p>
                                <p>当前设置：<code data-default="ga-enable">{if $user->ga_enable==1} 要求验证 {else} 不要求 {/if}</code>
                                </p>
                                <p>当前服务器时间：{date("Y-m-d H:i:s")}</p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="ga-enable">验证设置</label>
                                    <button type="button" id="ga-enable" class="form-control maxwidth-edit"
                                            data-toggle="dropdown" value="{$user->ga_enable}"></button>
                                    <ul class="dropdown-menu" aria-labelledby="ga-enable">
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="0"
                                               data="ga-enable">不要求</a></li>
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="1"
                                               data="ga-enable">要求验证</a></li>
                                    </ul>
                                </div>
                                <div class="form-group form-group-label">
                                    <div class="text-center">
                                        <div id="ga-qr" class="qr-center"></div>
                                        密钥：{$user->ga_token}
                                    </div>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="code">测试一下</label>
                                    <input type="text" id="code" placeholder="输入验证器生成的数字来测试"
                                           class="form-control maxwidth-edit">
                                </div>
                            </div>
                            <div class="card-action">
                                <div class="card-action-btn pull-left">
                                    <a class="btn btn-brand-accent btn-flat waves-attach" href="/user/ga_reset"><span
                                                class="mdi mdi-autorenew"></span>&nbsp;重置</a>
                                    <button class="btn btn-flat waves-attach" id="ga-test"><span
                                                class="mdi mdi-check-all"></span>&nbsp;测试
                                    </button>
                                    <button class="btn btn-brand btn-flat waves-attach" id="ga-set"><span class="mdi mdi-check"></span>&nbsp;设置
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xx-12 col-sm-6">
                {if $config['enable_change_email'] == true}
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <div class="cardbtn-edit">
                                        <div class="card-heading">账户邮箱修改</div>
                                        <button class="btn btn-flat" id="email-update"><span class="mdi mdi-check"></span>&nbsp;
                                        </button>
                                    </div>
                                    <p>当前账户邮箱：<code>{$user->email}</code></p>
                                    <div class="form-group form-group-label">
                                        <label class="floating-label" for="newemail">新邮箱</label>
                                        <input class="form-control maxwidth-edit" id="newemail" type="text">
                                    </div>
                                    {if $config['enable_email_verify'] == true}
                                        <div class="form-group form-group-label">
                                            <label class="floating-label" for="email_code">邮箱验证码</label>
                                            <input class="form-control maxwidth-auth" id="email_code" type="text"
                                                onKeypress="javascript:if(event.keyCode == 32)event.returnValue = false;" autocomplete="one-time-code">
                                        </div>
                                        <div class="form-group form-group-label">
                                            <button id="email_verify"
                                                class="btn-reg btn btn-block btn-brand-accent waves-attach waves-light">
                                                获取验证码
                                            </button>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">用戶名修改</div>
                                    <button class="btn btn-flat" id="username-update"><span class="mdi mdi-check"></span>&nbsp;
                                    </button>
                                </div>
                                <p>当前用戶名：<code>{$user->user_name}</code></p>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="newusername">新用戶名</label>
                                    <input class="form-control maxwidth-edit" id="newusername" type="text">
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
                                    <div class="card-heading">账号登录密码修改</div>
                                    <button class="btn btn-flat" id="pwd-update"><span class="mdi mdi-check"></span>&nbsp;
                                    </button>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="oldpwd">当前密码</label>
                                    <input class="form-control maxwidth-edit" id="oldpwd" type="password">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="pwd">新密码</label>
                                    <input class="form-control maxwidth-edit" id="pwd" type="password">
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="repwd">确认新密码</label>
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
                                    <div class="card-heading">IP 解封</div>
                                    <button class="btn btn-flat" id="unblock"><span class="mdi mdi-block-helper"></span>&nbsp;
                                    </button>
                                </div>
                                <p>当前状态：<code id="ajax-block">{$Block}</code></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">每日使用报告设置</div>
                                    <button class="btn btn-flat" id="dailyreport-update"><span class="mdi mdi-check"></span>&nbsp;
                                    </button>
                                </div>
                                <p class="card-heading"></p>
                                <p>当前设置：<code id="ajax-mail" data-default="mail">{if $user->sendDailyMail == 2}TelegramBot接收{elseif $user->sendDailyMail == 1}邮件接收{else}不发送{/if}</code></p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="mail">接收设置</label>
                                    <button type="button" id="mail" class="form-control maxwidth-edit"
                                            data-toggle="dropdown" value="{$user->sendDailyMail}"></button>
                                    <ul class="dropdown-menu" aria-labelledby="mail">
                                        <li>
                                            <a href="#" class="dropdown-option" onclick="return false;" val="0" data="mail">不发送</a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-option" onclick="return false;" val="1" data="mail">邮件接收</a>
                                        </li>
                                        <li>
                                            <a href="#" class="dropdown-option" onclick="return false;" val="2" data="mail">TelegramBot接收</a>
                                        </li>
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
                                    <div class="card-heading">联络方式修改</div>
                                    <button class="btn btn-flat" id="contact_update"><span class="mdi mdi-check"></span>&nbsp;
                                    </button>
                                </div>
                                <p>当前联络方式：
                                    <code data-default="imtype">
                                        {if $user->im_type==1}微信{/if}
                                        {if $user->im_type==2}QQ{/if}
                                        {if $user->im_type==4}Telegram{/if}
                                        {if $user->im_type==5}Discord{/if}
                                    </code>
                                </p>
                                <p>当前联络方式账号：
                                    <code>{$user->im_value}</code>
                                </p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="imtype">选择您的联络方式</label>
                                    <button class="form-control maxwidth-edit" id="imtype" data-toggle="dropdown"
                                            value="{$user->im_type}"></button>
                                    <ul class="dropdown-menu" aria-labelledby="imtype">
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="1"
                                               data="imtype">微信</a></li>
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="2"
                                               data="imtype">QQ</a></li>
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="3"
                                               data="imtype">Facebook</a></li>
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="4"
                                               data="imtype">Telegram</a></li>
                                        <li><a href="#" class="dropdown-option" onclick="return false;" val="5"
                                               data="imtype">Discord</a></li>
                                    </ul>
                                </div>
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="contact">在这输入联络方式账号</label>
                                    <input class="form-control maxwidth-edit" id="contact" type="text">
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
                                            <div class="card-heading">重置端口</div>
                                            <button class="btn btn-flat" id="portreset"><span
                                                        class="mdi mdi-autorenew"></span>&nbsp;
                                            </button>
                                        </div>
                                        <p>对号码不满意？来摇号吧～！</p>
                                        <p>随机更换一个端口使用，价格：<code>{$config['port_price']}</code>元/次</p>
                                        <p>重置后 1 分钟内生效</p>
                                        <p>当前端口：<code id="ajax-user-port">{$user->port}</code></p>
                                    </div>
                                {/if}
                                {if $config['port_price_specify']>=0}
                                    <div class="card-inner">
                                        <div class="cardbtn-edit">
                                            <div class="card-heading">钦定端口</div>
                                            <button class="btn btn-flat" id="portspecify"><span
                                                        class="mdi mdi-check"></span>&nbsp;
                                            </button>
                                        </div>
                                        <p>不想摇号？来钦定端口吧～！</p>
                                        <p>价格：<code>{$config['port_price_specify']}</code>元/次</p>
                                        <p>端口范围：<code>{$config['min_port']}～{$config['max_port']}</code></p>
                                        <div class="form-group form-group-label">
                                            <label class="floating-label" for="port-specify">在这输入想钦定的端口号</label>
                                            <input class="form-control maxwidth-edit" id="port-specify" type="num">
                                        </div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                {/if}
                {if $config['enable_telegram'] === true}
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <div class="cardbtn-edit">
                                        <div class="card-heading">Telegram 绑定</div>
                                    {if $user->telegram_id != 0}
                                        <button class="btn btn-brand-accent btn-flat" id="telegram_reset"><span class="mdi mdi-block-helper"></span>&nbsp;</button>
                                    </div>
                                        <div class="text-center">
                                            <p>当前绑定的 Telegram 账户：<a href="https://t.me/{$user->im_value}">@{$user->im_value}</a></p>
                                        </div>
                                    {else}
                                    </div>
                                        <p>二维码或绑定码有效期 10 分钟，超时请刷新本页面以重新获取，每个只能使用一次</p>
                                        <p><button class="copy-text btn btn-subscription" type="button" data-clipboard-text="/start {$bind_token}">点击拷贝绑定码</button> 私聊发给 Telegram 机器人 <a href="https://t.me/{$telegram_bot}"  target="view_window">@{$telegram_bot}</a> 以绑定 Telegram</p>
                                        <p><a class="btn btn-subscription" type="button" target="view_window" href="https://t.me/{$telegram_bot}?start={$bind_token}">一键绑定至账户</a> 手机电脑平板等如已安装 Telegram 可点击</p>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            <div class="card-inner">
                                <div class="cardbtn-edit">
                                    <div class="card-heading">主题修改</div>
                                    <button class="btn btn-flat" id="theme-update"><span class="mdi mdi-check"></span>&nbsp;
                                    </button>
                                </div>
                                <p>当前主题：<code data-default="theme">{$user->theme}</code></p>
                                <div class="form-group form-group-label control-highlight-custom dropdown">
                                    <label class="floating-label" for="theme">主题</label>
                                    <button id="theme" type="button" class="form-control maxwidth-edit" data-toggle="dropdown" value="{$user->theme}">
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="mail">
                                        {foreach $themes as $theme}
                                            <li>
                                                <a href="#" class="dropdown-option" onclick="return false;"
                                                   val="{$theme}" data="theme">{$theme}</a>
                                            </li>
                                        {/foreach}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {include file='dialog.tpl'}
        </section>
    </div>
</main>

{include file='user/footer.tpl'}

<script>
    $(function () {
        new ClipboardJS('.copy-text');
    });
    $(".copy-text").click(function () {
        $("#result").modal();
        $$.getElementById('msg').innerHTML = '已复制到您的剪贴板。';
    });
</script>
<script>
    /*
     * 筛选 SS/SSR 加密、混淆和协议的选项
     *
     * 引入三个按钮：
     * #filter-btn-ss 筛选 SS，点击以后隐藏 .filter-item-ssr，显示 .filter-item-ss 和 .filter-item-universal
     * #filter-btn-ssr 筛选 SSR，点击以后隐藏 .filter-item-ss，显示 .filter-item-ssr 和 .filter-item-universal
     * #filter-btn-universal 筛选必须同时兼容两者的，点击以后隐藏 .filter-item-ss 和 .filter-item-ssr，显示 .filter-item-universal
     *
     * 引入函数 hideFilterItem(itemType) 和 showFilterItem(itemType)，参数 item 可以是 ss ssr universal。
     */
    (() => {
        const hideFilterItem = (itemType) => {
            for (let i of $$.getElementsByClassName(`filter-item-${
                itemType
            }`)) {
                i.style.display = 'none';
            }
        };
        const showFilterItem = (itemType) => {
            for (let i of $$.getElementsByClassName(`filter-item-${
                itemType
            }`)) {
                i.style.display = 'block';
            }
        };
        const chooseSS = () => {
            hideFilterItem('ssr');
            showFilterItem('ss');
            showFilterItem('universal');
        };
        const chooseSSR = () => {
            hideFilterItem('ss');
            showFilterItem('ssr');
            showFilterItem('universal');
        };
        const chooseUniversal = () => {
            hideFilterItem('ss');
            hideFilterItem('ssr');
            showFilterItem('universal');
        };
        $$.getElementById('filter-btn-ss').addEventListener('click', chooseSS);
        $$.getElementById('filter-btn-ssr').addEventListener('click', chooseSSR);
        $$.getElementById('filter-btn-universal').addEventListener('click', chooseUniversal);
    }
    )();
</script>
<script>
    $(document).ready(function () {
        $("#portreset").click(function () {
            $.ajax({
                type: "POST",
                url: "port_reset",
                dataType: "json",
                data: {},
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('ajax-user-port').innerHTML = data.msg;
                        $$.getElementById('msg').innerHTML = `设置成功，新端口是 ${
                                data.msg
                                }`;
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                    port: $$getValue('port-specify')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('ajax-user-port').innerHTML = $$getValue('port-specify');
                        $$.getElementById('msg').innerHTML = data.msg;
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                    oldpwd: $$getValue('oldpwd'),
                    pwd: $$getValue('pwd'),
                    repwd: $$getValue('repwd')
                },
                success: (data) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#username-update").click(function () {
            $.ajax({
                type: "POST",
                url: "username",
                dataType: "json",
                data: {
                    newusername: $$getValue('newusername')
                },
                success: (data) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
{if $config['enable_change_email'] == true}
    <script>
        $(document).ready(function () {
            $("#email-update").click(function () {
                $.ajax({
                    type: "POST",
                    url: "email",
                    dataType: "json",
                    data: {
                        {if $config['enable_email_verify'] == true}
                            emailcode: $$getValue('email_code'),
                        {/if}
                        newemail: $$getValue('newemail')
                    },
                    success: (data) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    },
                    error: (jqXHR) => {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                })
            })
        })
    </script>
    {if $config['enable_email_verify'] == true}
        <script>
            var wait = 60;
            function time(o) {
                if (wait == 0) {
                    o.removeAttr("disabled");
                    o.text("获取验证码");
                    wait = 60;
                } else {
                    o.attr("disabled", "disabled");
                    o.text("重新发送(" + wait + ")");
                    wait--;
                    setTimeout(function () {
                        time(o)
                    },
                    1000)
                }
            }
            $(document).ready(function () {
                $("#email_verify").click(function () {
                    time($("#email_verify"));
                    $.ajax({
                        type: "POST",
                        url: "send",
                        dataType: "json",
                        data: {
                            email: $$getValue('newemail')
                        },
                        success: (data) => {
                            if (data.ret) {
                                $("#result").modal();
                                $$.getElementById('msg').innerHTML = data.msg;
                            } else {
                                $("#result").modal();
                                $$.getElementById('msg').innerHTML = data.msg;
                            }
                        },
                        error: (jqXHR) => {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = data.msg;
                        }
                    })
                })
            })
        </script>
    {/if}
{/if}
<script>
    var ga_qrcode = '{$user->getGAurl()}',
            qrcode1 = new QRCode(document.getElementById("ga-qr"));

    qrcode1.clear();
    qrcode1.makeCode(ga_qrcode);
    {if $config['enable_telegram'] === true}
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
        $("#contact_update").click(function () {
            $.ajax({
                type: "POST",
                url: "contact_update",
                dataType: "json",
                data: {
                    contact: $$getValue('contact'),
                    imtype: $$getValue('imtype')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                    protocol: $$getValue('protocol'),
                    obfs: $$getValue('obfs'),
                    obfs_param: $$getValue('obfs-param')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('ajax-user-protocol').innerHTML = $$getValue('protocol');
                        $$.getElementById('ajax-user-obfs').innerHTML = $$getValue('obfs');
                        $$.getElementById('ajax-user-obfs-param').innerHTML = $$getValue('obfs-param');
                        $$.getElementById('msg').innerHTML = data.msg;
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                data: {},
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('ajax-block').innerHTML = `IP：${
                                data.msg
                                } 没有被封`;
                        $$.getElementById('msg').innerHTML = `IP：${
                                data.msg
                                } 解封成功过`;
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                url: "ga_check",
                dataType: "json",
                data: {
                    code: $$getValue('code')
                },
                success: (data) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                url: "ga_set",
                dataType: "json",
                data: {
                    enable: $$getValue('ga-enable')
                },
                success: (data) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#passwd_reset").click(function () {
            $.ajax({
                type: "POST",
                url: "passwd_reset",
                dataType: "json",
                data: {},
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '修改成功';
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#telegram_reset").click(function () {
            $.ajax({
                type: "POST",
                url: "telegram_reset",
                dataType: "json",
                data: {},
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '修改成功';
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#url_reset").click(function () {
            $.ajax({
                type: "POST",
                url: "url_reset",
                dataType: "json",
                data: {},
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '修改成功';
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
<script>
    $(document).ready(function () {
        $("#dailyreport-update").click(function () {
            $.ajax({
                type: "POST",
                url: "mail",
                dataType: "json",
                data: {
                    mail: $$getValue('mail')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '修改成功';
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                    theme: $$getValue('theme')
                },
                success: (data) => {
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '修改成功';
                        window.setTimeout("location.href='/user/edit'", {$config['jump_delay']});
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
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
                    method: $$getValue('method')
                },
                success: (data) => {
                    $$.getElementById('ajax-user-method').innerHTML = $$getValue('method');
                    if (data.ret) {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = '修改成功';
                    } else {
                        $("#result").modal();
                        $$.getElementById('msg').innerHTML = data.msg;
                    }
                },
                error: (jqXHR) => {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            })
        })
    })
</script>
