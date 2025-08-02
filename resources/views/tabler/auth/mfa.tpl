{include file='header.tpl'}

<script src="https://unpkg.com/@simplewebauthn/browser/dist/bundle/index.umd.min.js"></script>

<body class="border-top-wide border-primary d-flex flex-column">
<div class="page page-center">
    <div class="container-tight my-auto">
        <div class="card card-md">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">二步验证</h2>
                <p>您的账户已启用二步验证，为了您的账户安全，请您完成附加身份验证。</p>
                {if $method['totp']}
                    <div class="my-5">
                        <div class="row g-4">
                            <div class="col">
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3 px-3"
                                               maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3 px-3"
                                               maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3 px-3"
                                               maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="row g-2">
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3 px-3"
                                               maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3 px-3"
                                               maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control form-control-lg text-center py-3 px-3"
                                               maxlength="1" inputmode="numeric" pattern="[0-9]*" data-code-input="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                <div class="form-footer">
                    {if $method['totp']}
                        <button class="btn btn-primary w-100 mb-3"
                                hx-post="/auth/totp" hx-swap="none" hx-vals="js:{
                                code: code,
                             }">
                            提交
                        </button>
                    {/if}
                    {if $method['fido']}
                        <button class="btn btn-primary w-100" id="webauthnLogin">
                            使用 FIDO2 验证
                        </button>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

{include file='footer.tpl'}

{if $method['totp']}
    <script>
        var code = '';
        document.addEventListener("DOMContentLoaded", function () {
            var inputs = document.querySelectorAll('[data-code-input]');

            for (let i = 0; i < inputs.length; i++) {
                inputs[i].addEventListener('input', function (e) {
                    if (e.target.value.length === e.target.maxLength && i + 1 < inputs.length) {
                        inputs[i + 1].focus();
                    }
                    code = '';
                    inputs.forEach(input => {
                        code += input.value;
                    });
                });
                inputs[i].addEventListener('keydown', function (e) {
                    if (e.target.value.length === 0 && e.keyCode === 8 && i > 0) {
                        inputs[i - 1].focus();
                    }
                });
            }
        });
    </script>
{/if}

{include file='footer.tpl'}

{if $method['fido']}
<script>
    const { startAuthentication } = SimpleWebAuthnBrowser;
    document.getElementById('webauthnLogin').addEventListener('click', async () => {
        const resp = await fetch('/auth/fido');
        const options = await resp.json();
        let asseResp;
        try {
            asseResp = await startAuthentication({ optionsJSON: options });
        } catch (error) {
            document.getElementById("fail-message").innerHTML = error;
            throw error;
        }
        const verificationResp = await fetch('/auth/fido', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(asseResp),
        });
        const verificationJSON = await verificationResp.json();
        if (verificationJSON.ret === 1) {
            document.getElementById("success-message").innerHTML = verificationJSON.msg;
            successDialog.show();
            window.location.href = verificationJSON.redir;
        } else {
            document.getElementById("fail-message").innerHTML = verificationJSON.msg;
            failDialog.show();
        }
    });
</script>
{/if}