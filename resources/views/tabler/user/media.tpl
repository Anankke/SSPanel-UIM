{include file='user/header.tpl'}
<script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>

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
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap datatable">
                                <thead>
                                    <tr>
                                        <th>节点</th>
                                        {foreach $results['0']['unlock_item'] as $key => $value}
                                            <th>{$key}</th>
                                        {/foreach}
                                        <th>更新时间</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $results as $result}
                                        <tr>
                                            <td>{$result['node_name']}</td>
                                            <td><span class="">{$result['unlock_item']['YouTube']}</span></td>
                                            <td><span class="">{$result['unlock_item']['Netflix']}</span></td>
                                            <td><span class="">{$result['unlock_item']['Disney+']}</span></td>
                                            <td><span class="">{$result['unlock_item']['OpenAI']}</span></td>                                                                                      
                                            <td><span class="">{$result['unlock_item']['BBC']}</span></td>
                                            <td><span class="">{$result['unlock_item']['Abema']}</span></td>
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
<script>
$(document).ready(function(){
    $('td > span:contains("Yes")').addClass('badge bg-green-lt');
    $('td > span:contains("No")').addClass('badge bg-red-lt');
    $('td > span:contains("Only")').addClass('badge bg-purple-lt');
    $('td > span:contains("Failed")').addClass('badge bg-yellow-lt');
});
</script> 
{include file='user/footer.tpl'}