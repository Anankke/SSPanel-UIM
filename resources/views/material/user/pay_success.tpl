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
                                <p>正在处理您的支付，请您稍等。此页面会自动刷新，或者您可以选择关闭此页面，余额将自动到账</p>
                                <script>
                                    setTimeout('window.location.reload()', 5000);
                                </script>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>


{include file='user/footer.tpl'}