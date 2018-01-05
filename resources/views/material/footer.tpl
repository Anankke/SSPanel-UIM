	<footer class="ui-footer">
		<div class="container">
			<marquee>&copy; {$config["appName"]}  <a href="/staff">STAFF</a> 本站Google提供雲計算服務,伺服器位於台灣,遵守台灣法律法規<marquee>{if $config["enable_analytics_code"] == 'true'}{include file='analytics.tpl'}{/if}
		</div>
	</footer>


	<!-- js -->
	<script src="//cdn.staticfile.org/jquery/2.2.1/jquery.min.js"></script>
	<script src="//static.geetest.com/static/tools/gt.js"></script>
	
	<script src="/theme/material/js/base.min.js"></script>
	<script src="/theme/material/js/project.min.js"></script>
</body>
</html>