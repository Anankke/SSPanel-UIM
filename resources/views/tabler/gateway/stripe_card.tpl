<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@latest/dist/css/tabler-payments.min.css">

<div class="card-inner">
    <p class="card-heading">银行卡充值</p>
    <p>可以使用带有 <span class="payment payment-provider-unionpay me-3"></span> / <span class="payment payment-provider-mastercard me-3"></span> / <span class="payment payment-provider-visa me-3"></span> 等标识的信用卡或借记卡</p>
    <form action="/user/payment/purchase/stripe_card" method="post">
        <div class="form-group form-group-label">    
            <label class="floating-label" for="amount-stripe-card">金额</label>
            <input class="form-control maxwidth-edit" id="price" name="price" min="{$public_setting['stripe_min_recharge']}" max="{$public_setting['stripe_max_recharge']}" step="0.1" type="number" required="required">
            <button class="btn btn-flat waves-attach" type="submit"><i class="icon ti ti-credit-card"></i></button>
        </div>
    </form>
</div>