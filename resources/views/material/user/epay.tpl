<div class="card-inner">
    <h4>
        epay在线充值
    <h4>
    <p class="card-heading"></p>
    <form class="epay" name="epay" action="/user/payment/purchase/epay" method="post">
        <input class="form-control maxwidth-edit" id="price" name="price" placeholder="输入金额，选择以下要付款的渠道"
            autofocus="autofocus" type="number" min="0.01" max="1000" step="0.01" required="required">
        <br />
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="alipay">
            <img src="/images/alipay.jpg" height="50px" />
        </button>
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="wxpay">
            <img src="/images/weixin.jpg" height="50px" />
        </button>
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value="qqpay">
            <img src="/images/qqpay.jpg" height="50px" />
        </button>
        <button class="btn btn-flat waves-attach" id="btnSubmit" type="submit" name="type" value=usdt">
            <img src="/images/usdt.png" height="50px" />
        </button>

    </form>
</div>