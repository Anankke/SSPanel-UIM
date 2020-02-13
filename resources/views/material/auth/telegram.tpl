{if $config['enable_telegram'] === true}
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs@gh-pages/qrcode.min.js"></script>
    <script>
        var telegram_qrcode = 'mod://login/{$login_token}';
        var qrcode = new QRCode(document.getElementById("telegram-qr"));
        qrcode.clear();
        qrcode.makeCode(telegram_qrcode);
    </script>
    <script>
        $(document).ready(function () {
            $("#calltgauth").click(
                    function () {
                        f();
                    }
            );

            function f() {
                $.ajax({
                    type: "POST",
                    url: "qrcode_check",
                    dataType: "json",
                    data: {
                        token: "{$login_token}",
                        number: "{$login_number}"
                    },
                    success: (data) => {
                        if (data.ret > 0) {
                            clearTimeout(tid);

                            $.ajax({
                                type: "POST",
                                url: "/auth/qrcode_login",
                                dataType: "json",
                                data: {
                                    token: "{$login_token}",
                                    number: "{$login_number}"
                                },
                                success: (data) => {
                                    if (data.ret) {
                                        $("#result").modal();
                                        $$.getElementById('msg').innerHTML = '登录成功！';
                                        window.setTimeout("location.href=/user/", {$config['jump_delay']});
                                    }
                                },
                                error: (jqXHR) => {
                                    $("#result").modal();
                                    $$.getElementById('msg').innerHTML = `发生错误：${
                                            jqXHR.status
                                            }`;
                                }
                            });

                        } else {
                            if (data.ret === -1) {
                                $('#telegram-qr').replaceWith('此二维码已经过期，请刷新页面后重试。');
                                $('#code_number').replaceWith('<code id="code_number">此数字已经过期，请刷新页面后重试。</code>');
                            }
                        }
                    },
                    error: (jqXHR) => {
                        if (jqXHR.status !== 200 && jqXHR.status !== 0) {
                            $("#result").modal();
                            $$.getElementById('msg').innerHTML = `发生错误：${
                                    jqXHR.status
                                    }`;
                        }
                    }
                });
                tid = setTimeout(f, 2500); //循环调用触发setTimeout
            }


        })
    </script>
{/if}

{if $config['enable_telegram'] === true}
    <script>
        $(document).ready(function () {
            var el = document.createElement('script');
            document.getElementById('telegram-login-box').append(el);
            el.onload = function () {
                $('#telegram-alert').remove()
            }
            el.src = 'https://telegram.org/js/telegram-widget.js?4';
            el.setAttribute('data-size', 'large')
            el.setAttribute('data-telegram-login', '{$telegram_bot}')
            el.setAttribute('data-auth-url', '{$base_url}/auth/telegram_oauth')
            el.setAttribute('data-request-access', 'write')
        });
    </script>
{/if}
