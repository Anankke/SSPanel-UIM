<?php
interface LtCacheAdapter
{
	public function connect($hostConf);
	public function add($key, $value, $ttl = 0, $tableName, $connectionResource);
	public function del($key, $tableName, $connectionResource);
	public function get($key, $tableName, $connectionResource);
	public function update($key, $value, $ttl = 0, $tableName, $connectionResource);
}