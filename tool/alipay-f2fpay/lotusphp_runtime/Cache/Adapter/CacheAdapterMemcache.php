<?php
class LtCacheAdapterMemcache implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		return memcache_connect($hostConf["host"], $hostConf["port"]);
	}

	public function add($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		return $connectionResource->add($this->getRealKey($tableName, $key), $value, false, $ttl);
	}

	public function del($key, $tableName, $connectionResource)
	{
		return $connectionResource->delete($this->getRealKey($tableName, $key), 0);
	}

	public function get($key, $tableName, $connectionResource)
	{
		return $connectionResource->get($this->getRealKey($tableName, $key));
	}

	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		return $connectionResource->replace($this->getRealKey($tableName, $key), $value, false, $ttl);
	}

	protected function getRealKey($tableName, $key)
	{
		return $tableName . "-" . $key;
	}
}