{include file='user/main.tpl'}







<main class="content">
    <div class="content-header ui-content-header">
        <div class="container">
            <h1 class="content-heading">充值结果</h1>


        </div>
    </div>
    <div class="container">
        <section class="content-inner margin-top-no">
            <div class="col-lg-12 col-md-12">
                <div class="card margin-bottom-no">
                    <div class="card-main">
                        <div class="card-inner">
                            {if ($success == 1)}
                                <p>已充值成功 {$money} 元！请进入 <a href="/user/shop">套餐购买</a> 页面来选购您的套餐。</p>
                            {else}
                                <p>充值失败，请检查账单或联系管理员。</p>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>





{include file='user/footer.tpl'}