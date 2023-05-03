<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"/>
</head>

<body style="background-color:#EEEEEE;">
    <div style="text-align: center">
        <div border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top:30px;table-layout:fixed;background-color:#EEEEEE;" id="bodyTable">
            <div align="center" valign="top" style="padding-right:10px;padding-left:10px;" id="bodyCell">
                <div border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF;max-width:600px;text-align: center" width="100%" class="wrapperTable">
                    <div align="center" valign="top">
                        <div border="0" cellpadding="0" cellspacing="0" width="100%" class="logoTable">
                            <div align="center" valign="middle" style="padding-top:60px;padding-bottom:60px">
                                <h2 class="bigTitle" style="color:#000000; font-family:'Open Sans', Tahoma, Verdana, sans-serif; font-size:26px; font-weight:600; font-style:normal; letter-spacing:normal; line-height:34px; text-align:center; padding:0; margin:0;">
                                    邮箱验证
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div border="0" cellpadding="0" cellspacing="0" style="max-width:600px;text-align: center" width="100%" class="wrapperTable">
                    <div align="center" valign="top">
                        <div border="0" cellpadding="0" cellspacing="0" style="background-color:#FFFFFF" width="100%" class="oneColumn">
                            <div align="center" valign="top" style="padding-bottom:60px;padding-left:20px;padding-right:20px;" class="description">
                                <p class="midText" style="color:#000000; font-family:'Open Sans', Tahoma, Verdana, sans-serif; font-size:14px; font-weight:400; line-height:22px; text-align:center; padding:0; margin:0;">
                                    你请求的邮箱验证代码为: <b style="color:#8D6CD1">{$code}</b> <br>
                                    本验证代码在 {$expire} 前有效。<br>
                                    如果此验证码非你本人所请求，请直接忽视。<br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div border="0" cellpadding="0" cellspacing="0" style="max-width:600px;text-align: center" width="100%" class="wrapperTable">
                    <div align="center" valign="top">
                        <div border="0" cellpadding="0" cellspacing="0" width="100%" class="footer">
                            <div>
                                <div align="center" valign="top" style="padding-top:15px;padding-bottom:30px;padding-left:10px;padding-right:10px;" class="brandInfo">
                                    <p class="smlText" style="color:#313131; font-family:'Open Sans', Tahoma, Verdana, sans-serif; font-size:11px; font-weight:400; line-height:18px; text-align:center; margin:0; padding:0;">
                                        <a href="{$config['baseUrl']}" style="color:#8D6CD1;text-decoration:none" target="_blank">{$config['appName']}</a> |
                                        <a href="{$config['baseUrl']}/user/edit" style="color:#8D6CD1;text-decoration:none" target="_blank">修改邮件接收设置</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
