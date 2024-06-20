<div class="card-inner">
    <h4>
        EPay
    </h4>
    <p class="card-heading"></p>
    <form class="epay" name="epay" method="post">
        {if $public_setting['epay_alipay']}
        <button class="btn btn-flat waves-attach"
                hx-post="/user/payment/purchase/epay" hx-swap="none"
                hx-vals='js:{
                    price: {$invoice->price},
                    invoice_id: {$invoice->id},
                    type: "alipay",
                    redir: window.location.href
                }'>
            <img src="/images/alipay.png" height="50px"/>
        </button>
        {/if}
        {if $public_setting['epay_wechat']}
        <button class="btn btn-flat waves-attach"
                hx-post="/user/payment/purchase/epay" hx-swap="none"
                hx-vals='js:{
                    price: {$invoice->price},
                    invoice_id: {$invoice->id},
                    type: "wxpay",
                    redir: window.location.href
                }'>
            <img src="/images/wechat.png" height="50px"/>
        </button>
        {/if}
        {if $public_setting['epay_qq']}
        <button class="btn btn-flat waves-attach"
                hx-post="/user/payment/purchase/epay" hx-swap="none"
                hx-vals='js:{
                    price: {$invoice->price},
                    invoice_id: {$invoice->id},
                    type: "qqpay",
                    redir: window.location.href
                }'>
            <img src="/images/qqpay.png" height="50px"/>
        </button>
        {/if}
        {if $public_setting['epay_usdt']}
        <button class="btn btn-flat waves-attach"
                hx-post="/user/payment/purchase/epay" hx-swap="none"
                hx-vals='js:{
                    price: {$invoice->price},
                    invoice_id: {$invoice->id},
                    type: "usdt",
                    redir: window.location.href
                }'>
            <img src="/images/usdt.png" height="50px"/>
        </button>
        {/if}
    </form>
</div>
