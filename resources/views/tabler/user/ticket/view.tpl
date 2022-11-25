{include file='user/tabler_header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">工单记录</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">你可以在这里查看工单消息并添加回复</span>
                    </div>
                </div>
                {if $ticket->status !== 'closed'}
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <i class="icon ti ti-plus"></i>
                            添加回复
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#add-reply" aria-label="Create new report">
                            <i class="icon ti ti-plus"></i>
                        </a>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="h1 my-2 mb-3">#{$ticket->id} {$ticket->title}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-deck my-3">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">工单状态</div>
                            </div>
                            <div class="h1 mb-3">{$ticket->status}</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="subheader">工单类型</div>
                            </div>
                            <div class="h1 mb-3">{$ticket->type}</div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="subheader">工单开启时间</div>
                                </div>
                                <div class="h1 mb-3">{$ticket->datetime}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center my-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="divide-y">
                                {foreach $comments as $comment}
                                <div>
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                {nl2br($comment['comment'])}
                                            </div>
                                            <div class="text-muted my-1">{$comment['commenter_name']} 回复于 {Tools::toDateTime($comment['datetime'])}</div>
                                        </div>
                                        <div class="col-auto">
                                            <div>
                                                # {$comment['comment_id'] + 1}
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                        <textarea id="reply-comment" class="form-control" rows="10" placeholder="请输入回复内容"></textarea>
                    </div>
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
                url: "/user/ticket/{$ticket->id}",
                type: 'PUT',
                dataType: "json",
                data: {
                    comment: $('#reply-comment').val()
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
