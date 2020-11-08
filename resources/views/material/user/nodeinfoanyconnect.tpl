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
                                    <p>下面为您的 Anyconnect 配置。</p>
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
                                    <p class="card-heading">客户端下载</p>
                                    <p>由于版权问题，此处不提供下载。</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-sm-12">
                        <div class="card">
                            <div class="card-main">
                                <div class="card-inner margin-bottom-no">
                                    <p class="card-heading">配置方法</p>
                                    <p>Windows：下载客户端安装后打开，再打开的窗口中点击左下角的设施（齿轮）按钮，取消勾选Block connections to untrusted
                                        servers，之后在框中输入服务器地址，点击connect，如果出现提示框点击connext
                                        anyway即可，并在随后弹出的认证框中填写用户名密码连接就好。</p>
                                    <p>Mac OS X：下载客户端安装后打开，再打开的窗口中点击左下角的设施（齿轮）按钮，取消勾选Block connections to untrusted
                                        servers，之后在框中输入服务器地址，点击connect，如果出现提示框点击connext
                                        anyway即可，并在随后弹出的认证框中填写用户名密码连接就好。</p>
                                    <p>
                                        Android：下载客户端安装后打开。点击连接-添加新的VPN连接，并在其中输入服务器地址（如配置信息所示）后点击完成并在上一页中选择新建的连接，之后返回第一页，在anyconnect
                                        VPN一行中有个开关，点击开关将其打开，如出现安全警告，点击继续即可，之后在弹出的认证框中输入用户名密码，连接即可。</p>
                                    <p>iOS：从 App Store
                                        中下载客户端安装后打开。点击连接-添加新的VPN连接，并在其中输入服务器地址（如配置信息所示）后点击完成并在上一页中选择新建的连接，之后返回第一页，在anyconnect
                                        VPN一行中有个开关，点击开关将其打开，如出现安全警告，点击继续即可，之后在弹出的认证框中输入用户名密码，连接即可。</p>
                                    <p>windows
                                        Phone：在应用商店中下载客户端安装后打开设置-网络-VPN-添加VPN连接，VPN提供商一栏选择anyconnect，连接名称任意，服务器名称或地址中填写如配置信息所示的地址，之后点击保存即可，连接时点击对应的新建的VPN，点击连接，之后会提示输入用户名与密码，分别输入后即可。</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

{include file='user/footer.tpl'}