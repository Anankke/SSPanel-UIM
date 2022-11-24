{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">账户已被封禁</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">你的账户功能已被停用，并且禁止访问用户中心</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <div class="card">
                        <div class="empty">
                            <div class="empty-img">
                                <i class="ti ti-circle-x icon mb-2 text-danger icon-lg" style="font-size:3.5rem;"></i>
                            </div>
                            {if $banned_reason == null}
                            <p class="empty-title">账户被系统封禁</p>
                            <p class="empty-subtitle text-muted">你的账户被系统自动封禁了，请联系管理员</p>
                            {else}
                            <p class="empty-title">以下是你被封禁的理由</p>
                            <p class="empty-subtitle text-muted">{$banned_reason}</p>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
{include file='user/tabler_footer.tpl'}