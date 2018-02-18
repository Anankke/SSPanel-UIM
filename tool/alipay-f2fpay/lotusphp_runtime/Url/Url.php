<?php
class LtUrl
{
	public $configHandle;
	public $routingTable;
	public $baseUrl;

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
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

		$protocol = strtoupper($this->routingTable['protocol']);
		if ('REWRITE' == $protocol)
		{
			$this->baseUrl = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME) . '/';
		}
		else if ('STANDARD' == $protocol)
		{
			$this->baseUrl = $_SERVER['PHP_SELF'];
		}
		else
		{
			$this->baseUrl = '';
		}
	}

	public function generate($module, $action, $args = array())
	{
		$args = array_merge(array('module' => $module, 'action' => $action), $args);
		$url = ''; 
		// $url = $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		// $url .= $_SERVER['HTTP_HOST'];
		// $url .= $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
		$url .= $this->baseUrl;
		$url .= $this->reverseMatchingRoutingTable($args);
		return $url;
	}

	/**
	 * 将变量反向匹配路由表, 返回匹配后的url
	 * 
	 * @param array $params 
	 * @return string 
	 */
	public function reverseMatchingRoutingTable($args)
	{
		$ret = $this->routingTable['pattern'];
		$default = $this->routingTable['default'];
		$reqs = $this->routingTable['reqs'];
		$delimiter = $this->routingTable['delimiter'];
		$varprefix = $this->routingTable['varprefix'];
		$postfix = $this->routingTable['postfix'];
		$protocol = strtoupper($this->routingTable['protocol']);
		if ('STANDARD' == $protocol)
		{
			return '?' . http_build_query($args, '', '&');
		}
		$pattern = explode($delimiter, trim($this->routingTable['pattern'], $delimiter));

		foreach($pattern as $k => $v)
		{
			if ($v[0] == $varprefix)
			{ 
				// 变量
				$varname = substr($v, 1); 
				// 匹配变量
				if (isset($args[$varname]))
				{
					$regex = "/^{$reqs[$varname]}\$/i";
					if (preg_match($regex, $args[$varname]))
					{
						$ret = str_replace($v, $args[$varname], $ret);
						unset($args[$varname]);
					}
				}
				else if (isset($default[$varname]))
				{
					$ret = str_replace($v, $default[$varname], $ret);
				}
			}
			else if ($v[0] == '*')
			{ 
				// 通配符
				$tmp = '';
				foreach($args as $key => $value)
				{
					if (!isset($default[$key]))
					{
						$tmp .= $key . $delimiter . rawurlencode($value) . $delimiter;
					}
				}
				$tmp = rtrim($tmp, $delimiter);
				$ret = str_replace($v, $tmp, $ret);
				$ret = rtrim($ret, $delimiter);
			}
			else
			{ 
				// 静态
			}
		}
		if ('REWRITE' == $protocol)
		{
			$ret = $ret . $postfix;
		}
		else if ('PATH_INFO' == $protocol)
		{
			$ret = $_SERVER['SCRIPT_NAME'] . $delimiter . $ret . $postfix;
		}
		else
		{
			$ret = $ret . $postfix;
		}
		return $ret;
	}
}
