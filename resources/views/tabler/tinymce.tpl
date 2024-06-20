<script src="//cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/tinymce.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        tinyMCE.baseURL = '//cdnjs.cloudflare.com/ajax/libs/tinymce/7.0.1/';
        tinyMCE.suffix = '.min';
        tinyMCE.init({
            selector: '#tinymce',
            menubar: false,
            statusbar: false,
            plugins:
                'advlist autolink lists link image charmap preview anchor searchreplace visualblocks ' +
                'code insertdatetime media table',
            toolbar: 'undo redo | bold italic backcolor link | styles | fontsize | lineheight | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | removeformat',
            content_style: 'body { font-size: 14px; }',
            {if $user->is_dark_mode}
            skin: 'oxide-dark',
            content_css: 'dark',
            {/if}
        });
    })
</script>
