{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title my-3">工单列表</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">你可以在这里联系管理员获取支持</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        <button href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create-ticket">
                            <i class="icon ti ti-plus"></i>
                            创建工单
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="row row-cards row-deck">
                        {if $tickets !== 0}
                            {foreach $tickets as $ticket}
                            <div class="col-md-4 col-sm-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="card-stamp">
                                            {if $ticket->status !== 'closed'}
                                            <div class="card-stamp-icon bg-yellow">
                                                <i class="ti ti-clock"></i>
                                            </div>
                                            {else}
                                            <div class="card-stamp-icon bg-green">
                                                <i class="ti ti-check"></i>
                                            </div>
                                            {/if}
                                        </div>
                                        <h3 class="card-title" style="font-size: 20px;">
                                            #{$ticket->id}
                                        </h3>
                                        <p class="text-secondary text-truncate" style="height: 100px;">
                                            {$ticket->title}
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex">
                                            <span class="status status-grey">{$ticket->status}</span>
                                            <span class="status status-grey">{$ticket->type}</span>
                                            <a href="/user/ticket/{$ticket->id}/view"
                                               class="btn btn-primary ms-auto">查看</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {/foreach}
                        {else}
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">没有任何工单</h3>
                            </div>
                            <div class="card-body">如需帮助，请点击右上角按钮开启新工单</div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="create-ticket" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">创建工单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <select id="ticket-type" class="form-select">
                            <option value="0">请选择工单类型</option>
                            <option value="howto">使用</option>
                            <option value="billing">财务</option>
                            <option value="account">账户</option>
                            <option value="other">其他</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input id="ticket-title" type="text" class="form-control" placeholder="请输入工单主题">
                    </div>
                    <div class="mb-3">
                        <textarea id="ticket-comment" class="form-control" rows="12" placeholder="请输入工单内容"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="create-ticket-button" class="btn btn-primary" data-bs-dismiss="modal"
                            hx-post="/user/ticket" hx-swap="none"
                            hx-vals='js:{
                            title: document.getElementById("ticket-title").value,
                            comment: document.getElementById("ticket-comment").value,
                            type: document.getElementById("ticket-type").value }'>
                        创建
                    </button>
                </div>
            </div>
        </div>
    </div>

{include file='user/footer.tpl'}
