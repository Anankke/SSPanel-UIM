<div class="col-lg-9">
    <div class="card card-lg">
        <div class="card-body">
            <div class="markdown">
                <div>
                    <div class="d-flex mb-3">
                        <h1 class="m-0">订阅地址</h1>
                    </div>
                </div>
                <div class="mb-3">
                    <select id="client" class="form-select">
                        {foreach $subInfo as $client => $suburl}
                            {if $client != 'link' && !in_array($client, $config['docs_sub_hidden'])}
                                <option value="{$suburl}">{$client}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
                <button id="copy-button" class="btn btn-primary ms-auto">
                    复制订阅地址
                </button>
                <div class="my-3">
                    <div class="d-flex mb-3">
                        <h1 class="m-0">导航</h1>
                    </div>
                </div>
                <p>
                    根据使用设备选择连接客户端
                </p>
                <div class="mt-4">
                    <div class="row">
                        {foreach $groups as $key => $class}
                            <div class="col-sm-6">
                                <h3>{$key}</h3>
                                <ul class="list-unstyled">
                                    {foreach $class as $client}
                                        {if $client['switch'] == true}
                                        <li>
                                            - <a href="{$client['url']}">{$client['name']}</a>
                                        </li>
                                        {/if}
                                    {/foreach}
                                </ul>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-blur fade" id="success-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-success"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-green icon-lg" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <circle cx="12" cy="12" r="9" />
                    <path d="M9 12l2 2l4 -4" />
                </svg>
                <p id="success-message" class="text-muted">成功</p>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                好
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="fail-dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24"
                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 9v2m0 4v.01" />
                    <path
                        d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                </svg>
                <p id="fail-message" class="text-muted">失败</p>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                确认
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#copy-button").click(function() {
        client = $("#client").val();
        $("#copy-button").attr('data-clipboard-text', client);

        var clipboard = new ClipboardJS('.btn');
        clipboard.on('success', function(e) {
            $('#success-message').text('已复制到剪切板');
            $('#success-dialog').modal('show');
        });
    });
</script>