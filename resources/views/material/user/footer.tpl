	<footer class="ui-footer">
		<div class="container">
			<marquee>&copy;{date("Y")} {$config["appName"]} | Powered by <a href="/staff">SSPANEL</a> </marquee> {if $config["enable_analytics_code"] == 'true'}{include file='analytics.tpl'}{/if}
		</div>
	</footer>

	<!-- js -->
    {if $config["sspanelAnalysis"] == 'true'}
    <!-- Google Analytics -->
    <script>
        window.ga=window.ga||function(){ (ga.q=ga.q||[]).push(arguments) };ga.l=+new Date;
        ga('create', 'UA-111801619-3', 'auto');
        var hostDomain = window.location.host || document.location.host || document.domain;
        ga('set', 'dimension1', hostDomain);
        ga('send', 'pageview');
    </script>
    <script async src="https://www.google-analytics.com/analytics.js"></script>
    <!-- End Google Analytics -->
    {/if}
	<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0"></script>
    {if isset($geetest_html)}
	<script src="//static.geetest.com/static/tools/gt.js"></script>
    {/if}
	<script src="/theme/material/js/base.min.js"></script>
	<script src="/theme/material/js/project.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/clipboard@1.5.16/dist/clipboard.min.js"></script>
	<script type="text/javascript" color="0,217,255" opacity="0.5" count="49" src="https://cdn.jsdelivr.net/npm/canvas-nest.js@1.0.1"></script>

    <script>console.log('数据库查询 {count($queryLog)} 次 | 执行时间 {$optTime} ms');</script>
</body>
</html>
