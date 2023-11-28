<div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-circle-check icon mb-2 text-green icon-lg" style="font-size:3.5rem;"></i>
                <p id="success-message" class="text-secondary">成功</p>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a id="success-confirm" href="" class="btn w-100" data-bs-dismiss="modal">
                                好
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <i class="ti ti-circle-x icon mb-2 text-danger icon-lg" style="font-size:3.5rem;"></i>
                <p id="fail-message" class="text-secondary">失败</p>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                确认
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row text-center align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Powered by <a href="/staff" class="link-secondary">SSPanel-UIM</a>
                        <!-- 删除staff是不尊重每一位开发者的行为 -->
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        Theme by <a href="https://tabler.io/" class="link-secondary">Tabler</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
</div>
</div>
<!-- js -->
<script src="//{$config['jsdelivr_url']}/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
<script>
    let successDialog = new bootstrap.Modal(document.getElementById('success-dialog'));
    let failDialog = new bootstrap.Modal(document.getElementById('fail-dialog'));

    let clipboard = new ClipboardJS('.copy');
    clipboard.on('success', function () {
        document.getElementById("success-message").innerHTML = '已复制到剪切板';
        successDialog.show();
    });

    htmx.on("htmx:afterRequest", function(evt) {
        if (evt.detail.xhr.getResponseHeader('HX-Refresh') === 'true' ||
            evt.detail.xhr.getResponseHeader('HX-Trigger'))
        {
            return;
        }

        let res = JSON.parse(evt.detail.xhr.response);

        if (typeof res.data !== 'undefined') {
            for (let key in res.data) {
                if (res.data.hasOwnProperty(key)) {
                    if (key === "ga-url") {
                        qrcode.clear();
                        qrcode.makeCode(res.data[key]);
                    }

                    if (key === "last-checkin-time") {
                        document.getElementById("check-in").innerHTML = "已签到"
                        document.getElementById("check-in").disabled = true;
                    }

                    let element = document.getElementById(key);

                    if (element) {
                        element.innerHTML = res.data[key];
                    }
                }
            }
        }

        if (res.ret === 1) {
            document.getElementById("success-message").innerHTML = res.msg;
            successDialog.show();
        } else {
            document.getElementById("fail-message").innerHTML = res.msg;
            failDialog.show();
        }
    });
</script>
<script>console.table([['数据库查询', '执行时间'], ['{count($queryLog)} 次', '{$optTime} ms']])</script>

{include file='live_chat.tpl'}

</body>

</html>
