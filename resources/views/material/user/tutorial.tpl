{include file='user/main.tpl'}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

{*
                                    <div class="tile tile-collapse">
                                        <div data-toggle="tile" data-target="#tutorial-heading-<!-- 客户端名称 -->">
                                            <div class="tile-side pull-left" data-ignore="tile">
                                                <div class="avatar avatar-sm"><i class="material-icons"><!-- 客户端 Icon --></i></div>
                                            </div>
                                            <div class="tile-inner">
                                                <div class="text-overflow"><!-- 客户端名称 --></div>
                                            </div>
                                        </div>
                                        <div class="collapsible-region collapse" id="tutorial-heading-<!-- 客户端名称 -->">
                                            <div class="tile-sub">
                                                <div id="tutorial-<!--客户端名称>-content">
                                                <!-- 客户端教程内容 -->
                                                </div>
                                                <script>
                                                    document.getElementById('content').innerHTML = marked(`{include file='markdown/**.md'}`);
                                                </script>
                                            </div>
                                        </div>
                                    </div>
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
                                            <a class="" data-toggle="tab" href="#tutorial_ssr"><i class="icon icon-lg">flight_takeoff</i>&nbsp;SS/SSD</a>
                                        </li>
                                        <li>
                                            <a class="" data-toggle="tab" href="#tutorial_v2ray"><i class="icon icon-lg">flight_land</i>&nbsp;V2RAY</a>
                                        </li>
                                    </ul>
                                </nav>

                                <div class="tab-pane fade active in page-course" id="tutorial_ssr">
                                </div>

                                <div class="tab-pane fade page-course" id="tutorial_ss">
                                </div>

                                <div class="tab-pane fade page-course" id="tutorial_v2ray">
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



