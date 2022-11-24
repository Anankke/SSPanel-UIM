{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">流媒体解锁</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">你可以在这里查看节点的流媒体解锁情况</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-12">
                    <div class="card">
                        {if $results != null}
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th>节点</th>
                                            {foreach $results['0']['unlock_item'] as $key => $value}
                                                {if $key != 'BilibiliChinaMainland'}
                                                    {if $key == 'BilibiliHKMCTW'}
                                                        <th>港澳台B站</th>
                                                    {else if $key == 'BilibiliTW'}
                                                        <th>台湾B站</th>
                                                    {else}
                                                        <th>{$key}</th>
                                                    {/if}
                                                {/if}
                                            {/foreach}
                                            <th>更新时间</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach $results as $result}
                                            <tr>
                                                <td>{$result['node_name']}</td>
                                                <td>{$result['unlock_item']['YouTube']}</td>
                                                <td>{$result['unlock_item']['Netflix']}</td>
                                                <td>{$result['unlock_item']['DisneyPlus']}</td>
                                                <td>{$result['unlock_item']['BilibiliHKMCTW']}</td>
                                                <td>{$result['unlock_item']['BilibiliTW']}</td>
                                                <td>{$result['unlock_item']['MyTVSuper']}
                                                <td>{$result['unlock_item']['BBC']}</td>
                                                <td>{$result['unlock_item']['Abema']}</td>
                                                <td>{date('Y-m-d H:i:s', $result['created_at'])}</td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        {else}
                            <div class="card-body">
                                <p>管理员未启用此功能。</p>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // https://article.docway.net/it/details/60f8c4b1791936603cddc187    
        $("td:contains('Yes')").css("color", "green");
        $("td:contains('No')").css("color", "red");
        $("td:contains('Unknow')").css("color", "orange");
        $("td:contains('仅限自制')").css("color", "purple");
    </script>
    
{include file='user/tabler_footer.tpl'}