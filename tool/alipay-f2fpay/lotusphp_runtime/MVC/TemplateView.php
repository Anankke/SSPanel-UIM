<?php
class LtTemplateView
{
	public $layout;
	public $layoutDir;

	public $template;
	public $templateDir;
	public $compiledDir;

	public $autoCompile; // bool
	public $component; // bool
	private $tpl_include_files;

	public function __construct()
	{
		/**
		 * 自动编译通过对比文件修改时间确定是否编译,
		 * 当禁止自动编译时, 需要手工删除编译后的文件来重新编译.
		 * 
		 * 支持component include自动编译
		 */
		$this->autoCompile = true;
		$this->component = false;
	}

	public function render()
	{
		if (empty($this->compiledDir))
		{
			$this->compiledDir = dirname($this->templateDir) . "/viewTpl/";
		}
		if (!empty($this->layout))
		{
			include $this->template(true);
		}
		else if ($this->component)
		{
			return; // 模板内使用{component module action}合并文件
		}
		else
		{
			include $this->template();
		}
	}

	/**
	 * 返回编译后的模板路径, 如果不存在则编译生成并返回路径. 
	 * 如果文件存在且允许自动编译, 则对比模板文件和编译后的文件修改时间 
	 * 当修改模板后自支重新编译
	 * 
	 * @param bool $islayout 是否使用布局
	 * @return string 返回编译后的模板路径
	 */
	public function template($islayout = false)
	{
		$this->layoutDir = rtrim($this->layoutDir, '\\/') . '/';
		$this->compiledDir = rtrim($this->compiledDir, '\\/') . '/';
		$this->templateDir = rtrim($this->templateDir, '\\/') . '/';

		if ($islayout)
		{
			$tplfile = $this->layoutDir . $this->layout . '.php';
			$objfile = $this->compiledDir . 'layout/' . $this->layout . '@' . $this->template . '.php';
		}
		else
		{
			$tplfile = $this->templateDir . $this->template . '.php';
			$objfile = $this->compiledDir . $this->template . '.php';
		}
		if (is_file($objfile))
		{
			if ($this->autoCompile)
			{
				$iscompile = true;
				$tpl_include_files = include($objfile);
				$last_modified_time = array();
				foreach($tpl_include_files as $f)
				{
					$last_modified_time[] = filemtime($f);
				}
				if (filemtime($objfile) == max($last_modified_time))
				{
					$iscompile = false;
				}
			}
			else
			{
				$iscompile = false;
			}
		}
		else
		{ 
			// 目标文件不存在,编译模板
			$iscompile = true;
		}
		if ($iscompile)
		{
			$this->tpl_include_files[] = $objfile;
			$this->tpl_include_files[] = $tplfile;
			$dir = pathinfo($objfile, PATHINFO_DIRNAME);
			if (!is_dir($dir))
			{
				if (!mkdir($dir, 0777, true))
				{
					trigger_error("Can not create $dir");
				}
			}
			$str = file_get_contents($tplfile);
			if (!$str)
			{
				trigger_error('Template file Not found or have no access!', E_USER_ERROR);
			}
			$str = $this->parse($str);
			if ($this->autoCompile)
			{
				$prefix = "<?php\r\nif(isset(\$iscompile)&&true==\$iscompile)\r\nreturn " . var_export(array_unique($this->tpl_include_files), true) . ";?>";
				$prefix = preg_replace("/([\r\n])+/", "\r\n", $prefix);
				$postfix = "\r\n<!--Template compilation time : " . date('Y-m-d H:i:s') . "-->\r\n";
			}
			else
			{
				$prefix = '';
				$postfix = '';
			}
			$str = $prefix . $str . $postfix;
			if (!file_put_contents($objfile, $str))
			{
				if (file_put_contents($objfile . '.tmp', $str))
				{
					copy($objfile . '.tmp', $objfile); // win下不能重命名已经存在的文件
					unlink($objfile . '.tmp');
				}
			}
			@chmod($objfile,0777);
		}
		return $objfile;
	}

	/**
	 * 解析{}内字符串,替换php代码
	 * 
	 * @param string $str 
	 * @return string 
	 */
	protected function parse($str)
	{
		$str = $this->removeComments($str);
		$str = $this->parseIncludeComponent($str); 
		// 回车 换行
		$str = str_replace("{CR}", "<?php echo \"\\r\";?>", $str);
		$str = str_replace("{LF}", "<?php echo \"\\n\";?>", $str); 
		// if else elseif
		$str = preg_replace("/\{if\s+(.+?)\}/", "<?php if(\\1) { ?>", $str);
		$str = preg_replace("/\{else\}/", "<?php } else { ?>", $str);
		$str = preg_replace("/\{elseif\s+(.+?)\}/", "<?php } elseif (\\1) { ?>", $str);
		$str = preg_replace("/\{\/if\}/", "<?php } ?>", $str); 
		// loop
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\}/e", "\$this->addquote('<?php if(isset(\\1) && is_array(\\1)) foreach(\\1 as \\2) { ?>')", $str);
		$str = preg_replace("/\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}/e", "\$this->addquote('<?php if(isset(\\1) && is_array(\\1)) foreach(\\1 as \\2=>\\3) { ?>')", $str);
		$str = preg_replace("/\{\/loop\}/", "<?php } ?>", $str); 
		// url生成
		$str = preg_replace("/\{url\(([^}]+)\)\}/", "<?php echo LtObjectUtil::singleton('LtUrl')->generate(\\1);?>", $str); 

