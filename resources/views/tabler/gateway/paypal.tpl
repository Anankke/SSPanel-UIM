<script src="//www.paypal.com/sdk/js?client-id={$public_setting['paypal_client_id']}&currency={$public_setting['paypal_currency']}"></script>

<div class="card-inner">
    <h4>
        PayPal
    </h4>
    <p class="card-heading"></p>
    <div id="paypal-button-container"></div>
</div>

<script>
    paypal.Buttons({
        createOrder() {
            return fetch("/user/payment/purchase/paypal", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    invoice_id: {$invoice->id},
                }),
            })
                .then((response) => response.json())
                .then((order) => order.id);
        },
        onApprove() {
            window.setTimeout(location.href = '/user/invoice', {$config['jump_delay']});
        }
    }).render('#paypal-button-container');

</script>
