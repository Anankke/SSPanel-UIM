{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">工单回复</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">你可以在这里查看历史消息并添加回复</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            添加回复
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#add-reply" aria-label="Create new report">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
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
                        <div class="card-body">
                            <div class="h1 my-2 mb-3">#{$topic->tk_id} {$topic->title}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center my-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="divide-y">
                                {$count = '0'}
                                {$total = $discussions->count()}
                                {foreach $discussions as $discuss}
                                    <div>
                                        <div class="row">
                                            {if $discuss->user_id == $user->id}
                                                <div class="col-auto">
                                                    <span class="avatar">用户</span>
                                                </div>
                                            {else}
                                                <div class="col-auto">
                                                    <span class="avatar"
                                                        style="background-image: url(/theme/tabler/static/admin.png)"></span>
                                                </div>
                                            {/if}
                                            <div class="col">
                                                <div>
                                                    {nl2br($discuss->content)}
                                                </div>
                                                <div class="text-muted my-1">{$discuss->created_at}</div>
                                            </div>
                                            <!-- 标记最新回复 -->
                                            {$count = $count + 1}
                                            {if $count == $total}
                                                <div class="col-auto align-self-center">
                                                    <div class="badge bg-primary"></div>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                    {if $count == $total && $topic->getOriginal('closed_by') != null}
                                        <div>
                                            <div class="row">
                                                <div class="col-auto">
                                                    <span class="avatar"
                                                        style="background-image: url(/theme/tabler/static/warning.png)"></span>
                                                </div>
                                                <div class="col">
                                                    <div>
                                                        此主题帖已关闭，如有需要请创建新工单
                                                    </div>
                                                    <div class="text-muted my-1">
                                                        {date('Y-m-d H:i:s', $topic->getOriginal('closed_at'))}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="add-reply" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">添加回复</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea id="reply-content" class="form-control" rows="10" placeholder="请输入回复内容"></textarea>
                    </div>
                    <p>* 上传图片有助于帮助解决问题，请使用图床上传。可以前往
                        <a target="view_window" href="https://www.imgurl.org/">
                            imgurl.org
                        </a>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="reply" type="button" class="btn btn-primary" data-bs-dismiss="modal">回复</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#reply").click(function() {
            $.ajax({
                url: "/user/ticket/{$topic->tk_id}",
                type: 'PUT',
                dataType: "json",
                data: {
                    content: $('#reply-content').val()
                },
                success: function(data) {
                    if (data.ret == 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#success-confirm").click(function() {
            location.reload();
        });
    </script>
{include file='user/tabler_footer.tpl'}