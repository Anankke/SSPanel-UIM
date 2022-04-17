<footer class="ui-footer">
    <div class="container">
        &copy;{date("Y")} {$config['appName']} | Powered by <a href="/staff">SSPANEL</a>
        {if $config['enable_analytics_code'] === true}{include file='analytics.tpl'}{/if}
    </div>
</footer>

<!-- js -->
<script src="https://cdn.staticfile.org/jquery/3.6.0/jquery.min.js"></script>
{if isset($geetest_html)}
    <script src="//static.geetest.com/static/tools/gt.js"></script>
{/if}
<script src="/theme/material/js/base.min.js"></script>
<script src="/theme/material/js/project.min.js"></script>

</body>
{include file='live_chat.tpl'}
</html>