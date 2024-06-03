{include file='header.tpl'}

<body class="border-top-wide border-primary d-flex flex-column">
<div class="page page-center">
    <div class="container-tight my-auto">
        <div class="text-center mb-4">
            <a href="#" class="navbar-brand navbar-brand-autodark">
                <img src="/images/next-logo.svg" height="64" alt="Next Panel Logo">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">设置新密码</h2>
                <div class="mb-3">
                    <label class="form-label">新密码</label>
                    <input id="password" type="password" class="form-control" placeholder="请输入新密码">
                </div>
                <div class="mb-3">
                    <label class="form-label">再次输入新密码</label>
                    <input id="confirm_password" type="password" class="form-control" placeholder="请再次输入新密码">
                </div>
                <div class="form-footer">
                    <button class="btn btn-primary w-100"
                            hx-post="/password/token" hx-swap="none"
                            hx-vals='js:{
                            token: location.pathname.split("/").pop(),
                            password: document.getElementById("password").value,
                            confirm_password: document.getElementById("confirm_password").value, }'>
                        <i class="ti ti-key icon"></i>
                        重置
                    </button>
                </div>
            </div>
        </div>
        <div class="text-center text-secondary mt-3">
            已有账户？ <a href="/auth/login" tabindex="-1">点击登录</a>
        </div>
    </div>
</div>

{include file='footer.tpl'}
