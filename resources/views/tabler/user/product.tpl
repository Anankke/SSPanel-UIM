{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">商品列表</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">在这里浏览商店商品并根据需要下单</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="m-0 my-2">
                                <div>
                                    <p>账户当前余额为：<code>{$user->money}</code> 元，剩余流量为：<code>{$user->unusedTraffic()}</code>
                                        {if time() > strtotime($user->expire_in)}
                                            ，你的账户已经过期了
                                        {else}
                                            {$diff = round((strtotime($user->expire_in) - time()) / 86400)}
                                            ，还有 <code>{$diff}</code> 天到期
                                        {/if}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="row">
                                    {foreach $products as $product}
                                    <div class="col-md-3 col-sm-12 my-3">
                                        <div class="card card-md">
                                            {if $product->type === 'tabp'}
                                            <div class="ribbon bg-blue">时间流量包</div>
                                            {elseif $product->type === 'time'}
                                            <div class="ribbon bg-blue">时间包</div>
                                            {else}
                                            <div class="ribbon bg-blue">流量包</div>
                                            {/if}
                                            <div class="card-body text-center">
                                                <div id="product-{$product->id}-name"
                                                    class="text-uppercase text-muted font-weight-medium">
                                                    {$product->name}</div>
                                                <div id="product-{$product->id}-price"
                                                    class="display-6 my-3"><p class="fw-bold">{$product->price}</p> <i class="ti ti-currency-yuan"></i>
                                                </div>
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item">
                                                        <div class="row align-items-center">
                                                            <div class="col text-truncate">
                                                                <div class="text-reset d-block">Lv. {$product->content->class}</div>
                                                                <div class="d-block text-muted text-truncate mt-n1">等级</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item">
                                                        <div class="row align-items-center">
                                                            <div class="col text-truncate">
                                                                <div class="text-reset d-block">{$product->content->class_time} 天</div>
                                                                <div class="d-block text-muted text-truncate mt-n1">等级时长</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item">
                                                        <div class="row align-items-center">
                                                            <div class="col text-truncate">
                                                                <div class="text-reset d-block">{$product->content->bandwidth} GB</div>
                                                                <div class="d-block text-muted text-truncate mt-n1">可用流量</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item">
                                                        <div class="row align-items-center">
                                                            <div class="col text-truncate">
                                                                {if $product->content->speed_limit < 0}
                                                                <div class="text-reset d-block">不限制</div>  
                                                                {else}
                                                                <div class="text-reset d-block">{$product->content->speed_limit} Mbps</div>
                                                                {/if}
                                                                <div class="d-block text-muted text-truncate mt-n1">连接速度</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item">
                                                        <div class="row align-items-center">
                                                            <div class="col text-truncate">
                                                                {if $product->content->ip_limit < 0}
                                                                <div class="text-reset d-block">不限制</div>
                                                                {else}
                                                                <div class="text-reset d-block">{$product->content->ip_limit}</div>
                                                                {/if}
                                                                <div class="d-block text-muted text-truncate mt-n1">同时连接 IP 数</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-2">
                                                    {if $product->stock === -1 || $product->stock > 0}
                                                    <div class="col">
                                                        <button onclick="buy('{$product->id}')" href="#"
                                                            class="btn btn-primary w-100 my-3">购买</button>
                                                    </div>
                                                    {else}
                                                    <div class="col">
                                                        <button href="#" class="btn btn-primary w-100 my-3"
                                                            disabled>告罄</button>
                                                    </div>
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="product-buy-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">确认订单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <span>商品名称：</span>
                        <span id="product-buy-name" style="float: right"></span>
                    </div>
                    <div class="mb-3">
                        <span>商品售价：</span>
                        <span id="product-buy-price" style="float: right"></span>
                    </div>
                    <div class="mb-3">
                        <span>折扣：</span>
                        <span id="product-buy-discount" style="float: right">0.00</span>
                    </div>
                    <hr />
                    <div class="mb-3">
                        <span>合计：</span>
                        <span id="product-buy-total" style="float: right">0</span>
                    </div>
                    <div class="mb-3">
                        <div class="input-group mb-2">
                            <input id="coupon" type="text" class="form-control" placeholder="填写优惠码，没有请留空">
                            <button id="verify-coupon" class="btn" type="button">检查</button>
                        </div>
                    </div>
                    <p id="valid-msg"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="create-order" type="button" class="btn btn-primary"
                        data-bs-dismiss="modal">创建订单</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function buy(product_id) {
            order_product_id = product_id;
            product_buy_name = $('#product-' + product_id + '-name').text();
            product_buy_price = $('#product-' + product_id + '-price').text();

            $('#product-buy-dialog').modal('show');
            $('#product-buy-name').text(product_buy_name);
            $('#product-buy-price').text((product_buy_price * 1).toFixed(2));
            $('#product-buy-total').text((product_buy_price * 1).toFixed(2));
        }

        $("#verify-coupon").click(function() {
            $.ajax({
                url: '/user/coupon',
                type: 'POST',
                dataType: "json",
                data: {
                    coupon: $('#coupon').val(),
                    product_id: order_product_id
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#product-buy-discount').text(data.discount);
                        $('#product-buy-total').text(data.buy_price);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#product-buy-dialog').modal('hide');
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#create-order").click(function() {
            $.ajax({
                url: '/user/order',
                type: 'POST',
                dataType: "json",
                data: {
                    coupon: $('#coupon').val(),
                    product_id: order_product_id
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text('正在准备您的订单');
                        $('#success-dialog').modal('show');
                        setTimeout(function() {
                            $(location).attr('href', '/user/order/' + data.order_id);
                        }, 1500);
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#product-buy-dialog').modal('hide');
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>
    
{include file='user/tabler_footer.tpl'}