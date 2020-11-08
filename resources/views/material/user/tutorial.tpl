{include file='user/main.tpl'}

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sukka/markdown.css">
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
.tile-sub div {
    padding: 16px;
}
</style>

{*

如何添加教程：

首先，整个 UI 分为几个部分，包括 Tab 和一一对应的容器。容器之间的切换通过 Tab 实现，容器中会放置 Tile Collapse 组件

|| SSR || SS || V2Ray ||    <- 顶部的三个 Tab
========================
------------------------
| SSR Windows          |    <- 一个收起来的 Tile Collapse 组件
------------------------
------------------------
| SSR Android          |    <- 点击后展开的 Tile Collapse 组件
------------------------
|         教           |
|         程           |    <- 由 markdown 编写的教程放在 resources/views/material/user/markdown 目录下
|         内           |    <- 由浏览器将 markdown 渲染成 HTML
|         容           |
------------------------
------------------------
| SSTap                |    <- 又一个收起来的 Tile Collapse 组件
------------------------

Tab 负责控制 SSR / SS / V2Ray 的容器的切换。Tab 条目位于 ul.nav.nav-list 下；对应的放置客户端教程的容器分别是 #tutorial_ssr #tutorial_ss #tutorial_v2ray

在容器中放置多个 Tile Collapse 组件，每个组件用于显示一种客户端的教程

以下是一个 Tile Collapse 组件的代码，缩进已经调整好，复制后替换就可以用：

                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-<!-- ID -->">
                                            <div class="tile-inner">
                                                <div class="text-overflow"><!-- 客户端名称 --></div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-<!-- ID -->">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-<!--ID -->-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-<!--ID -->-content').innerHTML = marked(`{include file='user/markdown/<!-- markdown 文件名 -->.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>

！！注意！！

* 每个 Tile Collapse 组件的 ID 必须唯一！
* Markdown 文件中的 Code Block 的符号 ` 需要使用反斜杠防转义：\`
* include file 的路径是相对于当前主题所在目录（/resources/views/material）的

*}

<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">使用教程</h1>
        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="ui-card-wrap">
                <div class="col-lg-12 col-md-12">
                    <div class="card">
                        <div class="card-main">
                            <div class="card-inner">
                                <nav class="tab-nav margin-top-no">
                                    <ul class="nav nav-list">
                                        <li class="active">
                                            <a class="" data-toggle="tab" href="#tutorial_ssr"><i class="icon icon-lg">airplanemode_active</i>&nbsp;SSR</a>
                                        </li>
                                        <li>
                                            <a class="" data-toggle="tab" href="#tutorial_ss"><i class="icon icon-lg">flight_takeoff</i>&nbsp;SS</a>
                                        </li>
                                        <li>
                                            <a class="" data-toggle="tab" href="#tutorial_v2ray"><i class="icon icon-lg">flight_land</i>&nbsp;V2RAY</a>
                                        </li>
                                    </ul>
                                </nav>
                                <div class="tab-pane fade active in page-course" id="tutorial_ssr">
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssrwin">
                                            <div class="tile-inner">
                                                <div class="text-overflow">ShadowsocksR / ShadowsocksRR Windows</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssrwin">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssrwin-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssrwin-content').innerHTML = marked(`{include file='user/markdown/ssr-win.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssr-sstap">
                                            <div class="tile-inner">
                                                <div class="text-overflow">SSTap</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssr-sstap">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssr-sstap-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssr-sstap-content').innerHTML = marked(`{include file='user/markdown/sstap.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssrmac">
                                            <div class="tile-inner">
                                                <div class="text-overflow">ShadowsocksX-NG-R8 macOS</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssrmac">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssrmac-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssrmac-content').innerHTML = marked(`{include file='user/markdown/ssr-mac.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-electron-ssr">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Electron SSR Linux</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-electron-ssr">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-electron-ssr-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-electron-ssr-content').innerHTML = marked(`{include file='user/markdown/electron-ssr.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssr-android">
                                            <div class="tile-inner">
                                                <div class="text-overflow">SSR / SSRR Android</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssr-android">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssr-android-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssr-android-content').innerHTML = marked(`{include file='user/markdown/ssr-android.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssr-quantumult">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Quantumult iOS</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssr-quantumult">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssr-quantumult-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssr-quantumult-content').innerHTML = marked(`{include file='user/markdown/quantumult.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssr-shadowrocket">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Shadowrocket iOS</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssr-shadowrocket">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssr-shadowrocket-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssr-shadowrocket-content').innerHTML = marked(`{include file='user/markdown/shadowrocket.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssr-potatso-lite">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Potatso Lite iOS</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssr-potatso-lite">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssr-potatso-lite-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssr-potatso-lite-content').innerHTML = marked(`{include file='user/markdown/potatso-lite.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssr-router">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Merlin & Padavan</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssr-router">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssr-router-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssr-router-content').innerHTML = marked(`{include file='user/markdown/router.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade page-course" id="tutorial_ss">
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssxng">
                                            <div class="tile-inner">
                                                <div class="text-overflow">ShadowsocksX-NG macOS</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssxng">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssxng-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssxng-content').innerHTML = marked(`{include file='user/markdown/ssx.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-ssqt5">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Shadowsocks Qt5 Linux</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-ssqt5">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-ssqt5-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-ssqt5-content').innerHTML = marked(`{include file='user/markdown/ss-qt5.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-router-ss">
                                            <div class="tile-inner">
                                                <div class="text-overflow">Merlin & Padavan</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-router-ss">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-router-ss-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-router-ss-content').innerHTML = marked(`{include file='user/markdown/router-ss.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade page-course" id="tutorial_v2ray">
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-v2rayn">
                                            <div class="tile-inner">
                                                <div class="text-overflow">V2RayN Windows</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-v2rayn">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-v2rayn-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-v2rayn-content').innerHTML = marked(`{include file='user/markdown/v2rayn.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-v2rayng">
                                            <div class="tile-inner">
                                                <div class="text-overflow">V2RayNG Android</div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-v2rayng">
                                            <div class="tile-sub markdown-body">
                                                <div id="tutorial-v2rayng-content"></div>
                                                <script>
                                                    document.getElementById('tutorial-v2rayng-content').innerHTML = marked(`{include file='user/markdown/v2rayng.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {include file='dialog.tpl'}
                </div>
            </div>
        </section>
    </div>
</main>

{include file='user/footer.tpl'}