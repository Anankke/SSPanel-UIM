{include file='admin/header.tpl'}

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">LLM</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">设置站点的大型语言模型服务</span>
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a id="save-setting" href="#" class="btn btn-primary">
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
            <div class="row row-deck row-cards">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs">
                                <li class="nav-item">
                                    <a href="#backend" class="nav-link active" data-bs-toggle="tab">设置</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#openai" class="nav-link" data-bs-toggle="tab">OpenAI</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#google-ai" class="nav-link" data-bs-toggle="tab">Google AI</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#vertex-ai" class="nav-link" data-bs-toggle="tab">Vertex AI</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#huggingface" class="nav-link" data-bs-toggle="tab">Hugging Face</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#cf-workers-ai" class="nav-link" data-bs-toggle="tab">Cloudflare Workers AI</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#anthropic" class="nav-link" data-bs-toggle="tab">Anthropic</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#aws-bedrock" class="nav-link" data-bs-toggle="tab">AWS Bedrock</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="tab-pane active show" id="backend">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Backend</label>
                                            <div class="col">
                                                <select id="llm_backend" class="col form-select"
                                                        value="{$settings['llm_backend']}">
                                                    <option value=""
                                                            {if $settings['llm_backend'] === ""}selected{/if}>
                                                        None
                                                    </option>
                                                    <option value="openai"
                                                            {if $settings['llm_backend'] === "openai"}selected{/if}>
                                                        OpenAI
                                                    </option>
                                                    <option value="google-ai"
                                                            {if $settings['llm_backend'] === "google-ai"}selected{/if}>
                                                        Google AI
                                                    </option>
                                                    <option value="vertex-ai"
                                                            {if $settings['llm_backend'] === "vertex-ai"}selected{/if}>
                                                        Vertex AI
                                                    </option>
                                                    <option value="huggingface"
                                                            {if $settings['llm_backend'] === "huggingface"}selected{/if}>
                                                        Hugging Face
                                                    </option>
                                                    <option value="cf-workers-ai"
                                                            {if $settings['llm_backend'] === "cf-workers-ai"}selected{/if}>
                                                        Cloudflare Workers AI
                                                    </option>
                                                    <option value="anthropic"
                                                            {if $settings['llm_backend'] === "anthropic"}selected{/if}>
                                                        Anthropic
                                                    </option>
                                                    <option value="aws-bedrock"
                                                            {if $settings['llm_backend'] === "aws-bedrock"}selected{/if}>
                                                        AWS Bedrock
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="openai">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">API Key</label>
                                            <div class="col">
                                                <input id="openai_api_key" type="text" class="form-control"
                                                       value="{$settings['openai_api_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Model ID</label>
                                            <div class="col">
                                                <input id="openai_model_id" type="text" class="form-control"
                                                       value="{$settings['openai_model_id']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="google-ai">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">API Key</label>
                                            <div class="col">
                                                <input id="google_ai_api_key" type="text" class="form-control"
                                                       value="{$settings['google_ai_api_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Model ID</label>
                                            <div class="col">
                                                <input id="google_ai_model_id" type="text" class="form-control"
                                                       value="{$settings['google_ai_model_id']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="vertex-ai">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Token</label>
                                            <div class="col">
                                                <input id="vertex_ai_access_token" type="text" class="form-control"
                                                       value="{$settings['vertex_ai_access_token']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Location</label>
                                            <div class="col">
                                                <input id="vertex_ai_location" type="text" class="form-control"
                                                       value="{$settings['vertex_ai_location']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Project ID</label>
                                            <div class="col">
                                                <input id="vertex_ai_project_id" type="text" class="form-control"
                                                       value="{$settings['vertex_ai_project_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Model ID</label>
                                            <div class="col">
                                                <input id="vertex_ai_model_id" type="text" class="form-control"
                                                       value="{$settings['vertex_ai_model_id']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="huggingface">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">API Key</label>
                                            <div class="col">
                                                <input id="huggingface_api_key" type="text" class="form-control"
                                                       value="{$settings['huggingface_api_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Endpoint URL</label>
                                            <div class="col">
                                                <input id="huggingface_endpoint_url" type="text" class="form-control"
                                                       value="{$settings['huggingface_endpoint_url']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="cf-workers-ai">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Account ID</label>
                                            <div class="col">
                                                <input id="cf_workers_ai_account_id" type="text" class="form-control"
                                                       value="{$settings['cf_workers_ai_account_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">API Token</label>
                                            <div class="col">
                                                <input id="cf_workers_ai_api_token" type="text" class="form-control"
                                                       value="{$settings['cf_workers_ai_api_token']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Model ID</label>
                                            <div class="col">
                                                <input id="cf_workers_ai_model_id" type="text" class="form-control"
                                                       value="{$settings['cf_workers_ai_model_id']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="anthropic">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">API Key</label>
                                            <div class="col">
                                                <input id="anthropic_api_key" type="text" class="form-control"
                                                       value="{$settings['anthropic_api_key']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Model ID</label>
                                            <div class="col">
                                                <input id="anthropic_model_id" type="text" class="form-control"
                                                       value="{$settings['anthropic_model_id']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="aws-bedrock">
                                    <div class="card-body">
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Key ID</label>
                                            <div class="col">
                                                <input id="aws_bedrock_access_key_id" type="text" class="form-control"
                                                       value="{$settings['aws_bedrock_access_key_id']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Access Key Secret</label>
                                            <div class="col">
                                                <input id="aws_bedrock_access_key_secret" type="text" class="form-control"
                                                       value="{$settings['aws_bedrock_access_key_secret']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Region</label>
                                            <div class="col">
                                                <input id="aws_bedrock_region" type="text" class="form-control"
                                                       value="{$settings['aws_bedrock_region']}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3 row">
                                            <label class="form-label col-3 col-form-label">Model ID</label>
                                            <div class="col">
                                                <input id="aws_bedrock_model_id" type="text" class="form-control"
                                                       value="{$settings['aws_bedrock_model_id']}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $("#save-setting").click(function () {
                $.ajax({
                    url: '/admin/setting/llm',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        {foreach $update_field as $key}
                        {$key}: $('#{$key}').val(),
                        {/foreach}
                    },
                    success: function (data) {
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
