{include file='user/main.tpl'}







<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">Recharge result</h1>


        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="col-lg-12 col-md-12">
                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            {if ($success == 1)}
                                <p>Recharged successfully {$money} CNY! Please go to the <a href="/user/shop">Package Purchase</a> page to purchase your package.</p>
                            {else}
                                <p>The recharge failed, please check the bill or contact the administrator.</p>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>





{include file='user/footer.tpl'}