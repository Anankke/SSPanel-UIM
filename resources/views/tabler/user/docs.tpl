{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <!-- Page title -->
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        <span class="home-title">文档中心</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">在这里查看安装和使用教程</span>
                    </div>
                </div>
                {if $config['enable_ticket'] == true}
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="/user/ticket" class="btn btn-primary d-none d-sm-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                创建工单
                            </a>
                            <a href="/user/ticket" class="btn btn-primary d-sm-none btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row gx-lg-5">
                <div class="d-none d-lg-block col-lg-3 my-5">
                    <ul class="nav nav-pills nav-vertical">
                        <li class="nav-item">
                            <a href="/user/docs/index" class="nav-link {if strtolower($client) == 'index'}active{/if}">
                                前言
                            </a>
                        </li>
                        <!--    客户端集合  客户端系统  客户端-->
                        {foreach $groups as $key => $class}
                            <li class="nav-item">
                                <a href="#{$key}" class="nav-link" data-bs-toggle="collapse" aria-expanded="false">
                                    {$key}
                                    <span class="nav-link-toggle"></span>
                                </a>
                                <ul class="nav nav-pills collapse" id="{$key}">
                                    {foreach $class as $item}
                                        {if $item['switch'] == true}
                                            <li class="nav-item">
                                                {$app_name = explode('-', $client)}
                                                <a href="{$item['url']}"
                                                    class="nav-link {if strtolower($app_name['0']) == strtolower($item['name'])}active{/if}">
                                                    {$item['name']}
                                                </a>
                                            </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </li>
                        {/foreach}
                    </ul>
                </div>
                {include file="user/docs/{$client}.tpl"}
            </div>
        </div>
    </div>
{include file='user/tabler_footer.tpl'}