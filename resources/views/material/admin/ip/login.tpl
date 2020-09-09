{include file='admin/main.tpl'}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">最近登录记录</h1>
        </div>
    </div>
    <div class="container">
        <div class="col-lg-12 col-sm-12">
            <section class="content-inner margin-top-no">

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>这里是最近的登录记录。</p>
                            <p>显示表项: {include file='table/checkbox.tpl'}</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-main">
                        <div class="card-inner">
                            <p>IP 归属地查询（Powered by <a href="https://skk.moe" target="_blank">Sukka</a>）</p>

                            <iframe src="https://find-ip.skk.moe" allow="accelerometer; ambient-light-sensor; camera; encrypted-media; geolocation; gyroscope; hid; microphone; midi; payment; usb; vr" sandbox="allow-forms allow-modals allow-popups allow-presentation allow-same-origin allow-scripts" style="width:100%;height:100px;border:0;border-radius:4px;overflow:hidden"></iframe>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    {include file='table/table.tpl'}
                </div>


        </div>


    </div>
</main>

{include file='admin/footer.tpl'}

<script>
    {include file='table/js_1.tpl'}

    window.addEventListener('load', () => {
        {include file='table/js_2.tpl'}
    });
</script>
