{include file='header.tpl'}

<body style="background-color:#EEEEEE;">
    <div style="text-align: center">
        <div border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top:30px;table-layout:fixed;background-color:#EEEEEE;" id="bodyTable">
            <div align="center" valign="top" style="padding-right:10px;padding-left:10px;" id="bodyCell">
                <div border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;max-width:600px;text-align: center" width="100%" class="wrapperTable">
                    <div align="center" valign="top">
                        <div border="0" cellpadding="0" cellspacing="0" width="100%" class="logoTable">
                            <div align="center" valign="middle" style="padding-top:60px;padding-bottom:60px">
                                <h2 class="bigTitle">
                                    密码重置
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div border="0" cellpadding="0" cellspacing="0" style="max-width:600px;text-align: center" width="100%" class="wrapperTable">
                    <div align="center" valign="top">
                        <div border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF" width="100%" class="oneColumn">
                            <div align="center" valign="top" style="padding-bottom:60px;padding-left:20px;padding-right:20px;" class="description">
                                <p class="midText">
                                    你收到此邮件是因为你在 {$config['appName']} 系统申请了密码重置，如果非本人申请，请忽略此邮件。
                                    <br><br>
                                    <a href="{$resetUrl}" style="color:#505050" target="_blank">点击此链接重置密码</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

{include file='footer.tpl'}
