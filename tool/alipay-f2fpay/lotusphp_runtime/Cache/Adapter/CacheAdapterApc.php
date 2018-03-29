<?php
class LtCacheAdapterApc implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		return true;
	}

	public function add($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		return apc_add($this->getRealKey($tableName, $key), $value, $ttl);
	}

	public function del($key, $tableName, $connectionResource)
	{
		return apc_delete($this->getRealKey($tableName, $key));
	}

	public function get($key, $tableName, $connectionResource)
	{
		return apc_fetch($this->getRealKey($tableName, $key));
	}

	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		if ($this->del($key, $tableName, $connectionResource))
		{
			return $this->add($key, $value, $ttl, $tableName, $connectionResource);
		}
		else
		{
			return false;
		}
	}

	protected function getRealKey($tableName, $key)
	{
		return $tableName . "-" . $key;
	}
}