		// 函数
		$str = preg_replace("/\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\s*\(([^{}]*)\))\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff:]*\(([^{}]*)\))\}/", "<?php echo \$\\1;?>", $str); 
		// 变量
		/**
		 * 放弃支持$name.name.name
		 * $str = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff]+)\.([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1['\\2'];?>", $str);
		 */
		// 其它变量
		$str = preg_replace("/\{(\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{(\\$[a-zA-Z0-9_\.\[\]\'\"\$\x7f-\xff]+)\}/e", "\$this->addquote('<?php echo \\1;?>')", $str); 
		// 类->属性  类->方法
		$str = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\x7f-\xff][+\-\>\$\'\"\,\[\]\(\)a-zA-Z0-9_\x7f-\xff]+)\}/es", "\$this->addquote('<?php echo \\1;?>')", $str); 
		// 常量
		$str = preg_replace("/\{([A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str); 
		// 静态变量
		$str = preg_replace("/\{([a-zA-Z0-9_]*::?\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}/", "<?php echo \\1;?>", $str);
		$str = preg_replace("/\{([a-zA-Z0-9_]*::?\\\$[a-zA-Z0-9_\.\[\]\'\"\$\x7f-\xff]+)\}/e", "\$this->addquote('<?php echo \\1;?>')", $str); 

		// 合并相邻php标记
		$str = preg_replace("/\?\>\s*\<\?php[\r\n\t ]*/", "", $str);
		/**
		 * 删除空行
		 * Dos和windows采用回车+换行CR/LF表示下一行,
		 * 而UNIX/Linux采用换行符LF表示下一行，
		 * 苹果机(MAC OS系统)则采用回车符CR表示下一行.
		 * CR用符号 '\r'表示, 十进制ASCII代码是13, 十六进制代码为0x0D;
		 * LF使用'\n'符号表示, ASCII代码是10, 十六制为0x0A.
		 * 所以Windows平台上换行在文本文件中是使用 0d 0a 两个字节表示, 
		 * 而UNIX和苹果平台上换行则是使用0a或0d一个字节表示.
		 * 
		 * 这里统一替换成windows平台回车换行, 第二参数考虑 \\1 保持原有
		 */
		$str = preg_replace("/([\r\n])+/", "\r\n", $str); 
		// 删除第一行
		$str = preg_replace("/^[\r\n]+/", "", $str); 
		// write
		$str = trim($str);
		return $str;
	}
	/**
	 * 变量加上单引号 
	 * 如果是数字就不加单引号, 如果已经加上单引号或者双引号保持不变
	 */
	protected function addquote($var)
	{
		preg_match_all("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", $var, $vars);
		foreach($vars[1] as $k => $v)
		{
			if (is_numeric($v))
			{
				$var = str_replace($vars[0][$k], "[$v]", $var);
			}
			else
			{
				$var = str_replace($vars[0][$k], "['$v']", $var);
			}
		}
		return str_replace("\\\"", "\"", $var);
	}

	/**
	 * 模板中第一行可以写exit函数防止浏览
	 * 删除行首尾空白, html javascript css注释
	 */
	protected function removeComments($str, $clear = false)
	{
		$str = str_replace(array('<?php exit?>', '<?php exit;?>'), array('', ''), $str); 
		// 删除行首尾空白
		$str = preg_replace("/([\r\n]+)[\t ]+/s", "\\1", $str);
		$str = preg_replace("/[\t ]+([\r\n]+)/s", "\\1", $str); 
		// 删除 {} 前后的 html 注释 <!--  -->
		$str = preg_replace("/\<\!\-\-\s*\{(.+?)\}\s*\-\-\>/s", "{\\1}", $str);
		$str = preg_replace("/\<\!\-\-\s*\-\-\>/s", "", $str); 
		// 删除 html注释 存在 < { 就不删除
		$str = preg_replace("/\<\!\-\-\s*[^\<\{]*\s*\-\-\>/s", "", $str);
		if ($clear)
		{
			$str = $this->clear($str);
		}
		return $str;
	}
	/**
	 * 清除一部分 style script内的注释
	 * 多行注释内部存在 / 字符就不会清除
	 */
	protected function clear($str)
	{
		preg_match_all("|<script[^>]*>(.*)</script>|Usi", $str, $tvar);
		foreach($tvar[0] as $k => $v)
		{ 
			// 删除单行注释
			$v = preg_replace("/\/\/\s*[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/", "", $v); 
			// 删除多行注释
			$v = preg_replace("/\/\*[^\/]*\*\//s", "", $v);
			$str = str_replace($tvar[0][$k], $v, $str);
		}
		preg_match_all("|<style[^>]*>(.*)</style>|Usi", $str, $tvar);
		foreach($tvar[0] as $k => $v)
		{ 
			// 删除多行注释
			$v = preg_replace("/\/\*[^\/]*\*\//s", "", $v);
			$str = str_replace($tvar[0][$k], $v, $str);
		}
		return $str;
	}
	/**
	 * 
	 * @todo 注意相互引用的模板嵌套会导致死循环
	 */
	protected function parseIncludeComponent($str)
	{
		$count_include_component = preg_match_all("/\{include\s+(.+)\}/", $str, $tvar);
		$count_include_component += preg_match_all("/\{component\s+([a-zA-Z0-9\.\-_]+)\s+([a-zA-Z0-9\.\-_]+)\}/", $str, $tvar);
		unset($tvar);
		while ($count_include_component > 0)
		{
			$str = $this->parseInclude($str);
			$str = $this->parseComponent($str);
			$count_include_component = preg_match_all("/\{include\s+(.+)\}/", $str, $tvar);
			$count_include_component += preg_match_all("/\{component\s+([a-zA-Z0-9\.\-_]+)\s+([a-zA-Z0-9\.\-_]+)\}/", $str, $tvar);
			unset($tvar);
		}
		$str = $this->removeComments($str);
		return $str;
	}
	/**
	 * 解析多个{include path/file}合并成一个文件
	 * 
	 * @example {include 'debug_info'}
	 * {include 'debug_info.php'}
	 * {include "debug_info"}
	 * {include "debug_info.php"}
	 * {include $this->templateDir . $this->template}
	 */
	private function parseInclude($str)
	{
		$countSubTpl = preg_match_all("/\{include\s+(.+)\}/", $str, $tvar);
		while ($countSubTpl > 0)
		{
			foreach($tvar[1] as $k => $subfile)
			{
				eval("\$subfile = $subfile;");
				if (is_file($subfile))
				{
					$findfile = $subfile;
				}
				else if (is_file($subfile . '.php'))
				{
					$findfile = $subfile . '.php';
				}
				else if (is_file($this->templateDir . $subfile))
				{
					$findfile = $this->templateDir . $subfile;
				}
				else if (is_file($this->templateDir . $subfile . '.php'))
				{
					$findfile = $this->templateDir . $subfile . '.php';
				}
				else
				{
					$findfile = '';
				} 
				
				if (!empty($findfile))
				{
					$subTpl = file_get_contents($findfile);
					$this->tpl_include_files[] = $findfile;
				}
				else
				{ 
					// 找不到文件
					$subTpl = 'SubTemplate not found:' . $subfile;
				}
				$str = str_replace($tvar[0][$k], $subTpl, $str);
			}
			$countSubTpl = preg_match_all("/\{include\s+(.+)\}/", $str, $tvar);
		}
		return $str;
	}

	/**
	 * 解析多个{component module action}合并成一个文件
	 */
	private function parseComponent($str)
	{
		$countCom = preg_match_all("/\{component\s+([a-zA-Z0-9\.\-_]+)\s+([a-zA-Z0-9\.\-_]+)\}/", $str, $tvar);
		while ($countCom > 0)
		{
			$i = 0;
			while ($i < $countCom)
			{
				$comfile = $this->templateDir . "component/" . $tvar[1][$i] . '-' . $tvar[2][$i] . '.php';
				if (is_file($comfile))
				{
					$subTpl = file_get_contents($comfile);
					$this->tpl_include_files[] = $comfile;
				}
				else
				{
					$subTpl = 'SubTemplate not found:' . $comfile;
				}
////////////////////////////////////////////////////////////////////////////
$module = $tvar[1][$i];
$action = $tvar[2][$i];
$subTpl = "<?php
\$dispatcher = LtObjectUtil::singleton('LtDispatcher');
\$dispatcher->dispatchComponent('$module', '$action', \$this->context);
\$comdata = \$dispatcher->data;
unset(\$dispatcher);
?>
" . $subTpl;
////////////////////////////////////////////////////////////////////////////
				$str = str_replace($tvar[0][$i], $subTpl, $str);
				$i++;
			}
			$countCom = preg_match_all("/\{component\s+([a-zA-Z0-9\.\-_]+)\s+([a-zA-Z0-9\.\-_]+)\}/", $str, $tvar);
		}
		return $str;
	}
}
