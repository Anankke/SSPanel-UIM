<link rel="stylesheet"
      href="https://{$config['jsdelivr_url']}/npm/@tabler/core@latest/dist/css/tabler-payments.min.css">

<div class="card-inner">
    <h4>
        Stripe
    </h4>
    <p class="card-heading"></p>
    <p>可以使用带有
        <span class="payment payment-xs payment-provider-unionpay me-auto"></span>
        <span class="payment payment-xs payment-provider-mastercard me-auto"></span>
        <span class="payment payment-xs payment-provider-visa me-auto"></span>
        等标识的信用卡或借记卡</p>
    <form action="/user/payment/purchase/stripe" method="post">
        <div class="form-group form-group-label">
            <input id="price" name="price" value="{$invoice->price}" hidden>
            <input id="invoice_id" name="invoice_id" value="{$invoice->id}" hidden>
            <button class="btn btn-flat waves-attach" type="submit"><i class="icon ti ti-credit-card"></i></button>
        </div>
    </form>
</div>
