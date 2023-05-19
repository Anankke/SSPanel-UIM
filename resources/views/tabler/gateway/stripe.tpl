<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler-payments.min.css">

<div class="card-inner">
    <h4>
        Stripe 银行卡
    </h4>
    <p class="card-heading"></p>
    <p>可以使用带有
        <span class="payment payment-xs payment-provider-unionpay me-auto"></span>
        <span class="payment payment-xs payment-provider-mastercard me-auto"></span>
        <span class="payment payment-xs payment-provider-visa me-auto"></span>
        等标识的信用卡或借记卡</p>
    <form action="/user/payment/purchase/stripe" method="post">
        <div class="form-group form-group-label">    
            <label class="floating-label" for="amount-stripe-card">金额</label>
            <input class="form-control maxwidth-edit" id="price" name="price" min="{$public_setting['stripe_min_recharge']}" max="{$public_setting['stripe_max_recharge']}" step="0.1" type="number" required="required">
            <button class="btn btn-flat waves-attach" type="submit"><i class="icon ti ti-credit-card"></i></button>
        </div>
    </form>
</div>