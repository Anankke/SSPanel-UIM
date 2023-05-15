{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title my-3">工单回复</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">你可以在这里查看历史消息并添加回复</span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="btn-list">
                        {if $ticket->status !== 'closed'}
                        <button href="#" class="btn btn-red" data-bs-toggle="modal"
                            data-bs-target="#close_ticket_confirm_dialog">
                            <i class="icon ti ti-x"></i>
                            关闭
                        </button>
                        {/if}
                        <button id="add_ai_reply" href="#" class="btn btn-primary">
                            <i class="icon ti ti-robot"></i>
                            AI 回复
                        </button>
                        <button href="#" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#add-reply">
                            <i class="icon ti ti-plus"></i>
                            回复
                        </button>
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
                            <div class="h1 my-2 mb-3">#{$ticket->id} {$ticket->title}</div>
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
                                            {nl2br($comment->comment)}
                                        </div>
                                        <div class="text-secondary my-1">{$comment->commenter_name} 回复于 {$comment->datetime}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div>
                                            # {$comment->comment_id + 1}
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
                        <textarea id="reply-comment" class="form-control" rows="12" placeholder="请输入回复内容"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="reply" type="button" class="btn btn-primary" data-bs-dismiss="modal">回复</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="close_ticket_confirm_dialog" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">关闭工单</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p>
                            确认关闭工单？
                        <p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="confirm_close" type="button" class="btn btn-primary" data-bs-dismiss="modal">确认</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $("#reply").click(function() {
            $.ajax({
                url: "/admin/ticket/{$ticket->id}",
                type: 'PUT',
                dataType: "json",
                data: {
                    comment: $('#reply-comment').val()
                },
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#add_ai_reply").click(function() {
            $.ajax({
                url: "/admin/ticket/{$ticket->id}/ai",
                type: 'PUT',
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });

        $("#confirm_close").click(function() {
            $.ajax({
                url: "/admin/ticket/{$ticket->id}/close",
                type: 'PUT',
                dataType: "json",
                success: function(data) {
                    if (data.ret === 1) {
                        $('#success-message').text(data.msg);
                        $('#success-dialog').modal('show');
                    } else {
                        $('#fail-message').text(data.msg);
                        $('#fail-dialog').modal('show');
                    }
                }
            })
        });
    </script>

{include file='admin/footer.tpl'}