{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">商品列表</span>
                    </h2>
                    <div class="page-pretitle">
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
                            <div class="h1 mb-3">说明</div>
                            <div class="d-flex mb-2">
                                <div>账户当前余额为：<code>{$user->money}</code> 元，剩余流量为：<code>{$user->unusedTraffic()}</code>
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
                        <ul class="nav nav-tabs nav-fill" data-bs-toggle="tabs">
                            {foreach $product_lists as $key => $value}
                                <li class="nav-item">
                                    <a href="#product-{$value}" class="nav-link {if $key == 'tatp'}active{/if}"
                                        data-bs-toggle="tab">
                                        <i class="ti ti-box"></i>&nbsp;{$value}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                {foreach $product_lists as $key => $value}
                                    <div class="tab-pane show {if $key == 'tatp'}active{/if}" id="product-{$value}">
                                        <div class="row">
                                            {foreach $products as $product}
                                                {if $product->type == $key}
                                                    <div class="col-md-3 col-sm-12 my-3">
                                                        <div class="card card-md">
                                                            <div class="card-body text-center">
                                                                <div id="product-{$product->id}-name"
                                                                    class="text-uppercase text-muted font-weight-medium">
                                                                    {$product->name}</div>
                                                                <div id="product-{$product->id}-price"
                                                                    class="display-6 fw-bold my-3">{$product->price / 100}
                                                                </div>
                                                                <ul class="list-unstyled lh-lg">
                                                                    {$product->html}
                                                                </ul>
                                                                <div class="row g-2">
                                                                    {if $product->stock - $product->sales > '0'}
                                                                        <div class="col">
                                                                            <button onclick="buy('{$product->id}')" href="#"
                                                                                class="btn btn-primary w-100">购买</button>
                                                                        </div>
                                                                    {else}
                                                                        <div class="col">
                                                                            <button href="#" class="btn btn-primary w-100"
                                                                                disabled>告罄</button>
                                                                        </div>
                                                                    {/if}
                                                                    <div class="col-auto align-self-center">
                                                                        <span class="form-help" data-bs-toggle="popover"
                                                                            data-bs-placement="top"
                                                                            data-bs-content="{$product->translate}"
                                                                            data-bs-html="true">?</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                            {/foreach}
                                            {if $products_count_tatp == '0'}
                                                <div class="card-body">
                                                    <p>空空如也</p>
                                                </div>
                                            {/if}
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
                            <button id="verify-coupon" class="btn" type="button">验证</button>
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

    <div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-success"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9 12l2 2l4 -4" />
                    </svg>
                    <p id="success-message" class="text-muted">成功</p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 9v2m0 4v.01" />
                        <path
                            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                    </svg>
                    <p id="fail-message" class="text-muted">失败</p>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                    确认
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="notice-dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-status bg-yellow"></div>
                <div class="modal-body text-center py-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-yellow icon-lg" width="24" height="24"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <circle cx="12" cy="12" r="9"></circle>
                        <line x1="12" y1="17" x2="12" y2="17.01"></line>
                        <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"></path>
                    </svg>
                    <p id="notice-message" class="text-muted">注意</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="notice-confirm" type="button" class="btn btn-yellow" data-bs-dismiss="modal">确认</button>
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
                url: '/user/coupon_check',
                type: 'POST',
                dataType: "json",
                data: {
                    coupon: $('#coupon').val(),
                    product_id: order_product_id
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#product-buy-discount').text('-' + ((1 - data.discount) *
                            product_buy_price).toFixed(2));
                        $('#product-buy-total').text((data.discount * product_buy_price).toFixed(
                            2));
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