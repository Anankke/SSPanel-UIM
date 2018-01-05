<?php
class LtCacheAdapterFile implements LtCacheAdapter
{
	public function connect($hostConf)
	{
		$fileStore = new LtStoreFile;
		$fileStore->prefix = 'LtCache-file';
		$fileStore->useSerialize = true;
		$fileStore->init();
		return $fileStore;
	}

	public function add($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		if (0 != $ttl)
		{
			$ttl += time();
		}
		if (true == $connectionResource->add($this->getRealKey($tableName, $key), array("ttl" => $ttl, "value" => $value)))
		{
			return true;
		}
		else
		{
			if ($this->get($key,$tableName,$connectionResource))
			{
				return false;
			}
			else
			{
				$this->del($key,$tableName,$connectionResource);
				return $connectionResource->add($this->getRealKey($tableName, $key), array("ttl" => $ttl, "value" => $value));
			}
		}
	}

	public function del($key, $tableName, $connectionResource)
	{
		return $connectionResource->del($this->getRealKey($tableName, $key));
	}

	public function get($key, $tableName, $connectionResource)
	{
		$cachedArray = $connectionResource->get($this->getRealKey($tableName, $key));
		if (is_array($cachedArray) && (0 == $cachedArray["ttl"] || $cachedArray["ttl"] > time()))
		{
			return $cachedArray["value"];
		}
		else
		{
			return false;
		}
	}

	public function update($key, $value, $ttl = 0, $tableName, $connectionResource)
	{
		if (0 != $ttl)
		{
			$ttl += time();
		}
		return $connectionResource->update($this->getRealKey($tableName, $key), array("ttl" => $ttl, "value" => $value));
	}

	protected function getRealKey($tableName, $key)
	{
		return $tableName . "-" . $key;
	}
}
