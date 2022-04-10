{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span style="font-size: 36px;">邀请注册</span>
                    </h2>
                    <div class="page-pretitle">
                        <span style="font-size: 12px;">查看邀请注册链接和邀请返利记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">邀请规则</h3>
                            <ul>
                                <li>对方在进行账户充值或购买套餐后，您可获得订单金额的 <code>{$config['code_payback'] * 100} %</code>
                                    作为返利</li>
                                <li>具体邀请返利规则请查看公告，或通过工单系统询问管理员</li>
                            </ul>
                            <p>您通过邀请好友获得的总返利为 <code>{$paybacks_sum}</code> 元</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title">邀请链接</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{include file='user/tabler_footer.tpl'}