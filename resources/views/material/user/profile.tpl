{include file='user/main.tpl'}


<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">我的账户</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    {if $config['enable_kill']===true}
                                        <div class="cardbtn-edit">
                                            <div class="card-heading">我的帐号</div>
                                            <div class="account-flex">
                                                <span>注销账号</span>
                                                <a class="btn btn-flat" href="kill">
                                                    <span class="icon">not_interested</span>&nbsp;
                                                </a>
                                            </div>
                                        </div>
                                    {/if}
                                    <dl class="dl-horizontal">
                                        <dt>用户名</dt>
                                        <dd>{$user->user_name}</dd>
                                        <dt>邮箱</dt>
                                        <dd>{$user->email}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-doubleinner">
                                    <p class="card-heading">当前生效中的套餐</p>
                                </div>

                                <div class="card-table">
                                    <div class="table-responsive table-user">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                              	<th>#ID</th>
                                              	<th>套餐名称</th>
                                              	<th>已用天数</th>
                                              	<th>下次流量重置时间</th>
                                              	<th>套餐过期时间</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                          	{foreach $boughts as $bought}
                                          		{if $bought->valid()}
                                                <tr>
                                                    <td>#{$bought->id}</td>
                                                    <td>{$bought->shop()->name}</td>
                                                    <td>{$bought->used_days()} 天</td>
                                                    <td>{$bought->reset_time()}</td>
                                                    <td>{$bought->exp_time()}</td>
                                                </tr>
                                          		{/if}
                                          	{/foreach}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-doubleinner">
                                    <p class="card-heading">最近五分钟使用IP</p>
                                    <p>请确认都为自己的IP，如有异常请及时修改连接密码。</p>
                                </div>
                                <div class="card-table">
                                    <div class="table-responsive table-user">
                                        <table class="table table-fixed">
                                            <tr>

                                                <th>IP</th>
                                                <th>归属地</th>
                                            </tr>
                                            {foreach $userip as $single=>$location}
                                                <tr>

                                                    <td>{$single}</td>
                                                    <td>{$location}</td>
                                                </tr>
                                            {/foreach}
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-6">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-doubleinner">
                                    <p class="card-heading">最近十次登录IP</p>
                                    <p>请确认都为自己的IP，如有异常请及时修改密码。</p>
                                </div>
                                <div class="card-table">
                                    <div class="table-responsive table-user">
                                        <table class="table table-fixed">
                                            <tr>

                                                <th>IP</th>
                                                <th>归属地</th>
                                            </tr>
                                            {foreach $userloginip as $single=>$location}
                                                <tr>

                                                    <td>{$single}</td>
                                                    <td>{$location}</td>
                                                </tr>
                                            {/foreach}
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</main>


{include file='user/footer.tpl'}
