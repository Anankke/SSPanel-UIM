<footer class="ui-footer">
    <div class="container">
        &copy;{date("Y")} {$config['appName']} | Powered by <a href="/staff">SSPANEL</a>
        {if $config['enable_analytics_code'] === true}{include file='analytics.tpl'}{/if}
    </div>
</footer>

<!-- js -->
<script src="//cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.19/js/dataTables.material.min.js"></script>
<script src="/theme/tabler/js/base.min.js"></script>
<script src="/theme/tabler/js/project.min.js"></script>

</body>
</html>
