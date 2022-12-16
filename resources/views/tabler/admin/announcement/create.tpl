{include file='admin/tabler_header.tpl'}

<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/@tabler/core@latest/dist/libs/tinymce/skins/ui/oxide/skin.min.css">
<script src="//cdn.jsdelivr.net/npm/@tabler/core@latest/dist/libs/tinymce/tinymce.min.js"></script>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">创建公告</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">创建站点公告</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a id="create-ann" href="#" class="btn btn-primary">
                            <i class="icon ti ti-device-floppy"></i>
                            保存
                        </a>
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
                        <form method="post">
                            <textarea id="tinymce"></textarea>
                        </form>
                    </div>
                    <div class="mb-3">
                        <label class="form-label col-3 col-form-label">公告通知的用户等级，0为不分级</label>
                        <div class="col">
                            <input id="email_notify_class" type="text" class="form-control" value=""></input>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="divide-y">
                            <div>
                                <label class="row">
                                    <span class="col">发送邮件通知</span>
                                    <span class="col-auto">
                                        <label class="form-check form-check-single form-switch">
                                            <input id="email_notify" class="form-check-input" type="checkbox"
                                                checked="">
                                        </label>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>              
        </div>
    </div>
</div>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        let options = {
            selector: '#tinymce',
            height: 300,
            menubar: false,
            statusbar: false,
            plugins: 
              'advlist autolink lists link image charmap preview anchor ' +
              'searchreplace visualblocks code fullscreen ' +
              'insertdatetime media table code help wordcount',
            toolbar: 'undo redo | formatselect | ' +
              'bold italic backcolor link | blocks | alignleft aligncenter ' +
              'alignright alignjustify | bullist numlist outdent indent | ' +
              'removeformat',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;font-size:   14px; -webkit-font-smoothing: antialiased; }',
            {if $user->is_dark_mode}
            skin: 'oxide-dark',
            content_css: 'dark',
            {/if}
        }
        tinyMCE.init(options);
    })
    // @formatter:on

    $("#create-ann").click(function() {
        $.ajax({
            url: '/admin/announcement',
            type: 'POST',
            dataType: "json",
            data: {
                {foreach $update_field as $key}
                {$key}: $('#{$key}').val(),
                {/foreach}
                email_notify: $("#email_notify").is(":checked"),
                content: tinyMCE.activeEditor.getContent(),
            },
            success: function(data) {
                if (data.ret == 1) {
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

{include file='admin/tabler_footer.tpl'}
