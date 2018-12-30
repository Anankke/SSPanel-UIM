<!DOCTYPE HTML>
<html>
<head>
    <title>正在跳转用户中心 - {$config["appName"]} </title>
    <meta name="keywords" content=""/>
    <meta name="description" content=""/>
    <meta charset="utf-8"/>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="bookmark" href="/favicon.ico" type="image/x-icon"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
    <link rel="shortcut icon" type="image/ico" href="images/ssr.ico">
	<style>
        /*! Spectre.css v0.5.0 | MIT License | github.com/picturepan2/spectre */

        html {
            font-family: sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%
        }

        body {
            margin: 0
        }

        footer {
            display: block
        }

        a {
            background-color: transparent;
            -webkit-text-decoration-skip: objects
        }

        a:active,
        a:hover {
            outline-width: 0
        }

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit
        }

        *,
        ::after,
        ::before {
            box-sizing: inherit
        }

        html {
            box-sizing: border-box;
            font-size: 20px;
            line-height: 1.5;
            -webkit-tap-highlight-color: transparent
        }

        body {
            background: #fff;
            color: #50596c;
            font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;
            font-size: .8rem;
            overflow-x: hidden;
            text-rendering: optimizeLegibility
        }

        a {
            color: #5755d9;
            outline: 0;
            text-decoration: none
        }

        a:focus {
            box-shadow: 0 0 0 .1rem rgba(87, 85, 217, .2)
        }

        a:active,
        a:focus,
        a:hover {
            color: #4240d4;
            text-decoration: underline
        }

        .h1,
        .h4 {
            font-weight: 500
        }

        .h1 {
            font-size: 2rem
        }

        .h4 {
            font-size: 1.2rem
        }

        p {
            margin: 0 0 1rem
        }

        a {
            -webkit-text-decoration-skip: ink edges;
            text-decoration-skip: ink edges
        }

        .form-input:not(:placeholder-shown):invalid {
            border-color: #e85600
        }

        .form-input:not(:placeholder-shown):invalid:focus {
            box-shadow: 0 0 0 .1rem rgba(232, 86, 0, .2)
        }

        .container {
            margin-left: auto;
            margin-right: auto;
            padding-left: .4rem;
            padding-right: .4rem;
            width: 100%
        }

        .container.grid-lg {
            max-width: 976px
        }

        .empty {
            background: #f8f9fa;
            border-radius: .1rem;
            color: #667189;
            padding: 3.2rem 1.6rem;
            text-align: center
        }

        .empty .empty-subtitle,
        .empty .empty-title {
            margin: .4rem auto
        }

        .empty .empty-action {
            margin-top: .8rem
        }

        .text-error {
            color: #e85600
        }

        .divider {
            display: block;
            position: relative
        }

        .divider {
            border-top: .05rem solid #e7e9ed;
            height: .05rem;
            margin: .4rem 0
        }

        .container::after {
            clear: both;
            content: "";
            display: table
        }

        .centered {
            display: block;
            float: none;
            margin-left: auto;
            margin-right: auto
        }

        .valign {
            display: -webkit-box!important;
            display: -webkit-flex!important;
            display: -ms-flexbox!important;
            display: flex!important;
            -webkit-box-align: center!important;
            -webkit-align-items: center!important;
            -ms-flex-align: center!important;
            align-items: center!important;
        }

        .section-footer {
            color: #acb3c2;
            padding: 2rem .5rem 0 .5rem;
            position: relative;
            z-index: 200;
        }

        .section-footer a {
            color: #667189;
        }
    </style>
    {if !empty($redirect)}
        <meta http-equiv="refresh" content="3; url={$redirect}"/>
    {/if}
    <noscript>
        <link rel="stylesheet" href="/assets/css/noscript.css"/>
    </noscript>
</head>
<body>
<div class="empty valign" style="height:100vh">
<div class="centered">
<p class="empty-title h1">{$title}</p>
<p class="empty-title h4">{$message}</p>
<div class="divider"></div>
<div class="empty-action">
</div>
<footer class="section section-footer">
<div id="copyright" class="grid-footer container grid-lg">©
<span year="">{date("Y")}</span>
<a href="{$config["baseUrl"]}" target="_blank">{$config["appName"]}</a>
</div>
</footer>
</div>
</div>
</body>
</html>
