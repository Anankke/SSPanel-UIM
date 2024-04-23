{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">系统日志 #{$syslog->id}</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">日志详情</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-body">
                    <div class="datagrid">
                        <div class="datagrid-item">
                            <div class="datagrid-title">触发用户</div>
                            <div class="datagrid-content">{$syslog->user_id}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">触发IP</div>
                            <div class="datagrid-content">{$syslog->ip}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">日志内容</div>
                            <div class="datagrid-content">{$syslog->message}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">日志等级</div>
                            <div class="datagrid-content">{$syslog->level_text}</div>
                        </div>
                        <div class="datagrid-item">
                            <div class="datagrid-title">日志类别</div>
                            <div class="datagrid-content">{$syslog->channel_text}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {include file='admin/footer.tpl'}
