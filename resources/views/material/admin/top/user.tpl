{include file='admin/tabler_admin_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">{$date}</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">浏览站点用户流量用量排行榜</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="/admin/top/user/{$previous_day}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="5" y1="12" x2="11" y2="18"></line>
                                <line x1="5" y1="12" x2="11" y2="6"></line>
                            </svg>
                            上一日
                        </a>
                        <a href="/admin/top/user/{$previous_day}" class="btn btn-primary d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="5" y1="12" x2="11" y2="18"></line>
                                <line x1="5" y1="12" x2="11" y2="6"></line>
                            </svg>
                        </a>
                        <a href="/admin/top/user/{$next_day}" class="btn btn-primary d-none d-sm-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-right"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="13" y1="18" x2="19" y2="12"></line>
                                <line x1="13" y1="6" x2="19" y2="12"></line>
                            </svg>
                            下一日
                        </a>
                        <a href="/admin/top/user/{$next_day}" class="btn btn-primary d-sm-none btn-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-right"
                                width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <line x1="13" y1="18" x2="19" y2="12"></line>
                                <line x1="13" y1="6" x2="19" y2="12"></line>
                            </svg>
                        </a>
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
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>用户编号</th>
                                        <th>流量用量</th>
                                        <th>记录时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {$count = 1}
                                    {foreach $logs as $log}
                                        <tr>
                                            <td>{$count++}</td>
                                            <td>{$log->user_id}</td>
                                            <td>{sprintf("%.2f", round($log->value, 2))} GB</td>
                                            <td>{date('Y-m-d H:i:s', $log->created_at)}</td>
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
{include file='admin/tabler_admin_footer.tpl'}