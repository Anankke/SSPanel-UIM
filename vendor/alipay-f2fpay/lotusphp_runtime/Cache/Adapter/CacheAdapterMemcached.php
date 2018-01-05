<?php
class LtCacheAdapterMemcached implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		$connectionResource = new Memcached();
		$connectionResource->addServer($hostConf["host"], $hostConf["port"]);
		return $connectionResource;
	}

	public function add($key, $value, $ttl=0, $tableName, $connectionResource)
	{
		return $connectionResource->add($this->getRealKey($tableName, $key), $value, $ttl);
	}

	public function del($key, $tableName, $connectionResource)
	{
		return $connectionResource->delete($this->getRealKey($tableName, $key));
	}

	public function get($key, $tableName, $connectionResource)
	{
		return $connectionResource->get($this->getRealKey($tableName, $key));
	}

	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		return $connectionResource->replace($this->getRealKey($tableName, $key), $value, $ttl);
	}

	protected function getRealKey($tableName, $key)
	{
		return $tableName . "-" . $key;
	}
}