{include file='user/tabler_header.tpl'}
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">使用问答</span>
                    </h2>
                    <div class="page-pretitle">
                        <span class="home-subtitle">常见的使用问题问答和其他相关内容介绍</span>
                    </div>
                </div>
                {if $config['enable_ticket'] == true}
                    <div class="col-auto ms-auto d-print-none">
                        <div class="btn-list">
                            <a href="/user/ticket" class="btn btn-primary d-none d-sm-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                创建工单
                            </a>
                            <a href="/user/ticket" class="btn btn-primary d-sm-none btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                                    height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="card card-lg">
                <div class="card-body">
                    <div class="space-y-4">
                        {foreach $faqs as $key => $value}
                            <div>
                                <h2 class="mb-3">{$key}</h2>
                                <div id="faq-{$key}" class="accordion" role="tablist" aria-multiselectable="true">
                                    {foreach $value as $faq}
                                        <div class="accordion-item">
                                            <div class="accordion-header" role="tab">
                                                <button class="accordion-button {if $faq['is_first'] == false}collapsed{/if}"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#{$faq['mark']}">{$faq['question']}</button>
                                            </div>
                                            <div id="{$faq['mark']}"
                                                class="accordion-collapse collapse {if $faq['is_first'] == true}show{/if}"
                                                role="tabpanel" data-bs-parent="#faq-{$key}">
                                                <div class="accordion-body pt-0">
                                                    <div>
                                                        <p>{$faq['answer']}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
{include file='user/tabler_footer.tpl'}