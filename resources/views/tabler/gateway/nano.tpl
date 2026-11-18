<div class="card-inner">
    <h4>
        Nano (XNO)
    </h4>
    <p class="card-heading"></p>
    <form class="nano" name="nano" method="post" onsubmit="return false;">
        <button class="btn btn-flat waves-attach" type="button" onclick="nanoStart()">
            <span>Pay</span>
        </button>
    </form>
    <div id="nano-instructions" style="display:none;margin-top:1rem;">
        <p>Send exactly <strong><span id="nano-amount"></span> XNO</strong> to</p>
        <p><code id="nano-account"></code></p>
        <p>Set your wallet's representative for this payment to</p>
        <p><code id="nano-representative"></code></p>
        <p class="text-muted" id="nano-status">Waiting for your payment...</p>
    </div>
</div>

<script>
    function nanoStart() {
        var invoice = {$invoice->id};
        fetch('/user/payment/purchase/nano', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'invoice_id=' + encodeURIComponent(invoice) + '&type=nano'
        }).then(function (r) { return r.json(); }).then(function (d) {
            if (d.ret !== 1 || !d.nano) {
                document.getElementById('nano-status').textContent = d.msg || 'Unable to start payment';
                document.getElementById('nano-instructions').style.display = 'block';
                return;
            }
            document.getElementById('nano-amount').textContent = d.nano.amount_xno;
            document.getElementById('nano-account').textContent = d.nano.account;
            document.getElementById('nano-representative').textContent = d.nano.representative;
            document.getElementById('nano-instructions').style.display = 'block';
            nanoPoll(d.nano.trade_no);
        });
    }

    function nanoPoll(tradeNo) {
        var timer = setInterval(function () {
            fetch('/payment/notify/nano?trade_no=' + encodeURIComponent(tradeNo))
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    if (d.ret === 1) {
                        clearInterval(timer);
                        document.getElementById('nano-status').textContent = 'Payment received.';
                        window.location.reload();
                    }
                });
        }, 6000);
    }
</script>
