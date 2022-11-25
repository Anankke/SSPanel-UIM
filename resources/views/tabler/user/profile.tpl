{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">  
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">     
                    <h2 class="page-title">
                        <span class="home-title">账户信息</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">浏览最近的登录和使用记录</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户邮箱</div>
                            </div>
                            <div class="h1 mb-3">{$user->email}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户昵称</div>
                            </div>
                            <div class="h1 mb-3">{$user->user_name}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户注册时间</div>
                            </div>
                            <div class="h1 mb-3">{$user->reg_date}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">账户累计使用流量</div>
                            </div>
                            <div class="h1 mb-3">{round($user->transfer_total / 1073741824,2)} GB</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck my-3">
                <div class="col-md-6 com-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">最近登录记录</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter text-nowrap card-table">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>时间</th>
                                        <th>归属</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $userloginip as $login}
                                        <tr>
                                            <td>{$login->ip}</td>
                                            <td>{date('Y-m-d H:i:s', $login->datetime)}</td>
                                            <td>{Tools::getIpInfo($login->ip)}</td>
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
    
{include file='user/tabler_footer.tpl'}