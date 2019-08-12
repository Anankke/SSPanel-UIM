{include file='user/main.tpl'}

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
                                        <li {if $ssr_prefer}class="active"{/if}>
                                            <a class="" data-toggle="tab" href="#tutorial_ssr"><i class="icon icon-lg">airplanemode_active</i>&nbsp;SSR</a>
                                        </li>
                                        <li {if !$ssr_prefer}class="active"{/if}>
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



