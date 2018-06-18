<?php
class LtStoreMemory implements LtStore
{
	protected $stack;

	public function add($key, $value)
	{
		if (isset($this->stack[$key]))
		{
			return false;
		}
		else
		{
			$this->stack[$key] = $value;
			return true;
		}
	}

	public function del($key)
	{
		if (isset($this->stack[$key]))
		{
			unset($this->stack[$key]);
			return true;
		}
		else
		{
			return false;
		}
	}

	public function get($key)
	{
		return isset($this->stack[$key]) ? $this->stack[$key] : false;
	}

	/**
	 * key不存在返回false
	 * 
	 * @return bool 
	 */
	public function update($key, $value)
	{
		if (!isset($this->stack[$key]))
		{
			return false;
		}
		else
		{
			$this->stack[$key] = $value;
			return true;
		}
	}
}
