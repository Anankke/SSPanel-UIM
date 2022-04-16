{include file='tabler_header.tpl'}

<body class="border-top-wide border-primary d-flex flex-column">
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">405</div>
                <p class="empty-subtitle text-muted">
                    状态码 405 Method Not Allowed 表明服务器禁止了使用当前 HTTP 方法的请求
                </p>
                <div class="empty-action">
                    <a href="/user" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                            <line x1="5" y1="12" x2="11" y2="18" />
                            <line x1="5" y1="12" x2="11" y2="6" />
                        </svg>
                        返回用户首页
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
{include file='tabler_footer.tpl'}
</html>