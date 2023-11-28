{include file='user/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">邀请注册</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">查看邀请注册链接和邀请返利记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="row row-deck row-cards">
                        <div class="col-sm-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">邀请规则</h3>
                                    <ul>
                                        <li>邀请注册的用户在账单确认后，你可获得其账单金额的 <code>{$rebate_ratio_per}
                                                %</code>
                                            作为返利
                                        </li>
                                        <li>部分商品的返利比例可能不遵循上面的比例</li>
                                    </ul>
                                    <p>你目前通过邀请好友获得的总返利为 <code>{$paybacks_sum}</code> 元</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="card-title">邀请链接</h3>
                                    <input class="form-control" id="invite-url" value="{$invite_url}" disabled/>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex">
                                        <button id="reset-url" class="btn text-red btn-link"
                                                hx-post="/user/invite_reset" hx-swap="none">
                                            重置
                                        </button>
                                        <button data-clipboard-text="{$invite_url}"
                                           class="copy btn btn-primary ms-auto">复制</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 my-3">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">返利记录</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                <tr>
                                    <th>记录ID</th>
                                    <th>邀请用户ID</th>
                                    <th>邀请用户昵称</th>
                                    <th>返利金额</th>
                                    <th>返利时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $paybacks as $payback}
                                    <tr>
                                        <td>{$payback->id}</td>
                                        <td>{$payback->userid}</td>
                                        <td>{$payback->user_name}</td>
                                        <td>{$payback->ref_get} 元</td>
                                        <td>{$payback->datetime}</td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include file='user/footer.tpl'}
