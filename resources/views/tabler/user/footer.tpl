<div class="modal modal-blur fade" id="success-dialog" role="dialog">
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
                            <button type="button" id="success-confirm" class="btn w-100" data-bs-dismiss="modal">
                                好
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="fail-dialog" role="dialog">
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
<script src="//{$config['jsdelivr_url']}/npm/@tabler/core@latest/dist/js/tabler.min.js"></script>
<script>
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const bgColor = type === 'danger' ? 'bg-danger' : 'bg-success';
        toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 ' + bgColor + ' text-white px-4 py-2 rounded';
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.remove();
        }, 2000);
    }

    window.addEventListener('load', function() {
        if (typeof tabler !== 'undefined' && tabler.bootstrap) {
            window.successDialog = new tabler.bootstrap.Modal(document.getElementById('success-dialog'));
            window.failDialog = new tabler.bootstrap.Modal(document.getElementById('fail-dialog'));
        }
    });

    // Initialize clipboard functionality
    if (typeof ClipboardJS !== 'undefined' && document.querySelector('.copy')) {
        let clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            showToast('已复制到剪切板');
            e.clearSelection();
        });
        
        clipboard.on('error', function(e) {
            console.error('复制失败:', e);
            const text = e.trigger.getAttribute('data-clipboard-text');
            if (text) {
                // Try native API first, fallback to prompt
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function() {
                        showToast('已复制到剪切板');
                    }).catch(function(err) {
                        console.error('原生 API 也失败了:', err);
                        prompt('复制失败，请手动复制以下内容：', text);
                    });
                } else {
                    prompt('复制失败，请手动复制以下内容：', text);
                }
            } else {
                showToast('复制失败，请重试', 'danger');
            }
        });
    } else if (typeof ClipboardJS === 'undefined') {
        console.error('ClipboardJS library not loaded');
        document.querySelectorAll('.copy').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const text = this.getAttribute('data-clipboard-text');
                if (text) {
                    prompt('请手动复制以下内容：', text);
                }
            });
        });
    }

    htmx.on("htmx:afterRequest", function(evt) {
        if (evt.detail.xhr.getResponseHeader('HX-Refresh') === 'true' ||
            evt.detail.xhr.getResponseHeader('HX-Trigger'))
        {
            return;
        }

        try {
            let res = JSON.parse(evt.detail.xhr.response);

            if (typeof res.data !== 'undefined') {
                // Update DOM elements with response data
                for (let key in res.data) {
                    if (res.data.hasOwnProperty(key)) {
                        if (key === "ga-url" && typeof qrcode !== 'undefined') {
                            qrcode.clear();
                            qrcode.makeCode(res.data[key]);
                            continue;
                        }

                        if (key === "last-checkin-time") {
                            const checkInBtn = document.getElementById("check-in");
                            checkInBtn.textContent = "已签到";
                            checkInBtn.disabled = true;
                            continue;
                        }

                        const element = document.getElementById(key);
                        if (element) {
                            if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
                                element.value = res.data[key];
                            } else {
                                element.textContent = res.data[key];
                            }
                        }
                    }
                }
            }

            // Show success or error message
            const isSuccess = res.ret === 1;
            const messageId = isSuccess ? "success-message" : "fail-message";
            const dialog = isSuccess ? window.successDialog : window.failDialog;
            
            document.getElementById(messageId).textContent = res.msg;
            if (dialog) {
                dialog.show();
            } else {
                showToast(res.msg, isSuccess ? 'success' : 'danger');
            }
        } catch (e) {
            console.error("Failed to parse HTMX response:", e);
            showToast('发生了意外错误', 'danger');
        }
    });
</script>
<script>console.table([['数据库查询', '执行时间'], ['{count($queryLog)} 次', '{$optTime} ms']])</script>

{include file='live_chat.tpl'}

{include file='telemetry.tpl'}

</body>

</html>
