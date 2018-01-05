<?php
class LtAutoloader
{
	public $conf = array(
	/**
	 * 是否自动加载定义了函数的文件
	 *
	 * 可选项： 
	 * # true   自动加载 
	 * # false  跳过函数，只自动加载定义了class或者interface的文件
	 */
		"load_function" => true,

	/**
	 * 要扫描的文件类型
	 *
	 * 若该属性设置为array("php","inc","php3")， 
	 * 则扩展名为"php","inc","php3"的文件会被扫描， 
	 * 其它扩展名的文件会被忽略
	 */
		"allow_file_extension" => array("php", "inc"),

	/**
	 * 不扫描的目录
	 *
	 * 若该属性设置为array(".svn", ".setting")， 
	 * 则所有名为".setting"的目录也会被忽略
	 */
		"skip_dir_names" => array(".svn"),
	);

	public $storeHandle;
	public $autoloadPath;
	protected $functionFileMapping;
	protected $fileStore;

	public function init()
	{
		if (!is_object($this->storeHandle))
		{
			$this->storeHandle = new LtStoreMemory;
			$this->fileStore = new LtStoreFile;
			$this->fileStore->prefix = 'LtAutoloader-token-cache';
			$this->fileStore->useSerialize = true;
			$this->fileStore->init();
		}
		// Whether scanning directory
		if (0 == $this->storeHandle->get(".class_total") && 0 == $this->storeHandle->get(".function_total"))
		{
			$this->storeHandle->add(".class_total", 0);
			$this->storeHandle->add(".function_total", 0);
			$this->storeHandle->add(".functions", array(), 0);
			$autoloadPath = $this->preparePath($this->autoloadPath);
			foreach($autoloadPath as $key => $path)
			{
				if (is_file($path))
				{
					$this->addFileMap($path);
					unset($autoloadPath[$key]);
				}
			}
			$this->scanDirs($autoloadPath);
			unset($autoloadPath);
		}
		// Whether loading function files
		if ($this->conf["load_function"])
		{
			$this->loadFunction();
		}
		spl_autoload_register(array($this, "loadClass"));
	}

	public function loadFunction()
	{
		if ($functionFiles = $this->storeHandle->get(".functions"))
		{
			foreach ($functionFiles as $functionFile)
			{
				include($functionFile);
			}
		}
	}

	public function loadClass($className)
	{
		if ($classFile = $this->storeHandle->get(strtolower($className)))
		{
			include($classFile);
		}
	}

	protected function convertPath($path)
	{
		$path = str_replace("\\", "/", $path);
		if (!is_readable($path))
		{
			trigger_error("Directory is not exists/readable: {$path}");
			return false;
		}
		$path = rtrim(realpath($path), '\\/');
		if ("WINNT" != PHP_OS && preg_match("/\s/i", $path))
		{
			trigger_error("Directory contains space/tab/newline is not supported: {$path}");
			return false;
		}
		return $path;
	}

	/**
	 * The string or an Multidimensional array into a one-dimensional array
	 *
	 * @param array $ or string $var
	 * @return array one-dimensional array
	 */
	protected function preparePath($var)
	{
		$ret = array();
		if (!is_array($var))
		{
			$var = array($var);
		}
		$i = 0;
		while (isset($var[$i]))
		{
			if (!is_array($var[$i]) && $path = $this->convertPath($var[$i]))
			{
				$ret[] = $path;
			}
			else
			{
				foreach($var[$i] as $v)
				{
					$var[] = $v;
				}
			}
			unset($var[$i]);
			$i ++;
		}
		return $ret;
	}

	/**
	 * Using iterative algorithm scanning subdirectories
	 * save autoloader filemap
	 *
	 * @param array $dirs one-dimensional
	 * @return
	 */
	protected function scanDirs($dirs)
	{
		$i = 0;
		while (isset($dirs[$i]))
		{
			$dir = $dirs[$i];
			$files = scandir($dir);
			foreach ($files as $file)
			{
				if (in_array($file, array(".", "..")) || in_array($file, $this->conf["skip_dir_names"]))
				{
					continue;
				}
				$currentFile = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_file($currentFile))
				{
					$this->addFileMap($currentFile);
				}
				else if (is_dir($currentFile))
				{
					// if $currentFile is a directory, pass through the next loop.
					$dirs[] = $currentFile;
				}
				else
				{
					trigger_error("$currentFile is not a file or a directory.");
				}
			} //end foreach
			unset($dirs[$i]);
			$i ++;
		} //end while
	}

	protected function parseLibNames($src)
	{
		$libNames = array();
		$tokens = token_get_all($src);
		$level = 0;
		$found = false;
		$name = '';
		foreach ($tokens as $token)
		{
			if (is_string($token))
			{
				if ('{' == $token)
				{
					$level ++;
				}
				else if ('}' == $token)
				{
					$level --;
				}
			}
			else
			{
				list($id, $text) = $token;
				if (T_CURLY_OPEN == $id || T_DOLLAR_OPEN_CURLY_BRACES == $id)
				{
					$level ++;
				}
				if (0 < $level)
				{
					continue;
				}
				switch ($id)
				{
					case T_STRING:
						if ($found)
						{
							$libNames[strtolower($name)][] = $text;
							$found = false;
						}
						break;
					case T_CLASS:
					case T_INTERFACE:
					case T_FUNCTION:
						$found = true;
						$name = $text;
						break;
				}
			}
		}
		return $libNames;
	}

	protected function addClass($className, $file)
	{
		$key = strtolower($className);
		if ($existedClassFile = $this->storeHandle->get($key))
		{
			trigger_error("duplicate class [$className] found in:\n$existedClassFile\n$file\n");
			return false;
		}
		else
		{
			$this->storeHandle->add($key, $file);
			$this->storeHandle->update(".class_total", $this->storeHandle->get(".class_total") + 1);
			return true;
		}
	}

	protected function addFunction($functionName, $file)
	{
		$functionName = strtolower($functionName);
		if (isset($this->functionFileMapping[$functionName]))
		{
			$existedFunctionFile = $this->functionFileMapping[$functionName];
			trigger_error("duplicate function [$functionName] found in:\n$existedFunctionFile\n$file\n");
			return false;
		}
		else
		{
			$this->functionFileMapping[$functionName] = $file;
			$this->storeHandle->update(".functions", array_unique(array_values($this->functionFileMapping)));
			$this->storeHandle->update(".function_total", count($this->functionFileMapping));
			return true;
		}
	}

	protected function addFileMap($file)
	{
		if (!in_array(pathinfo($file, PATHINFO_EXTENSION), $this->conf["allow_file_extension"]))
		{
			return false;
		}
		$libNames = array();
		if ($this->fileStore instanceof LtStore)
		{
			$cachedFileLastModified = (int) @filemtime($this->fileStore->getFilePath($file));
			if ($cachedFileLastModified < filemtime($file) || !is_array(($libNames = $this->fileStore->get($file))))
			{
				$libNames = $this->parseLibNames(trim(file_get_contents($file)));
				if (0 < $cachedFileLastModified)
				{
					$this->fileStore->update($file, $libNames);
				}
				else
				{
					$this->fileStore->add($file, $libNames);
				}
			}
		}
		else
		{
			$libNames = $this->parseLibNames(trim(file_get_contents($file)));
		}

		foreach ($libNames as $libType => $libArray)
		{
			$method = "function" == $libType ? "addFunction" : "addClass";
			foreach ($libArray as $libName)
			{
				$this->$method($libName, $file);
			}
		}
		return true;
	}
}
