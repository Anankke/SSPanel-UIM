<div class="card auth-tg cust-model">
    <div class="card-main">
        <nav class="tab-nav margin-top-no margin-bottom-no">
            <ul class="nav nav-justified">
                <li class="active">
                    <a class="waves-attach" data-toggle="tab" href="#number_login">一键/验证码登录</a>
                </li>
                <li>
                    <a class="waves-attach" data-toggle="tab" href="#qrcode_login">二维码登录</a>
                </li>
            </ul>
        </nav>
        <div class="tab-pane fade active in" id="number_login">
            <div class="card-header">
                <div class="card-inner">
                    <h1 class="card-heading" style=" text-align:center;font-weight:bold;">Telegram 登录</h1>
                </div>
            </div>
            <div class="card-inner">
                <div class="text-center">
                    <p>一键登陆</p>
                </div>
                <p id="telegram-alert">正在载入 Telegram，如果长时间未显示请刷新页面或检查代理</p>
                <div class="text-center" id="telegram-login-box"></div>
                <p>或者添加机器人账号 <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a>，发送下面的数字给它。
                </p>
                <div class="text-center">
                    <h2><code id="code_number">{$login_number}</code></h2>
                </div>

            </div>
        </div>
        <div class="tab-pane fade" id="qrcode_login">
            <div class="card-header">
                <div class="card-inner">
                    <h1 class="card-heading" style=" text-align:center;font-weight:bold;">Telegram扫码登录</h1>
                </div>
            </div>
            <div class="card-inner">
                <p>添加机器人账号 <a href="https://t.me/{$telegram_bot}">@{$telegram_bot}</a>，拍下下面这张二维码发给它。
                </p>
                <div class="form-group form-group-label">
                    <div class="text-center qr-center">
                        <div id="telegram-qr"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>