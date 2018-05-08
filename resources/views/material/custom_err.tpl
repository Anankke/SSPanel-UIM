<!DOCTYPE HTML>
<html>
<head>
    <title>产生了一个错误 - {$config["appName"]} </title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta charset="utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="shortcut icon" type="image/ico" href="images/ssr.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/sspuic/p@0x01/public/assets/css/main.css"/>
    {if !empty($redirect)}
        <meta http-equiv="refresh" content="3; url={$redirect}"/>
    {/if}
    <noscript>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/sspuic/p@0x01/public/assets/css/noscript.css"/>
    </noscript>
</head>
<body>
<div id="wrapper">
    <header id="header">
        <div class="logo">
            <span class="icon fa-rocket"></span></div>
        <div class="content">
            <div class="inner">
                <h1>{$title}</h1>
                <p>{$message}</p></div>
        </div>
        <nav>
            <ul>
                <li><a href="./#">返回首页</a></li>
                <li>
                    <button onclick="window.history.back();">返回上一页</button>
                </li>
            </ul>
        </nav>
    </header>
    <footer id="footer"><p class="copyright">&copy;2014-2017 {$config["appName"]}</p></footer>
</div>
<div id="bg"></div>
<script src="https://cdn.jsdelivr.net/gh/sspuic/p@0x01/public/assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/sspuic/p@0x01/public/assets/js/skel.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/sspuic/p@0x01/public/assets/js/util.js"></script>
<script src="https://cdn.jsdelivr.net/gh/sspuic/p@0x01/public/assets/js/main.js"></script>
</body>
</html>