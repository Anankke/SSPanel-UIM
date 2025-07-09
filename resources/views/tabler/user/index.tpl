{include file='user/header.tpl'}

<style>
/* Animation classes for collapsible sections */
.collapsible-section {
    transition: all 0.35s ease;
    overflow: hidden;
}

.collapsible-section.collapsing {
    opacity: 0.3;
    transform: scale(0.98);
}

.collapsible-section.expanded {
    opacity: 1;
    transform: scale(1);
}

/* Client item hover effects */
.client-item:hover {
    border-color: var(--tblr-primary) !important;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Copy button feedback */
.copy.copied {
    background-color: var(--tblr-success) !important;
    border-color: var(--tblr-success) !important;
}

.recommended-section {
    background: rgba(var(--tblr-primary-rgb), 0.1);
    border: 1px solid rgba(var(--tblr-primary-rgb), 0.2);
}

.client-item {
    transition: all 0.3s;
}

.client-item:hover {
    background: var(--tblr-bg-surface-secondary);
    transform: translateX(5px);
}

@media (max-width: 576px) {
    .client-item:hover {
        transform: none;
    }
    
    .client-item .btn-group-vertical {
        margin-top: 0.5rem;
    }
    
    .recommended-section h4 {
        font-size: 1rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .copy button {
        word-break: keep-all;
        white-space: nowrap;
    }
    
    /* Enhanced mobile button styles */
    .btn-group-vertical .btn {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        min-height: 44px; /* iOS recommended touch target */
    }
    
    .btn-group-vertical {
        gap: 0.5rem;
    }
    
    .client-item {
        padding: 1rem !important;
    }
    
    /* Recommended client cards on mobile */
    .recommended-section .card-body {
        padding: 1rem;
    }
    
    .recommended-section .btn-group {
        flex-wrap: wrap;
        gap: 0.5rem;
        justify-content: center;
    }
    
    .recommended-section .btn-group-vertical {
        align-items: stretch;
        width: 100%;
    }
    
    .recommended-section .btn {
        flex: 1 1 auto;
        min-width: 100px;
    }
}

/* 手风琴样式 */
.accordion-button:not(.collapsed) {
    background: var(--tblr-primary-lt);
    color: var(--tblr-primary);
}

/* 敏感信息模糊效果 */
.spoiler {
    filter: blur(5px);
    transition: filter 0.3s;
}

.spoiler:hover {
    filter: none;
}
</style>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <span class="home-title">用户中心</span>
                    </h2>
                    <div class="page-pretitle my-3">
                        <span class="home-subtitle">在这里查看账户信息和最新公告</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="row row-cards">
                        {foreach $info_cards as $card}
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-{$card.color} text-white avatar">
                                                <i class="ti {$card.icon} icon"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="font-weight-medium">
                                                {$card.title}
                                            </div>
                                            <div class="text-secondary">
                                                {$card.value}
                                            </div>
                                        </div>
                                        {if isset($card.action_url)}
                                        <div class="col-auto">
                                            <a href="{$card.action_url}" class="btn btn-primary btn-icon">
                                                <i class="ti ti-plus icon"></i>
                                            </a>
                                        </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/foreach}
                    </div>
                </div>
                
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">快速配置</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h4 class="mb-3">
                                    <i class="ti ti-link"></i> 您的专属订阅地址
                                </h4>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" value="{$UniversalSub}" readonly id="universal-sub-link">
                                    <button class="btn btn-primary copy" data-clipboard-text="{$UniversalSub}">
                                        <i class="ti ti-copy"></i> 复制
                                    </button>
                                </div>
                                <p class="text-muted mb-0">
                                    <small>此订阅地址适用于所有客户端，请妥善保管</small>
                                </p>
                            </div>

                            <div class="recommended-section p-3 bg-primary-lt rounded mb-3">
                                <h4 class="mb-3">
                                    <i class="ti ti-rocket"></i> 
                                    为您推荐的 <span id="detected-os" class="text-primary">Windows</span> 客户端
                                </h4>
                                <div class="row g-3" id="recommended-clients">
                                </div>
                            </div>

                            <div class="text-center">
                                <button class="btn btn-ghost-primary" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#all-platforms" aria-expanded="false">
                                    <i class="ti ti-package"></i> 
                                    查看其他平台客户端
                                    <i class="ti ti-chevron-down ms-1"></i>
                                </button>
                            </div>
                            
                            <div class="collapse mt-3" id="all-platforms">
                                <div class="accordion" id="platform-accordion">
                                </div>
                                
                                <div class="mt-3 p-3 bg-secondary-lt rounded">
                                    <h5 class="mb-2">高级订阅格式</h5>
                                    <div class="small text-muted mb-2">如果您需要特定格式的订阅链接：</div>
                                    <div class="btn-group btn-group-sm flex-wrap">
                                        <button class="btn btn-outline-secondary copy" data-clipboard-text="{$UniversalSub}/json">
                                            JSON 格式
                                        </button>
                                        <button class="btn btn-outline-secondary copy" data-clipboard-text="{$UniversalSub}/v2rayjson">
                                            V2Ray JSON
                                        </button>
                                        {if $public_setting['enable_ss_sub']}
                                        <button class="btn btn-outline-secondary copy" data-clipboard-text="{$UniversalSub}/sip008">
                                            SIP008
                                        </button>
                                        <button class="btn btn-outline-secondary copy" data-clipboard-text="{$UniversalSub}/ss">
                                            Shadowsocks
                                        </button>
                                        {/if}
                                        {if $public_setting['enable_v2_sub']}
                                        <button class="btn btn-outline-secondary copy" data-clipboard-text="{$UniversalSub}/v2ray">
                                            V2Ray
                                        </button>
                                        {/if}
                                        {if $public_setting['enable_trojan_sub']}
                                        <button class="btn btn-outline-secondary copy" data-clipboard-text="{$UniversalSub}/trojan">
                                            Trojan
                                        </button>
                                        {/if}
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <button class="btn btn-ghost-secondary w-100" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#connection-info" aria-expanded="false">
                                        <i class="ti ti-info-circle"></i> 
                                        查看连接信息
                                        <i class="ti ti-chevron-down ms-1"></i>
                                    </button>
                                    <div class="collapse mt-2" id="connection-info">
                                        <div class="p-3 bg-light rounded">
                                            <div class="table-responsive">
                                                <table class="table table-sm mb-0">
                                                    <tbody>
                                                    <tr>
                                                        <td class="text-muted" style="width: 100px;">端口</td>
                                                        <td><code>{$user->port}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">连接密码</td>
                                                        <td><code class="spoiler">{$user->passwd}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">UUID</td>
                                                        <td><code class="spoiler" style="font-size: 0.8em;">{$user->uuid}</code></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">加密方式</td>
                                                        <td><code>{$user->method}</code></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-sm-12">
                    <div class="vstack">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">流量用量</h3>
                                <div class="progress progress-separated mb-3">
                                    {if $user->LastusedTrafficPercent() < '1'}
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 1%"></div>
                                    {else}
                                    <div class="progress-bar bg-primary" role="progressbar"
                                         style="width: {$user->LastusedTrafficPercent()}%">
                                    </div>
                                    {/if}
                                    {if $user->TodayusedTrafficPercent() < '1'}
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 1%"></div>
                                    {else}
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {$user->TodayusedTrafficPercent()}%"></div>
                                    {/if}
                                </div>
                                <div class="row">
                                    <div class="col-auto d-flex align-items-center pe-2">
                                        <span class="legend me-2 bg-primary"></span>
                                        <span>过去用量 {$user->LastusedTraffic()}</span>
                                    </div>
                                    <div class="col-auto d-flex align-items-center px-2">
                                        <span class="legend me-2 bg-success"></span>
                                        <span>今日用量 {$user->TodayusedTraffic()}</span>
                                    </div>
                                    <div class="col-auto d-flex align-items-center ps-2">
                                        <span class="legend me-2"></span>
                                        <span>剩余流量 {$user->unusedTraffic()}</span>
                                    </div>
                                </div>
                                <p class="my-3">
                                    {if $user->class === 0}
                                    前往
                                    <a href="/user/product">商店</a>
                                    购买套餐
                                    {else}
                                    你的 LV. {$user->class} 账户会在 {$class_expire_days} 天后到期（{$user->class_expire}）
                                    {/if}
                                </p>
                            </div>
                        </div>
                        {if $public_setting['traffic_log']}
                        <div class="card my-3 mb-0">
                            <div class="card-body">
                                <h3 class="card-title">每小时用量</h3>
                                <div id="traffic-log"></div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
                {if $public_setting['enable_checkin']}
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="card-stamp">
                            <div class="card-stamp-icon bg-green">
                                <i class="ti ti-check"></i>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">每日签到</h3>
                            <p>
                                签到可领取
                                {if $public_setting['checkin_min'] !== $public_setting['checkin_max']}
                                &nbsp;
                                <code>{$public_setting['checkin_min']} MB</code>
                                至
                                <code>{$public_setting['checkin_max']} MB</code>
                                范围内的流量
                                {else}
                                <code>{$public_setting['checkin_min']} MB</code>
                                {/if}
                            </p>
                            <p>
                                上次签到时间：<code id="last-checkin-time">{$user->lastCheckInTime()}</code>
                            </p>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                {if !$user->isAbleToCheckin()}
                                <button id="check-in" class="btn btn-primary ms-auto" disabled>已签到</button>
                                {else}
                                {if $public_setting['enable_checkin_captcha']}
                                {include file='captcha/div.tpl'}
                                {/if}
                                <button id="check-in" class="btn btn-primary ms-auto"
                                    hx-post="/user/checkin" hx-swap="none" hx-vals='js:{
                                    {if $public_setting['enable_checkin_captcha']}
                                    {include file='captcha/ajax.tpl'}
                                    {/if}
                                    }'>
                                    签到
                                </button>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                {/if}
                <div class="col-lg-6 col-sm-12">
                    <div class="card">
                        <div class="ribbon ribbon-top bg-yellow">
                            <i class="ti ti-bell-ringing icon"></i>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">
                                置顶公告
                                {if $ann !== null}
                                <span class="card-subtitle">{$ann->date}</span>
                                {/if}
                            </h3>
                            <p class="text-secondary">
                                {if $ann !== null}
                                {$ann->content}
                                {else}
                                暂无公告
                                {/if}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {if $public_setting['enable_checkin_captcha'] && $user->isAbleToCheckin()}
        {include file='captcha/js.tpl'}
    {/if}

    {if $public_setting['traffic_log']}
    <script src="//{$config['jsdelivr_url']}/npm/@tabler/core@latest/dist/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script>
        function getTrafficChartConfig(trafficData) {
            return {
                chart: {
                    type: "line",
                    fontFamily: "inherit",
                    height: '100%',
                    parentHeightOffset: 0,
                    toolbar: {
                        show: false
                    },
                    animations: {
                        enabled: false
                    }
                },
                stroke: {
                    curve: "smooth"
                },
                fill: {
                    opacity: 1
                },
                series: [
                    {
                        name: "使用流量（MB）",
                        data: trafficData
                    }
                ],
                tooltip: {
                    theme: "dark"
                },
                grid: {
                    padding: {
                        top: -20,
                        right: 0,
                        left: 0,
                        bottom: 0
                    },
                    strokeDashArray: 4
                },
                xaxis: {
                    title: {
                        text: "小时"
                    },
                    labels: {
                        padding: 0
                    },
                    tooltip: {
                        enabled: false
                    },
                    axisBorder: {
                        show: false
                    },
                    categories: [
                        "00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11",
                        "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23"
                    ]
                },
                yaxis: {
                    title: {
                        text: "使用流量（MB）",
                        rotate: -90
                    },
                    labels: {
                        padding: 14
                    }
                },
                colors: ["#FF4500"],
                legend: {
                    show: false
                }
            };
        }
        
        function initTrafficChart() {
            const chartElement = document.getElementById('traffic-log');
            if (!chartElement || !window.ApexCharts) return;
            
            try {
                const chart = new ApexCharts(chartElement, getTrafficChartConfig({$traffic_logs}));
                chart.render();
            } catch (error) {
                console.error('流量图表初始化失败:', error);
            }
        }
        
        document.addEventListener("DOMContentLoaded", function () {
            initTrafficChart();
        });
    </script>
    {/if}

    <script>
    window.APP_CONFIG = {
        enableR2Download: {if $config['enable_r2_client_download']}true{else}false{/if},
        universalSubUrl: "{$UniversalSub}",
        appName: "{$config['appName']}",
        enableSsSub: {if $public_setting['enable_ss_sub']}true{else}false{/if},
        enableV2Sub: {if $public_setting['enable_v2_sub']}true{else}false{/if},
        enableTrojanSub: {if $public_setting['enable_trojan_sub']}true{else}false{/if}
    };
    
    const platformIcons = {$platformIcons};

    const clientRecommendations = {$clientData};
    
    {literal}
    function detectOS() {
        const userAgent = navigator.userAgent;
        if (userAgent.indexOf("Win") !== -1) return "Windows";
        if (userAgent.indexOf("Mac") !== -1) return "macOS";
        if (userAgent.indexOf("Android") !== -1) return "Android";
        if (userAgent.match(/iPhone|iPad|iPod/i)) return "iOS";
        if (userAgent.indexOf("Linux") !== -1) return "Linux";
        return "Windows"; // default
    }
    

    const CONFIG = {
        ANIMATION_DURATION: 350,        // 动画持续时间（毫秒）
        FEEDBACK_TIMEOUT: 2000,         // 反馈提示持续时间（毫秒）
        CLIPBOARD_SUCCESS_TEXT: '已复制',
        CLIPBOARD_ERROR_TEXT: '复制失败，请手动选择并复制',
        CLASSES: {
            BTN_GROUP_MOBILE: 'btn-group-vertical',
            BTN_GROUP_DESKTOP: 'btn-group btn-group-sm', 
            MOBILE_ONLY: 'd-md-none w-100',
            DESKTOP_ONLY: 'd-none d-md-flex',
            MOBILE_SM: 'd-sm-none w-100',
            DESKTOP_SM: 'd-none d-sm-flex'
        },
        BUTTONS: {
            download: { icon: 'ti-download', text: '下载', class: 'btn-primary' },
            downloadAppStore: { icon: 'ti-brand-appstore', text: 'App Store', class: 'btn-primary' },
            copy: { icon: 'ti-copy', text: '复制订阅', class: 'btn-info copy' },
            import: { icon: 'ti-link', text: '一键导入', class: 'btn-success' },
            importRecommended: { icon: 'ti-rocket', text: '一键导入', class: 'btn-success' }
        }
    };
    
    function safeInit(fn, name) {
        try {
            fn();
        } catch (error) {
            console.error(`${name} 初始化失败:`, error);
        }
    }
    
    function createElement(tag, className, content) {
        const element = document.createElement(tag);
        if (className) element.className = className;
        if (content) element.textContent = content;
        return element;
    }
    
    function createIcon(iconClass) {
        const icon = createElement('i', 'ti ' + iconClass);
        return icon;
    }
    
    function createButton(type, options = {}) {
        const { client, url, isMobile, isRecommended } = options;
        const btnConfig = CONFIG.BUTTONS[type];
        
        let config = { ...btnConfig };
        if (type === 'download' && client?.isAppStore) {
            config = CONFIG.BUTTONS.downloadAppStore;
        } else if (type === 'import' && isRecommended) {
            config = CONFIG.BUTTONS.importRecommended;
        }
        
        const btn = createElement(type === 'copy' ? 'button' : 'a', 'btn ' + config.class);
        
        if (type === 'copy') {
            btn.setAttribute('data-clipboard-text', url);
        } else {
            btn.href = url;
            if (type === 'download' && client?.isAppStore) {
                btn.target = '_blank';
            }
        }
        
        btn.appendChild(createIcon(config.icon));
        btn.appendChild(document.createTextNode(' ' + config.text));
        
        return btn;
    }
    
    function createResponsiveButtonGroups(client, urls, isRecommended = false) {
        const { downloadUrl, subUrl, importUrl } = urls;
        const buttons = [];
        
        const buttonConfigs = [
            { type: 'download', url: downloadUrl, needsClient: true },
            { type: 'copy', url: subUrl },
            { type: 'import', url: importUrl }
        ];
        
        const variants = [
            { 
                isMobile: true, 
                classes: isRecommended ? 
                    `${CONFIG.CLASSES.BTN_GROUP_MOBILE} ${CONFIG.CLASSES.MOBILE_ONLY}` :
                    `${CONFIG.CLASSES.BTN_GROUP_MOBILE} ${CONFIG.CLASSES.MOBILE_SM}`
            },
            { 
                isMobile: false, 
                classes: isRecommended ?
                    `${CONFIG.CLASSES.BTN_GROUP_DESKTOP.replace('btn-group-sm', '')} ${CONFIG.CLASSES.DESKTOP_ONLY}` :
                    `${CONFIG.CLASSES.BTN_GROUP_DESKTOP} ${CONFIG.CLASSES.DESKTOP_SM}`
            }
        ];
        
        variants.forEach(variant => {
            const group = createElement('div', variant.classes);
            
            buttonConfigs.forEach(btnConfig => {
                const options = {
                    client: btnConfig.needsClient ? client : null,
                    url: btnConfig.url,
                    isMobile: variant.isMobile,
                    isRecommended
                };
                group.appendChild(createButton(btnConfig.type, options));
            });
            
            buttons.push(group);
        });
        
        return buttons;
    }
    
    function createClientCardContent(client) {
        const content = createElement('div');
        
        const title = createElement('h4', 'mb-1', client.name);
        const desc = createElement('p', 'text-secondary mb-0', client.description);
        
        content.appendChild(title);
        content.appendChild(desc);
        
        return content;
    }
    
    function generateClientHtml(client, isRecommended) {
        const config = window.APP_CONFIG;
        
        let downloadUrl = client.downloadUrl;
        if (!client.isAppStore && downloadUrl.includes('/clients/')) {
            downloadUrl = config.enableR2Download ? '/user' + downloadUrl : downloadUrl;
        }
        
        const subUrl = config.universalSubUrl + '/' + client.format;
        const importUrl = client.importUrl;
        
        const container = createElement('div', 'col-12');
        
        if (isRecommended) {
            const card = createElement('div', 'card');
            const cardBody = createElement('div', 'card-body');
            const flexContainer = createElement('div', 'd-flex flex-column flex-md-row align-items-center justify-content-between gap-3');
            
            const contentDiv = createClientCardContent(client);
            
            const buttonsContainer = createElement('div');
            const urls = { downloadUrl, subUrl, importUrl };
            const buttonGroups = createResponsiveButtonGroups(client, urls, true);
            buttonGroups.forEach(group => buttonsContainer.appendChild(group));
            
            flexContainer.appendChild(contentDiv);
            flexContainer.appendChild(buttonsContainer);
            cardBody.appendChild(flexContainer);
            card.appendChild(cardBody);
            container.appendChild(card);
        } else {
            const item = createElement('div', 'client-item d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between p-3 border rounded gap-2');
            
            const contentDiv = createElement('div', 'flex-fill');
            const title = createElement('h5', 'mb-0', client.name);
            const desc = createElement('small', 'text-muted', client.description);
            contentDiv.appendChild(title);
            contentDiv.appendChild(desc);
            
            const urls = { downloadUrl, subUrl, importUrl };
            const buttonGroups = createResponsiveButtonGroups(client, urls, false);
            
            item.appendChild(contentDiv);
            buttonGroups.forEach(group => item.appendChild(group));
            
            container.appendChild(item);
        }
        
        return container.outerHTML;
    }
    
    function initClientSelector() {
        const os = detectOS();
        document.getElementById('detected-os').textContent = os;
        
        const recommendations = clientRecommendations[os] || clientRecommendations["Windows"];
        const recommendedContainer = document.getElementById('recommended-clients');
        
        if (recommendedContainer) {
            recommendations.forEach(function(client) {
                const clientHtml = generateClientHtml(client, true);
            recommendedContainer.insertAdjacentHTML('beforeend', clientHtml);
            });
        }
        
        const accordionContainer = document.getElementById('platform-accordion');
        
        if (accordionContainer) {
            Object.keys(clientRecommendations).forEach(function(platform) {
                const clients = clientRecommendations[platform];
                const platformId = 'platform-' + platform.toLowerCase();
                const icon = platformIcons[platform] || CONFIG.BUTTONS.download.icon.replace('ti-', 'ti-device-');
                
                const accordionHtml = `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#${platformId}">
                                <i class="ti ${icon} me-2"></i> ${platform}
                            </button>
                        </h2>
                        <div id="${platformId}" class="accordion-collapse collapse" 
                             data-bs-parent="#platform-accordion">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    ${clients.map(client => generateClientHtml(client, false)).join('')}
                                </div>
                            </div>
                        </div>
                    </div>`;
                    
                accordionContainer.insertAdjacentHTML('beforeend', accordionHtml.trim());
            });
        }
    }
    
    function initClipboard() {
        if (typeof ClipboardJS === 'undefined') {
            console.warn('ClipboardJS 未加载');
            return;
        }
        
        const clipboard = new ClipboardJS('.copy');
        
        clipboard.on('success', function(e) {
            e.clearSelection();
            const originalText = e.trigger.innerHTML;
            const checkIcon = createIcon('ti-check');
            e.trigger.innerHTML = '';
            e.trigger.appendChild(checkIcon);
            e.trigger.appendChild(document.createTextNode(' ' + CONFIG.CLIPBOARD_SUCCESS_TEXT));
            setTimeout(function() {
                e.trigger.innerHTML = originalText;
            }, CONFIG.FEEDBACK_TIMEOUT);
        });
        
        clipboard.on('error', function(e) {
            console.error('复制失败:', e.action);
            alert(CONFIG.CLIPBOARD_ERROR_TEXT);
        });
    }
    
    function initCollapseAnimations() {
        const allPlatforms = document.getElementById('all-platforms');
        const recommendedSection = document.querySelector('.recommended-section');
        
        if (!allPlatforms || !recommendedSection) return;
        
        recommendedSection.classList.add('collapsible-section');
        
        allPlatforms.addEventListener('show.bs.collapse', function (e) {
            if (e.target !== allPlatforms) return;
            recommendedSection.classList.add('collapsing');
        });
        
        allPlatforms.addEventListener('hide.bs.collapse', function (e) {
            if (e.target !== allPlatforms) return;
            recommendedSection.classList.remove('collapsing');
            setTimeout(function() {
                recommendedSection.classList.add('expanded');
            }, CONFIG.ANIMATION_DURATION);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        safeInit(initClientSelector, '客户端选择器');
        safeInit(initClipboard, '剪贴板功能');
        safeInit(initCollapseAnimations, '折叠动画');
    });
    {/literal}
    </script>

    {include file='user/footer.tpl'}
