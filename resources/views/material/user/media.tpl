{include file='user/main.tpl'}

<style>
    .table>thead th {
        font-size: 14px;
    }
</style>

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">流媒体解锁</h1>
        </div>
    </div>
    <div class="container">
        <div class="row">
            {if $results != null}
                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <p>你可以在这里查看节点的流媒体解锁情况</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-table">
                                    <div class="table-responsive">
                                        <table class="table">
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {else}
                <div class="col-lg-12 col-md-12">
                    <div class="card margin-bottom-no">
                        <div class="card-main">
                            <div class="card-inner">
                                <div class="card-inner">
                                    <p>管理员尚未启用此功能</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
</main>

<script>
    // https://article.docway.net/it/details/60f8c4b1791936603cddc187    
    $("td:contains('Yes')").css("color", "green");
    $("td:contains('No')").css("color", "red");
    $("td:contains('Unknow')").css("color", "orange");
    $("td:contains('仅限自制')").css("color", "purple");
</script>

{include file='user/footer.tpl'}
