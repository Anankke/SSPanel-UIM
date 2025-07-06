{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">订单 #{$order->id}</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">订单详情</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <a href="/user/invoice/{$invoice->id}/view" targer="_blank" class="btn btn-primary">
                            <i class="icon ti ti-file-dollar"></i>
                            查看账单
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">基本信息</h3>
                </div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">商品类型</div>
                            <div class="datagrid-content">{$order->product_type_text}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">商品名称</div>
                            <div class="datagrid-content">{$order->product_name}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">订单优惠码</div>
                            <div class="datagrid-content">{$order->coupon}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">订单金额</div>
                            <div class="datagrid-content">{$order->price}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">订单状态</div>
                            <div class="datagrid-content">{$order->status}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">创建时间</div>
                            <div class="datagrid-content">{$order->create_time}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">更新时间</div>
                            <div class="datagrid-content">{$order->update_time}</div>
                        </div>
                    </div>
                </div>
            </div>
            {if $order->type === 'topup'}
            <div class="card my-3">
                <div class="card-header">
                    <h3 class="card-title">商品内容</h3>
                </div>
                <div class="card-body">
                    <div class="datagrid">
                        {if $order->product_type === 'tabp' || $order->product_type === 'time'}
                            <div class="datagrid-item">
                                <div class="datagrid-title">商品时长 (天)</div>
                                <div class="datagrid-content">{$order->content->time}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">等级时长 (天)</div>
                                <div class="datagrid-content">{$order->content->class_time}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">等级</div>
                                <div class="datagrid-content">{$order->content->class}</div>
                            </div>
                        {/if}
                        {if $order->product_type === 'tabp' || $order->product_type === 'bandwidth'}
                            <div class="datagrid-item">
                                <div class="datagrid-title">可用流量 (GB)</div>
                                <div class="datagrid-content">{$order->content->bandwidth}</div>
                            </div>
                        {/if}
                        {if $order->product_type === 'tabp' || $order->product_type === 'time'}
                            <div class="datagrid-item">
                                <div class="datagrid-title">速率限制 (Mbps)</div>
                                <div class="datagrid-content">
                                    {if $order->content->ip_limit === '0'}
                                        不限制
                                    {else}
                                        {$order->content->speed_limit}
                                    {/if}
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">同时连接IP限制</div>
                                <div class="datagrid-content">
                                    {if $order->content->ip_limit === '0'}
                                        不限制
                                    {else}
                                        {$order->content->ip_limit}
                                    {/if}
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
            {/if}
            <div class="card my-3">
                <div class="card-header">
                    <h3 class="card-title">关联账单</h3>
                </div>
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">账单内容</div>
                            <div class="datagrid-content">
                                <div class="table-responsive">
                                    <table id="invoice_content_table" class="table table-vcenter card-table">
                                        <thead>
                                        <tr>
                                            <th>名称</th>
                                            <th>价格</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {foreach $invoice->content as $invoice_content}
                                            <tr>
                                                <td>{$invoice_content->name}</td>
                                                <td>{$invoice_content->price}</td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">账单金额</div>
                            <div class="datagrid-content">{$invoice->price}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">账单状态</div>
                            <div class="datagrid-content">{$invoice->status}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">创建时间</div>
                            <div class="datagrid-content">{$invoice->create_time}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">更新时间</div>
                            <div class="datagrid-content">{$invoice->update_time}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">支付时间</div>
                            <div class="datagrid-content">{$invoice->pay_time}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include file='user/footer.tpl'}
