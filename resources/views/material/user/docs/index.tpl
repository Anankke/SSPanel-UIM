<div class="col-lg-9">
    <div class="card card-lg">
        <div class="card-body">
            <div class="markdown">
                <div>
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
                                        <li>
                                            - <a href="{$client['url']}">{$client['name']}</a>
                                        </li>
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