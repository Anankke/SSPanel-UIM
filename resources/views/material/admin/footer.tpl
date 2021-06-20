<footer class="ui-footer">
    <div class="container">
        &copy;{date("Y")} {$config['appName']} | Powered by <a href="/staff">SSPANEL</a>
        {if $config['enable_analytics_code'] === true}{include file='analytics.tpl'}{/if}
    </div>
</footer>

<!-- js -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@1.10.19"></script>
<script src="//cdn.jsdelivr.net/gh/DataTables/DataTables@1.10.19/media/js/dataTables.material.min.js"></script>
<script src="/theme/material/js/base.min.js"></script>
<script src="/theme/material/js/project.min.js"></script>
</body>
</html>
