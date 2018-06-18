<?php
class LtCacheHandle
{
	public $configHandle;
	public $group;
	public $node;
	public $role = "master";
	public $connectionManager;
	public $connectionResource;
	protected $connectionAdapter;

	public function __construct()
	{
	}

	public function init()
	{
		$this->connectionManager = new LtCacheConnectionManager;
		$this->connectionManager->configHandle =$this->configHandle;
	}

	public function add($key, $value, $ttl = 0, $tableName)
	{
		$this->initConnection();
		return $this->connectionAdapter->add($key, $value, $ttl, $tableName, $this->connectionResource);
	}

	public function del($key, $tableName)
	{
		$this->initConnection();
		return $this->connectionAdapter->del($key, $tableName, $this->connectionResource);
	}

	public function get($key, $tableName)
	{
		$this->initConnection();
		return $this->connectionAdapter->get($key, $tableName, $this->connectionResource);
	}

	public function update($key, $value, $ttl = 0, $tableName)
	{
		$this->initConnection();
		return $this->connectionAdapter->update($key, $value, $ttl, $tableName, $this->connectionResource);
	}

	protected function initConnection()
	{
		$connectionInfo = $this->connectionManager->getConnection($this->group, $this->node, $this->role);
		$this->connectionAdapter = $connectionInfo["connectionAdapter"];
		$this->connectionResource = $connectionInfo["connectionResource"];
	}
}