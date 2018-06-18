<?php
/**
 * The Router class
 */
class LtRouter
{
	public $configHandle;
	public $routingTable;
	public $module;
	public $action;

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil"))
			{
				$this->configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				$this->configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{
		$this->routingTable = $this->configHandle->get("router.routing_table");
		if (empty($this->routingTable))
		{
			$this->routingTable = array('pattern' => ":module/:action/*",
				'default' => array('module' => 'default', 'action' => 'index'),
				'reqs' => array('module' => '[a-zA-Z0-9\.\-_]+',
					'action' => '[a-zA-Z0-9\.\-_]+'
					),
				'varprefix' => ':',
				'delimiter' => '/',
				'postfix' => '',
				'protocol' => 'PATH_INFO', // REWRITE STANDARD
				);
		}

		$delimiter = $this->routingTable['delimiter'];
		$postfix = $this->routingTable['postfix'];
		$protocol = strtoupper($this->routingTable['protocol']);
		$module = '';
		$action = '';
		$params = array();
		// HTTP HTTPS
		if (isset($_SERVER['SERVER_PROTOCOL']))
		{
			if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO']))
			{ 
				// 忽略后缀
				$url = rtrim($_SERVER['PATH_INFO'], "$postfix");
				$url = explode($delimiter, trim($url, "/"));
			}
			else if (isset($_SERVER['REQUEST_URI']))
			{
				if ('REWRITE' == $protocol)
				{
					if (0 == strcmp($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))
					{
						$url = array();
					}
					else
					{
						$url = substr($_SERVER['REQUEST_URI'], strlen(pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME)));
						$url = rtrim($url, "$postfix");
						$url = explode($delimiter, trim($url, "/"));
					}
				}
				else if ('PATH_INFO' == $protocol)
				{
					$url = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
					$url = rtrim($url, "$postfix");
					$url = explode($delimiter, trim($url, "/"));
				}
				else //STANDARD
				{
					$url = array();
					foreach($_GET as $v)
					{
						$url[] = $v;
					}
				}
			}
			else
			{
				$url = array();
				foreach($_GET as $v)
				{
					$url[] = $v;
				}
			}
			$params = $this->matchingRoutingTable($url);
			$module = $params['module'];
			$action = $params['action'];
		}
		else
		{ 
			// CLI
			$i = 0;
			while (isset($_SERVER['argv'][$i]) && isset($_SERVER['argv'][$i + 1]))
			{
				if (("-m" == $_SERVER['argv'][$i] || "--module" == $_SERVER['argv'][$i]))
				{
					$module = $_SERVER['argv'][$i + 1];
				}
				else if (("-a" == $_SERVER['argv'][$i] || "--action" == $_SERVER['argv'][$i]))
				{
					$action = $_SERVER['argv'][$i + 1];
				}
				else
				{
					$key = $_SERVER['argv'][$i];
					$params[$key] = $_SERVER['argv'][$i + 1];
				}
				$i = $i + 2;
			}
		}
		// 如果$_GET中不存在配置的变量则添加
		foreach($params as $k => $v)
		{
			!isset($_GET[$k]) && $_GET[$k] = $v;
		}
		$this->module = $module;
		$this->action = $action;
	}

	/**
	 * url 匹配路由表
	 * 
	 * @param  $ [string|array] $url
	 * @return 
	 * @todo 修复导致$_GET多出属性的BUG
	 * @todo 如果是rewrite或者path_info模式，可能需要unset module和action两个$_GET变量
	 */
	public function matchingRoutingTable($url)
	{
		$ret = $this->routingTable['default']; //初始化返回值为路由默认值
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];
		$postfix = $this->routingTable['postfix'];
		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		/**
		 * 预处理url
		 */
		if (is_string($url))
		{
			$url = rtrim($url, $postfix); //忽略后缀
			$url = explode($delimiter, trim($url, $delimiter));
		}

		foreach($pattern as $k => $v)
		{
			if ($v[0] == $varprefix)
			{ 
				// 变量
				$varname = substr($v, 1); 
				// 匹配变量
				if (isset($url[$k]))
				{
					if (isset($reqs[$varname]))
					{
						$regex = "/^{$reqs[$varname]}\$/i";
						if (preg_match($regex, $url[$k]))
						{
							$ret[$varname] = $url[$k];
						}
					}
				}
			}
			else if ($v[0] == '*')
			{
				// 通配符
				$pos = $k;
				while (isset($url[$pos]) && isset($url[$pos + 1]))
				{
					$ret[$url[$pos ++]] = urldecode($url[$pos]);
					$pos++;
				}
			}
			else
			{ 
				// 静态
			}
		}
		return $ret;
	}
}
