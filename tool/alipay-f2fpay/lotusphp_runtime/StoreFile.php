<?php
class LtStoreFile implements LtStore
{
	public $storeDir;
	public $prefix = 'LtStore';
	public $useSerialize = false;
	static public $defaultStoreDir = "/tmp/LtStoreFile/";
	public function init()
	{
		/**
		 * 目录不存在和是否可写在调用add是测试
		 * @todo detect dir is exists and writable
		 */
		if (null == $this->storeDir)
		{
			$this->storeDir = self::$defaultStoreDir;
		}
		$this->storeDir = str_replace('\\', '/', $this->storeDir);
		$this->storeDir = rtrim($this->storeDir, '\\/') . '/';
	}

	/**
	 * 当key存在时:
	 * 如果没有过期, 不更新值, 返回 false
	 * 如果已经过期,   更新值, 返回 true
	 *
	 * @return bool
	 */
	public function add($key, $value)
	{
		$file = $this->getFilePath($key);
		$cachePath = pathinfo($file, PATHINFO_DIRNAME);
		if (!is_dir($cachePath))
		{
			if (!@mkdir($cachePath, 0777, true))
			{
				trigger_error("Can not create $cachePath");
			}
		}
		if (is_file($file))
		{
			return false;
		}
		if ($this->useSerialize)
		{
			$value = serialize($value);
		}
		$length = file_put_contents($file, '<?php exit;?>' . $value);
		return $length > 0 ? true : false;
	}

	/**
	 * 删除不存在的key返回false
	 *
	 * @return bool
	 */
	public function del($key)
	{
		$file = $this->getFilePath($key);
		if (!is_file($file))
		{
			return false;
		}
		else
		{
			return @unlink($file);
		}
	}

	/**
	 * 取不存在的key返回false
	 * 已经过期返回false
	 *
	 * @return 成功返回数据,失败返回false
	 */
	public function get($key)
	{
		$file = $this->getFilePath($key);
		if (!is_file($file))
		{
			return false;
		}
		$str = file_get_contents($file);
		$value = substr($str, 13);
		if ($this->useSerialize)
		{
			$value = unserialize($value);
		}
		return $value;
	}

	/**
	 * key不存在 返回false
	 * 不管有没有过期,都更新数据
	 *
	 * @return bool
	 */
	public function update($key, $value)
	{
		$file = $this->getFilePath($key);
		if (!is_file($file))
		{
			return false;
		}
		else
		{
			if ($this->useSerialize)
			{
				$value = serialize($value);
			}
			$length = file_put_contents($file, '<?php exit;?>' . $value);
			return $length > 0 ? true : false;
		}
	}

	public function getFilePath($key)
	{
		$token = md5($key);
		return $this->storeDir .
		$this->prefix . '/' .
		substr($token, 0, 2) .'/' .
		substr($token, 2, 2) . '/' .
		$token . '.php';
	}
}
