{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">创建订单</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">创建商品订单</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-sm-12 col-md-6 col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">订单内容</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-transparent table-responsive">
                                <tr hidden>
                                    <td>商品ID</td>
                                    <td id="product-id" class="text-end">{$product->id}</td>
                                </tr>
                                <tr>
                                    <td>商品名称</td>
                                    <td class="text-end">{$product->name}</td>
                                </tr>
                                <tr>
                                    <td>商品类型</td>
                                    {if $product->type === 'tabp'}
                                        <td class="text-end">时间流量包</td>
                                    {elseif $product->type === 'time'}
                                        <td class="text-end">时间包</td>
                                    {elseif $product->type === 'bandwidth'}
                                        <td class="text-end">流量包</td>
                                    {/if}
                                </tr>
                                {if $product->type === 'tabp' || $product->type === 'time'}
                                <tr>
                                    <td>商品时长</td>
                                    <td class="text-end">{$product->content->time} 天</td>
                                </tr>
                                <tr>
                                    <td>等级时长</td>
                                    <td class="text-end">{$product->content->class_time} 天</td>
                                </tr>
                                <tr>
                                    <td>等级</td>
                                    <td class="text-end">Lv. {$product->content->class}</td>
                                </tr>
                                {/if}
                                {if $product->type === 'tabp' || $product->type === 'bandwidth'}
                                <tr>
                                    <td>可用流量</td>
                                    <td class="text-end">{$product->content->bandwidth} GB</td>
                                </tr>
                                {/if}
                                {if $product->type === 'tabp' || $product->type === 'time'}
                                <tr>
                                    <td>速率限制</td>
                                    {if $product->content->speed_limit === '0'}
                                    <td class="text-end">不限制</td>
                                    {else}
                                    <td class="text-end">{$product->content->speed_limit} Mbps</td>
                                    {/if}
                                </tr>
                                <tr>
                                    <td>同时连接 IP 限制</td>
                                    {if $product->content->ip_limit === '0'}
                                    <td class="text-end">不限制</td>
                                    {else}
                                    <td class="text-end">{$product->content->ip_limit}</td>
                                    {/if}
                                </tr>
                                {/if}
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">价格明细（元）</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-transparent table-responsive">
                                <tr>
                                    <td>商品价格</td>
                                    <td class="text-end">{$product->price}</td>
                                </tr>
                                <tr>
                                    <td>优惠码</td>
                                    <td class="text-end" id="coupon-code"></td>
                                </tr>
                                <tr>
                                    <td>优惠金额</td>
                                    <td class="text-end" id="product-buy-discount"></td>
                                </tr>
                                <tr>
                                    <td>实际支付</td>
                                    <td class="text-end" id="product-buy-total">{$product->price}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="card my-3">
                        <div class="card-header">
                            <h3 class="card-title">优惠码</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="input-group mb-2">
                                    <input id="coupon" type="text" class="form-control" placeholder="填写优惠码，没有请留空">
                                    <button id="verify-coupon" class="btn" type="button">应用</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card my-3">
                        <div class="card-body">
                            <button id="create-order" href=""
                            class="btn btn-primary w-100 my-3">创建订单</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#verify-coupon").click(function() {
            $.ajax({
                url: '/user/coupon',
                type: 'POST',
                dataType: "json",
                data: {
                    coupon: $('#coupon').val(),
                    product_id: {$product->id},
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#coupon-code').text($('#coupon').val());
                        $('#product-buy-discount').text(data.discount);
                        $('#product-buy-total').text(data.buy_price);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#create-order").click(function() {
            $.ajax({
                url: '/user/order/create',
                type: 'POST',
                dataType: "json",
                data: {
                    coupon: $('#coupon').val(),
                    product_id: {$product->id},
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-noreload-message').text(data.msg);
                        $('#success-noreload-dialog').modal('show');
                        setTimeout(function() {
                            $(location).attr('href', '/user/invoice/' + data.invoice_id + '/view');
                        }, 1500);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>

{include file='user/footer.tpl'}
