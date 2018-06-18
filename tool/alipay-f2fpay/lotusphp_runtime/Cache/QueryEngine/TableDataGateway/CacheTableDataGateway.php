<?php
class LtCacheTableDataGateway
{
	public $tableName;

	public $ch;

	public function add($key, $value, $ttl = 0)
	{
		return $this->ch->add($key, $value, $ttl, $this->tableName);
	}

	public function del($key)
	{
		return $this->ch->del($key, $this->tableName);
	}

	public function get($key)
	{
		return $this->ch->get($key, $this->tableName);
	}

	public function update($key, $value, $ttl = 0)
	{
		return $this->ch->update($key, $value, $ttl, $this->tableName);
	}
}