<!DOCTYPE HTML>
<html>
	<head>
		<title>{$config["appName"]}</title>
        <meta name="keywords" content=""/>
        <meta name="description" content=""/>
        <meta charset="utf-8" />
        <link rel="shortcut icon" href="/favicon.ico"/>
        <link rel="bookmark" href="/favicon.ico"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no"/>
		<link rel="stylesheet" href="assets/css/main.css"/>
        <noscript><link rel="stylesheet" href="./assets/css/noscript.css" /></noscript>   

  </head>
  
       <body>
			<div id="wrapper">
              <!--首页开始-->
                          <div class="content">
							<div class="inner">
								<h1>邀请码管理</h1>
                          </div>
                        </div>
								<p>{$config["appName"]} 的邀请码(点击邀请码直接注册)</p>
                                   <div class="table-wrapper">
										<table>
											<thead>
												<tr>
													<th>邀请码</th>
                                                  <th>状态</th>
												</tr>
											</thead>
                                             {foreach $codes as $code}
											<tbody>
												<tr>
											<tr>
												<td><a href="/auth/register?code={$code->code}">{$code->code}</a></td>
												<td>可用</td>
											</tr>
											{/foreach}
                                              	</tbody>
										</table>
									</div>
 
                                        
                             <!--底页按钮-->
                           <nav>
							<ul>  
                          <a href="/" class="button">返回首页</a>
                           </ul>
						</nav>
                 
            
                     <!-- 版权底部 -->
                      <footer id="footer">
                   <p class="copyright">&copy;2015-2017 {$config["appName"]}</p>
                      </footer>
              <!-- 版权结束 -->
			 </div>
                <!-- BG -->
			<div id="bg"></div>
	        	<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
         <script src="assets/js/main.js"></script>
	</body>
</html>