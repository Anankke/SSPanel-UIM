<!DOCTYPE HTML>
<html>
  <head>
    <title>
      {$config["appName"]} - 可能是最贴心的云服务商
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--[if lte IE 8]>
      <script src="/moexin/js/ie/html5shiv.js">
      </script>
    <![endif]-->
    <link rel="stylesheet" href="/moexin/css/main.css" />
    <!--[if lte IE 9]>
      <link rel="stylesheet" href="/moexin/css/ie9.css" />
    <![endif]-->
    <!--[if lte IE 8]>
      <link rel="stylesheet" href="/moexin/css/ie8.css" />
    <![endif]-->
  </head>
  
  <body>
    <!-- Wrapper -->
    <div id="wrapper">
      <!-- Header -->
      <header id="header" class="alt">
        <span class="logo">
          <img src="/images/logo.png" width="10%" height="10%" />
        </span>
        <h1>
          {$config["appName"]}
        </h1>
        <p>
          提供最优质网游加速器，网络云存储，离线云挂机等综合服务
          <br />
        </p>
      </header>
      <!-- Nav -->
      <nav id="nav">
        <ul>
          <li>
            <a href="#intro" class="active">
              {$config["appName"]}
            </a>
          </li>
          {if $user->isLogin} {else}
          <li>
            <a href="#first">
              技术优势
            </a>
          </li>
          {/if}
          <li>
            <a href="#second">
              {if $user->isLogin}账号概览{else}平台概览{/if}
            </a>
          </li>
          <li>
            <a href="#cta">
              私人订制
            </a>
          </li>
        </ul>
      </nav>
      <!-- Main -->
      <div id="main">
        <!-- Introduction -->
        <section id="intro" class="main">
          <div class="spotlight">
            <div class="content">
              <header class="major">
                <h2>
                  {$config["appName"]}
                </h2>
              </header>
              <p>
                <h3>
                  布局亚太，面向中国，着眼世界
                </h3>
                我们在美国西海岸，亚洲北部，亚洲东部以及亚洲东南均有已部署或是规划中的节点，覆盖了90%的中国大陆地区企业应用出海需求。
              </p>
              <ul class="actions">
                {if $user->isLogin}
                <li>
                  <a href="/user" class="button">
                    用户中心
                  </a>
                </li>
                {else}
                <li>
                  <a href="/auth/login" class="button">
                    登陆
                  </a>
                </li>
                <li>
                  <a href="/auth/register" class="button">
                    注册
                  </a>
                </li>
                {/if}
              </ul>
            </div>
            <span class="image">
              <img src="images/pic01.jpg" alt="" />
            </span>
          </div>
        </section>
        {if $user->isLogin} {else}
        <!-- First Section -->
        <section id="first" class="main special">
          <header class="major">
            <h2>
              技术优势
            </h2>
          </header>
          <ul class="features">
            <li>
              <span class="icon major style1 fa-code">
              </span>
              <h3>
                高性能硬件设备
              </h3>
              <p>
                我们设计自有的宿主机硬件平台且不断更新，调整，确保每一个客户的实例随时处于最佳性能
              </p>
            </li>
            <li>
              <span class="icon major style3 fa-copy">
              </span>
              <h3>
                先进的网络架构
              </h3>
              <p>
                我们选择思科作为我们网络设备供应商，同时高度冗余的网络架构设计使得整体可用性大大提高
              </p>
            </li>
            <li>
              <span class="icon major style5 fa-diamond">
              </span>
              <h3>
                友善且有效的客户服务
              </h3>
              <p>
                我们不断听取客户的意见也不断改进，客服团队拥有多年相关经验，确保客户的任何问题都能得到解决
              </p>
            </li>
          </ul>
          <footer class="major">
            <ul class="actions">
              <li>
                <a href="//wpa.qq.com/msgrd?v=3&uin=1400605522" class="button">
                  详情咨询
                </a>
              </li>
            </ul>
          </footer>
        </section>
        {/if}
        <!-- Second Section -->
        <section id="second" class="main special">
          <header class="major">
            <h2>
              {if $user->isLogin}尊敬的{if $user->class!=0}VIP{$user->class}{else}免费{/if}用户：{$user->user_name}{else}简单、可靠的准企业级平台{/if}
            </h2>
            <p>
              {if $user->isLogin}{if $user->class!=0}您的VIP到期时间为：{if $user->class_expire!="1989-06-04
              00:05:00"}{$user->class_expire}{else}不过期{/if}，为保障服务正常使用，请及时充值续费。{else}我们非常乐意为你提供更优质的服务，希望你们能支持我们更好的发展，可以选择充值开通VIP。{/if}{else}通过我们强大的自主设计宿主机平台，我们得以在性能，可用性以及成本方面做到平衡，使得低价高质再也不是梦想{/if}
            </p>
          </header>
          <ul class="statistics">
            {if $user->isLogin}
            <li class="style1">
              <span class="icon fa-hourglass-start">
              </span>
              <strong>
                {$user->unusedTraffic()}
              </strong>
              可用流量
            </li>
            <li class="style2">
              <span class="icon fa-hourglass-end">
              </span>
              <strong>
                {$user->TodayusedTraffic()}
              </strong>
              今日用量
            </li>
            <li class="style3">
              <span class="icon fa-tachometer">
              </span>
              <strong>
                {if $user->node_speedlimit!=0}{$user->node_speedlimit}Mbps{else}∞{/if}
              </strong>
              速度限制
            </li>
            <li class="style4">
              <span class="icon fa-laptop">
              </span>
              <strong>
                {if $user->node_connector!=0}{$user->online_ip_count()} / {$user->node_connector}{else}{$user->online_ip_count()}
                / ∞{/if}
              </strong>
              在线设备
            </li>
            <li class="style5">
              <span class="icon fa-diamond">
              </span>
              <strong>
                {$user->money}
              </strong>
              可用余额
            </li>
            {else}
            <li class="style1">
              <span class="icon fa-users">
              </span>
              <strong>
                <div id="setnum01" class="number">
                  获取中
                </div>
              </strong>
              活跃用户
            </li>
            {/if}
          </ul>
          {if $user->isLogin} {else}
          <p class="content">
            TCat cloud service provides the best quality online game accelerator,
            network cloud storage, offline cloud hangers and other comprehensive services.
            Layout Asia Pacific, face China, eyes on the world. We have deployed or
            planned nodes in the west coast of the United States, north Asia, east
            Asia and southeast Asia, covering 90% of mainland Chinese enterprises applying
            to sea. Simple, reliable and quasi-enterprise platform. Through our powerful
            self-designed hosting platform, we are able to balance performance, availability,
            and cost, so that low quality is no longer a dream.
          </p>
          {/if}
          <footer class="major">
            <ul class="actions">
              {if $user->isLogin}
              <li>
                <a href="/user/code" class="button">
                  充值余额
                </a>
              </li>
              <li>
                <a href="/user/shop" class="button">
                  购买套餐
                </a>
              </li>
              {else}
              <li>
                <a href="//wpa.qq.com/msgrd?v=3&uin=1400605522" class="button">
                  详情咨询
                </a>
              </li>
              {/if}
            </ul>
          </footer>
        </section>
        <!-- Get Started -->
        <section id="cta" class="main special">
          <header class="major">
            <h2>
              私人订制
            </h2>
            <p>
              联系我们的销售，获取我们更多的资源信息。
            </p>
          </header>
          <footer class="major">
            <ul class="actions">
              <li>
                <a href="//wpa.qq.com/msgrd?v=3&uin=1400605522" class="button special">
                  加入我们
                </a>
              </li>
              <li>
                <a href="//wpa.qq.com/msgrd?v=3&uin=1400605522" class="button">
                  详情咨询
                </a>
              </li>
            </ul>
          </footer>
        </section>
      </div>
      <!-- Footer -->
      <footer id="footer">
        <section>
          <h2>
            关于我们
          </h2>
          <p>
            {$config["appName"]} 始于2017年，Moexin旗下子品牌，与当今主流云计算解决方案保持同步，配备多名资深技术骨干，以保证您的业务正常运行。
          </p>
          <ul class="actions">
            <li>
              <a href="//wpa.qq.com/msgrd?v=3&uin=1400605522" class="button">
                加入我们
              </a>
            </li>
          </ul>
        </section>
        <section>
          <h2>
            联系方式
          </h2>
          <dl class="alt">
            <dt>
              电话
            </dt>
            <dd>
              (852) 5517-4259
            </dd>
            <dt>
              邮箱
            </dt>
            <dd>
              <a href="#">
                i@98k.li
              </a>
            </dd>
          </dl>
          <ul class="icons">
            <li>
              <a href="#" class="icon fa-twitter alt">
                <span class="label">
                  Twitter
                </span>
              </a>
            </li>
            <li>
              <a href="#" class="icon fa-facebook alt">
                <span class="label">
                  Facebook
                </span>
              </a>
            </li>
            <li>
              <a href="#" class="icon fa-instagram alt">
                <span class="label">
                  Instagram
                </span>
              </a>
            </li>
            <li>
              <a href="#" class="icon fa-github alt">
                <span class="label">
                  GitHub
                </span>
              </a>
            </li>
            <li>
              <a href="#" class="icon fa-dribbble alt">
                <span class="label">
                  Dribbble
                </span>
              </a>
            </li>
          </ul>
        </section>
        <p class="copyright">
          &copy; 2017-
          <script type="text/javascript">
            document.write(new Date().getFullYear());
          </script>
          <a href="/">
            {$config["appName"]}
          </a>
          .
        </p>
      </footer>
    </div>
    <!-- Scripts -->
    <script src="/moexin/js/jquery.min.js">
    </script>
    <script src="/moexin/js/jquery.scrollex.min.js">
    </script>
    <script src="/moexin/js/jquery.scrolly.min.js">
    </script>
    <script src="/moexin/js/skel.min.js">
    </script>
    <script src="/moexin/js/util.js">
    </script>
    <!--[if lte IE 8]>
      <script src="/moexin/js/ie/respond.min.js">
      </script>
    <![endif]-->
    <script src="/moexin/js/main.js">
    </script>
    <script type="text/javascript">
      setInterval(displayNum1, 1000);
      function displayNum1() {
        document.getElementById("setnum01").innerHTML = Math.floor(Math.random() * (7000 - 6666) + 6666);
      }
    </script>
  </body>
</html>