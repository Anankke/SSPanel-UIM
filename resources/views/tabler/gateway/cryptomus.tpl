<div class="card-inner">
    <h4>
        Cryptomus
    </h4>
    <p class="card-heading"></p>
    <form class="cryptomus" name="cryptomus" method="post">
        <button class="btn btn-flat waves-attach"
                hx-post="/user/payment/purchase/cryptomus" hx-swap="none"
                hx-vals='js:{
                    price: {$invoice->price},
                    invoice_id: {$invoice->id},
                    type: "cryptomus",
                    redir: window.location.href
                }'>
            <span>Pay</span>
        </button>
    </form>
</div>
