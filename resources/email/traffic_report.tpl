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
                                    每日流量报告
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
                                    用户名: {$user->user_name}
                                    <br>
                                    Email: {$user->email}
                                    <br><br>
                                    总流量: {$enable_traffic}
                                    <br>
                                    已用流量: {$used_traffic}
                                    <br>
                                    剩余流量: {$unused_traffic}
                                    <br>
                                    今日使用流量: {$lastday_traffic}
                                    <br><br>
                                </p>
                                <p class="midText">
                                    {$text}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

{include file='footer.tpl'}
