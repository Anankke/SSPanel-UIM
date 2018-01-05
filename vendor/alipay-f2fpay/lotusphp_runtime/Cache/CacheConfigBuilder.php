<?php
class LtCacheConfigBuilder
{
	protected $servers = array();

	protected $defaultConfig = array(
		"adapter"    => "phps",     //apc,xcach,ea; file, phps; memcached
	//"prefix"     => ""
	//"host"       => "localhost", //some ip, hostname
	//"port"       => 3306,
	);

	public function addSingleHost($hostConfig)
	{
		$this->addHost("group_0", "node_0", "master", $hostConfig);
	}

	public function addHost($groupId, $nodeId = "node_0", $role = "master", $hostConfig)
	{
		if (isset($this->servers[$groupId][$nodeId][$role]))
		{//以相同role的第一个host为默认配置
			$ref = $this->servers[$groupId][$nodeId][$role][0];
		}
		else if ("slave" == $role && isset($this->servers[$groupId][$nodeId]["master"]))
		{//slave host以master的第一个host为默认配置
			$ref = $this->servers[$groupId][$nodeId]["master"][0];
		}
		else if (isset($this->servers[$groupId]) && count($this->servers[$groupId]))
		{//以本group第一个node的master第一个host为默认配置
			$refNode = key($this->servers[$groupId]);
			$ref = $this->servers[$groupId][$refNode]["master"][0];
		}
		else
		{
			if (!isset($hostConfig["adapter"]))
			{
				trigger_error("No db adapter specified");
			}
			$ref = $this->defaultConfig;
		}
		$conf = array_merge($ref, $hostConfig);
		$this->servers[$groupId][$nodeId][$role][] = $conf;
	}

	public function getServers()
	{
		return $this->servers;
	}
}