{include file='user/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">商品列表</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">
                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>商品不可叠加，新购商品会覆盖旧商品的效果。</p>
                            <p>购买新套餐时，如果未关闭旧套餐自动续费，则旧套餐的自动续费依然生效。</p>
                            <p><i class="icon icon-lg">attach_money</i>当前余额：<font color="#399AF2" size="5">{$user->money}</font> 元</p>
                        </div>
                    </div>
                </div>
                <div class="ui-switch">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner ui-switch-inner">
                                <div class="switch-btn" id="switch-cards">
                                    <a href="#" onclick="return false">
                                        <i class="mdui-icon material-icons">apps</i>
                                    </a>
                                </div>
                                <div class="switch-btn" id="switch-table">
                                    <a href="#" onclick="return false">
                                        <i class="mdui-icon material-icons">dehaze</i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-main">
                            <div class="dropdown btn-group">
                                <a href="javascript:void(0);" type="button" class="btn btn-dropdown-toggle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  选择商品类型 <span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                  <li class="order-type"><a href="javascript:void(0)" id="orders">套餐购买</a></li>
                                  <li class="order-type"><a href="javascript:void(0)" id="traffice-packages">叠加流量包</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <div data-areatype="orders">
                <div class="shop-flex">
                    {foreach $shops as $shop}
                    {if $shop->traffic_package() == 0}
                        <div class="card">
                            <div class="card-main">
                                <div class="shop-name">{$shop->name}</div>
                                <div class="shop-price">{$shop->price}</div>
                                <div class="shop-tat">
                                    <span>{$shop->bandwidth()}</span> / <span>{$shop->class_expire()}</span>
                                </div>
                                <div class="shop-cube">
                                    <div>
                                        <div class="cube-detail">
                                            <span>Lv.</span>{$shop->user_class()}
                                        </div>
                                        <div class="cube-title">
                                            VIP
                                        </div>
                                    </div>
                                    <div>
                                        <div class="cube-detail">
                                            {if {$shop->connector()} == '0' }无限制{else}{$shop->connector()}
                                                <span> 个</span>
                                            {/if}
                                        </div>
                                        <div class="cube-title">
                                            客户端数量
                                        </div>
                                    </div>
                                    <div>
                                        <div class="cube-detail">
                                            {if {$shop->speedlimit()} == '0' }无限制{else}{$shop->speedlimit()}
                                                <span> Mbps</span>
                                            {/if}
                                        </div>
                                        <div class="cube-title">
                                            端口速率
                                        </div>
                                    </div>
                                </div>
                                <div class="shop-content">
                                    <div class="shop-content-left">账号有效期:</div>
                                    <div class="shop-content-right">{$shop->expire()}<span>天</span></div>
                                    <div class="shop-content-left">重置周期:</div>
                                    <div class="shop-content-right">{if {$shop->reset()} == '0' }N / A{else}{$shop->reset_exp()}
                                            <span>天</span>
                                        {/if}</div>
                                    <div class="shop-content-left">重置频率:</div>
                                    <div class="shop-content-right">{if {$shop->reset()} == '0' }N / A{else}{$shop->reset_value()}
                                            <span>G</span>
                                            / {$shop->reset()}
                                            <span>天</span>
                                        {/if}</div>
                                </div>
                                <div class="shop-content-extra">
                                    {foreach $shop->content_extra() as $service}
                                        <div><span class="icon">{$service[0]}</span> {$service[1]}</div>
                                    {/foreach}
                                </div>
                                <a class="btn btn-brand-accent shop-btn" href="javascript:void(0);"
                                   onClick="buy('{$shop->id}',{$shop->auto_renew})">购买</a>
                            </div>
                        </div>
                    {/if}
                    {/foreach}
                    <div class="flex-fix3"></div>
                    <div class="flex-fix4"></div>
                </div>
                <div class="shop-table">
                    {foreach $shops as $shop}
                    {if $shop->traffic_package() == 0}
                        <div class="shop-gridarea">
                            <div class="card">
                                <div>
                                    <div class="shop-name"><span>{$shop->name}</span></div>
                                    <div class="card-tag tag-gold">VIP {$shop->user_class()}</div>
                                    <div class="card-tag tag-orange">¥ {$shop->price}</div>
                                    <div class="card-tag tag-cyan">{$shop->bandwidth()} G</div>
                                    <div class="card-tag tag-blue">{$shop->class_expire()} 天</div>
                                </div>
                                <div>
                                    <i class="material-icons">expand_more</i>
                                </div>
                            </div>
                            <a class="btn btn-brand-accent shop-btn" href="javascript:void(0);"
                               onClick="buy('{$shop->id}',{$shop->auto_renew})">购买</a>
                            <div class="shop-drop dropdown-area">
                                <div class="card-tag tag-black">账号有效期</div>
                                <div class="card-tag tag-blue">{$shop->expire()} 天</div>
                                {if {$shop->reset()} == '0' }
                                    <div class="card-tag tag-black">重置周期</div>
                                    <div class="card-tag tag-blue">N/A</div>
                                {else}
                                    <div class="card-tag tag-black">重置周期</div>
                                    <div class="card-tag tag-blue">{$shop->reset_exp()} 天</div>
                                    <div class="card-tag tag-black">重置频率</div>
                                    <div class="card-tag tag-blue">{$shop->reset_value()}G/{$shop->reset()}天</div>
                                {/if}
                                {if {$shop->speedlimit()} == '0' }
                                    <div class="card-tag tag-black">端口速率</div>
                                    <div class="card-tag tag-blue">无限制</div>
                                {else}
                                    <div class="card-tag tag-black">端口限速</div>
                                    <div class="card-tag tag-blue">{$shop->speedlimit()} Mbps</div>
                                {/if}
                                {if {$shop->connector()} == '0' }
                                    <div class="card-tag tag-black">客户端数量</div>
                                    <div class="card-tag tag-blue">无限制</div>
                                {else}
                                    <div class="card-tag tag-black">客户端限制</div>
                                    <div class="card-tag tag-blue">{$shop->connector()} 个</div>
                                {/if}
                            </div>
                        </div>
                    {/if}
                    {/foreach}
                </div>
            </div>
            <div style="display: none;" data-areatype="trafficePackages">
                <div class="shop-table" style="display: flex">
                    {foreach $shops as $shop}
                    {if $shop->traffic_package() != 0}
                    <div class="shop-gridarea">
                        <div class="card">
                            <div>
                                <div class="shop-name"><span>{$shop->name}</span></div>
                                <div class="card-tag tag-orange">¥ {$shop->price}</div>
                                <div class="card-tag tag-cyan">{$shop->bandwidth()} G</div>
                            </div>
                            <div>
                                <i class="material-icons">expand_more</i>
                            </div>
                        </div>
                        <a class="btn btn-brand-accent shop-btn" href="javascript:void(0);"
                        onClick="buyTraffic('{$shop->id}')">购买</a>
                        <div class="shop-drop dropdown-area">
                            <div class="card-tag tag-black">流量包流量</div>
                            <div class="card-tag tag-blue">{$shop->bandwidth()} G</div>
                        </div>
                    </div>
                {/if}
                {/foreach}
                </div>
            </div>
                <div aria-hidden="true" class="modal modal-va-middle fade" id="coupon_modal" role="dialog"
                     tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-heading">
                                <a class="modal-close" data-dismiss="modal">×</a>
                                <h2 class="modal-title">您有优惠码吗？</h2>
                            </div>
                            <div class="modal-inner">
                                <div class="form-group form-group-label">
                                    <label class="floating-label" for="coupon">有的话，请在这里输入。没有的话，直接确定吧</label>
                                    <input class="form-control maxwidth-edit" id="coupon" type="text">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <p class="text-right">
                                    <button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal"
                                            id="coupon_input" type="button">确定
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal modal-va-middle fade" id="traffic_package_modal" role="dialog"
                     tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-heading">
                                <a class="modal-close" data-dismiss="modal">×</a>
                                <h2 class="modal-title">确认购买流量包吗？</h2>
                            </div>
                            <div class="modal-footer">
                                <p class="text-right">
                                    <button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal"
                                            id="traffic_package_confirm" type="button">确定
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div aria-hidden="true" class="modal modal-va-middle fade" id="order_modal" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-xs">
                        <div class="modal-content">
                            <div class="modal-heading">
                                <a class="modal-close" data-dismiss="modal">×</a>
                                <h2 class="modal-title">订单确认</h2>
                            </div>
                            <div class="modal-inner">
                                <p id="name">商品名称：</p>
                                <p id="credit">优惠额度：</p>
                                <p id="total">总金额：</p>
                                <div class="checkbox switch">
                                    <label for="disableothers">
                                        <input checked class="access-hide" id="disableothers" type="checkbox">
                                        <span class="switch-toggle"></span>关闭旧套餐自动续费
                                    </label>
                                </div>
                                <br/>
                                <div class="checkbox switch" id="autor">
                                    <label for="autorenew">
                                        <input checked class="access-hide" id="autorenew" type="checkbox">
                                        <span class="switch-toggle"></span>到期时自动续费
                                    </label>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <p class="text-right">
                                    <button class="btn btn-flat btn-brand waves-attach" data-dismiss="modal"
                                            id="order_input" type="button">确定
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                {include file='dialog.tpl'}
        </div>
    </div>
</main>

{include file='user/footer.tpl'}

<script>
    function buy(id, auto) {
        if (auto == 0) {
            document.getElementById('autor').style.display = "none";
        } else {
            document.getElementById('autor').style.display = "";
        }
        shop = id;
        $("#coupon_modal").modal();
    }
    let trafficPackageId;
    function buyTraffic(id) {
        trafficPackageId = id
        $("#traffic_package_modal").modal();
    }
    $('#traffic_package_confirm').click(function() {
        $.ajax({
            type: "POST",
            url: "buy_traffic_package",
            dataType: "json",
            data: {
                shop: trafficPackageId
            },
            success: (data) => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/user/shop'", {$config['jump_delay']});
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                        data.msg
                        } 发生了错误`;
            }
        })
    })
    ;(function () {
        //UI切换
        let elShopCard = $$.querySelectorAll(".shop-flex");
        let elShopTable = $$.querySelectorAll("[data-areatype=orders] .shop-table");
        let switchToCard = new UIswitch('switch-cards', elShopTable, elShopCard, 'flex', 'tempshop');
        switchToCard.listenSwitch();
        let switchToTable = new UIswitch('switch-table', elShopCard, elShopTable, 'flex', 'tempshop');
        switchToTable.listenSwitch();
        switchToCard.setDefault();
        switchToTable.setDefault();
        //手风琴
        let dropDownButton = $$.querySelectorAll('.shop-table .card');
        let dropDownArea = $$.querySelectorAll('.dropdown-area');
        let arrows = $$.querySelectorAll('.shop-table .card i');
        for (let i = 0; i < dropDownButton.length; i++) {
            rotatrArrow(dropDownButton[i], arrows[i]);
            custDropdown(dropDownButton[i], dropDownArea[i]);
        }
        //商品类型
        let orderType = "orders"
        let orders = $$.querySelectorAll('[data-areatype=orders]')
        let trafficePackages = $$.querySelectorAll('[data-areatype=trafficePackages]')
        let switchToOrders = new UIswitch('orders', trafficePackages, orders, 'flex', 'tempordertype');
        switchToOrders.listenSwitch();
        let switchToTrafficePackages = new UIswitch('traffice-packages', orders, trafficePackages, 'flex', 'tempordertype');
        switchToTrafficePackages.listenSwitch();
        switchToOrders.setDefault();
        switchToTrafficePackages.setDefault();
    })();
    $("#coupon_input").click(function () {
        $.ajax({
            type: "POST",
            url: "coupon_check",
            dataType: "json",
            data: {
                coupon: $$getValue('coupon'),
                shop
            },
            success: (data) => {
                if (data.ret) {
                    $$.getElementById('name').innerHTML = `商品名称：${
                            data.name
                            }`;
                    $$.getElementById('credit').innerHTML = `优惠额度：${
                            data.credit
                            }`;
                    $$.getElementById('total').innerHTML = `总金额：${
                            data.total
                            }`;
                    $("#order_modal").modal();
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                        data.msg
                        } 发生了错误`;
            }
        })
    });
    $("#order_input").click(function () {
        if (document.getElementById('autorenew').checked) {
            var autorenew = 1;
        } else {
            var autorenew = 0;
        }
        if (document.getElementById('disableothers').checked) {
            var disableothers = 1;
        } else {
            var disableothers = 0;
        }
        $.ajax({
            type: "POST",
            url: "buy",
            dataType: "json",
            data: {
                coupon: $$getValue('coupon'),
                shop,
                autorenew,
                disableothers
            },
            success: (data) => {
                if (data.ret) {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                    window.setTimeout("location.href='/user/shop'", {$config['jump_delay']});
                } else {
                    $("#result").modal();
                    $$.getElementById('msg').innerHTML = data.msg;
                }
            },
            error: (jqXHR) => {
                $("#result").modal();
                $$.getElementById('msg').innerHTML = `${
                        data.msg
                        } 发生了错误`;
            }
        })
    });
</script>