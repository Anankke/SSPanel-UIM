	<footer class="ui-footer">
		<div class="container">
			<marquee>&copy; {$config["appName"]}  <a href="/staff">STAFF</a><amarquee>{if $config["enable_analytics_code"] == 'true'}{include file='analytics.tpl'}{/if}
		</div>
	</footer>




	<!-- js -->
	<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.1"></script>
    {if isset($geetest_html)}
	<script src="//static.geetest.com/static/tools/gt.js"></script>
    {/if}
	<script src="/theme/material/js/base.min.js"></script>
	<script src="/theme/material/js/project.min.js"></script>
	<script color="0,217,255" opacity="0.5" count="49" src="https://cdn.jsdelivr.net/npm/canvas-nest.js@1.0.1"></script>
</body>
</html>
