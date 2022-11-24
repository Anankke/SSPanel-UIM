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
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#account-recharge">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coin" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                                <path
                                    d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                </path>
                                <path d="M12 6v2m0 8v2"></path>
                            </svg>
                            充值余额
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#account-recharge">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coin" width="24"
                                height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <circle cx="12" cy="12" r="9"></circle>
                                <path
                                    d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                </path>
                                <path d="M12 6v2m0 8v2"></path>
                            </svg>
                        </a>
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
                                            ，大约还有 <code>{$diff}</code> 天到期
                                        {/if}
                                    </p>
                                    {if $config['user_product_page_custom'] == true}
                                        {$config['user_product_page_custom_text']}
                                    {/if}
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
                            {foreach $product_tab_lists as $product_tab}
                                <li class="nav-item">
                                    <a href="#product-{$product_tab['type']}"
                                        class="nav-link {if $product_tab['type'] == 'tatp'}active{/if}"
                                        data-bs-toggle="tab">
                                        <i class="ti ti-{$product_tab['icon']}"></i>&nbsp;{$product_tab['name']}
                                    </a>
                                </li>
                            {/foreach}
                        </ul>
                        <div class="card-body">
                            <div class="tab-content">
                                {foreach $product_lists as $key => $value}
                                    <div class="tab-pane show {if $key == 'tatp'}active{/if}" id="product-{$key}">
                                        <div class="row">
                                            {if $count[$key] != '0'}
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
                                                                            <span class="pop form-help" data-bs-toggle="popover"
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
                                            {else}
                                                <div class="card-body">
                                                    <p>此分类下没有商品</p>
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

    <div class="modal modal-blur fade" id="account-recharge" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">账户充值</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="color: red;">温馨提示：新的商店系统已支持直接下单扫码付款，无需先充值再购买。此功能是提供给在支付账单时遇到账单金额低于支付限额的用户</p>
                    <div class="form-group mb-3 row">
                        <label class="form-label col-2 col-form-label">金额</label>
                        <div class="col">
                            <input id="recharge_amount" type="text" class="form-control" placeholder="请输入充值金额">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="create-recharge-order" type="button" class="btn btn-primary"
                        data-bs-dismiss="modal">提交</button>
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

        $("#create-recharge-order").click(function() {
            $.ajax({
                url: '/user/charge',
                type: 'POST',
                dataType: "json",
                data: {
                    recharge_amount: $('#recharge_amount').val(),
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

        {literal}                     
            // https://zablog.me/2015/10/25/Popover/ 非常感谢
            $(document).ready(
                function() {
                    $(".pop").popover({placement:'left', trigger:'manual', delay: {show: 100, hide: 100}, html: true,
                    title: function() {
                        return $("#data-original-title").html();
                    },
                    content: function() {
                        return $("#data-content").html(); // 把content变成html
                    }
                });
            $('body').click(function(event) {
                var target = $(event.target); // 判断自己当前点击的内容
                if (!target.hasClass('popover') &&
                    !target.hasClass('pop') &&
                    !target.hasClass('popover-content') &&
                    !target.hasClass('popover-title') &&
                    !target.hasClass('arrow')) {
                    $('.pop').popover('hide'); // 当点击body的非弹出框相关的内容的时候，关闭所有popover
                }
            });
            $(".pop").click(function(event) {
            $('.pop').popover('hide'); // 当点击一个按钮的时候把其他的所有内容先关闭
            });
            }
            );
        {/literal}
    </script>
    
{include file='user/tabler_footer.tpl'}