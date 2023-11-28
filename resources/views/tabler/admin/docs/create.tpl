{include file='admin/header.tpl'}

<script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.0/tinymce.min.js"></script>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">创建文档</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">创建站点文档</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <button href="#" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#generate-ai-content">
                            <i class="icon ti ti-robot"></i>
                            AI 文档生成
                        </button>
                        <button id="create-doc" href="#" class="btn btn-primary">
                            <i class="icon ti ti-device-floppy"></i>
                            保存
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label col-3 col-form-label">文档标题</label>
                        <div class="col">
                            <input id="title" type="text" class="form-control" value="">
                        </div>
                    </div>
                    <div class="mb-3">
                        <form method="post">
                            <textarea id="tinymce"></textarea>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="generate-ai-content" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">使用 LLM 自动生成文档</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input id="question" class="form-control" rows="12" placeholder="请输入文档生成问题"></input>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">取消</button>
                    <button id="generate" type="button" class="btn btn-primary" data-bs-dismiss="modal">生成</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let options = {
            selector: '#tinymce',
            menubar: false,
            statusbar: false,
            plugins:
                'advlist autolink lists link image charmap preview anchor ' +
                'searchreplace visualblocks code fullscreen ' +
                'insertdatetime media table wordcount',
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor link | blocks | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'image removeformat',
            image_title: false,
            image_description: false,
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;font-size:   14px; -webkit-font-smoothing: antialiased; }',
            {if $user->is_dark_mode}
            skin: 'oxide-dark',
            content_css: 'dark',
            {/if}
        }
        tinyMCE.init(options);
    })

    $("#generate").click(function () {
        $.ajax({
            url: "/admin/docs/generate",
            type: 'POST',
            dataType: "json",
            data: {
                question: $("#question").val(),
            },
            success: function (data) {
                if (data.ret === 1) {
                    $('#success-noreload-message').text(data.msg);
                    $('#success-noreload-dialog').modal('show');
                    tinyMCE.activeEditor.setContent(data.content);
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    });

    $("#create-doc").click(function () {
        $.ajax({
            url: '/admin/docs',
            type: 'POST',
            dataType: "json",
            data: {
                title: $("#title").val(),
                content: tinyMCE.get('tinymce').getContent(),
            },
            success: function (data) {
                if (data.ret === 1) {
                    $('#success-message').text(data.msg);
                    $('#success-dialog').modal('show');
                    window.setTimeout("location.href=top.document.referrer", {$config['jump_delay']});
                } else {
                    $('#fail-message').text(data.msg);
                    $('#fail-dialog').modal('show');
                }
            }
        })
    });
</script>

{include file='admin/footer.tpl'}
