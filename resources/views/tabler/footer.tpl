<footer class="ui-footer">
    <div class="container">
        &copy;{date("Y")} {$config['appName']} | Powered by <a href="/staff">SSPANEL</a>
        {if $config['enable_analytics_code'] === true}{include file='analytics.tpl'}{/if}
    </div>
</footer>

<!-- js -->
<script src="https://cdn.staticfile.org/jquery/3.6.1/jquery.min.js"></script>
<script src="/theme/tabler/js/base.min.js"></script>
<script src="/theme/tabler/js/project.min.js"></script>

</body>
{include file='live_chat.tpl'}
</html>