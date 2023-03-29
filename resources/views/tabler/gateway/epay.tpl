<div class="card-inner">
    <h4>
        EPay 在线充值
    </h4>
    <p class="card-heading"></p>
    <form class="epay" name="epay" action="/user/payment/purchase/epay" method="post">
        {if $user->use_new_shop}
        <input hidden id="price" name="price" value="{$invoice->price}">
        <input hidden id="invoice_id" name="invoice_id" value="{$invoice->id}">
        {else}
        <input class="form-control maxwidth-edit" id="price" name="price" placeholder="输入金额，选择以下要付款的渠道"
            autofocus="autofocus" type="number" min="0.01" max="1000" step="0.01" required="required">
        <br />
        {/if}
        {if $public_setting['epay_alipay'] == true}
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="alipay">
            <img src="/images/alipay.png" height="50px" />
        </button>
        {/if}
        {if $public_setting['epay_wechat'] == true}
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="wxpay">
            <img src="/images/wechat.png" height="50px" />
        </button>
        {/if}
        {if $public_setting['epay_qq'] == true}
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="qqpay">
            <img src="/images/qqpay.png" height="50px" />
        </button>
        {/if}
        {if $public_setting['epay_usdt'] == true}
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="usdt">
            <img src="/images/usdt.png" height="50px" />
        </button>
        {/if}
    </form>
</div>