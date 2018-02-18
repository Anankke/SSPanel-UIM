<?php
/**
 *返回码定义
 */
/* 扩展内部错误 */
define("INTERNAL_ERR", -1);
/* 当前模式下不允许执行该函数 */
define("WRONG_MODE", 0);
/* 成功 */
define("SUCESS", 1);

/**
 * 模式定义
 */
define("READMODE", 0);
define("WRITEMODE", 1);

/**
 * @desc LtXml用于解析和生成XML文件
 * 使用前调用 init() 方法对类进行初始化
 *
 * LtXml提供两个公共方法 getArray() 和 getString
 *
 * getArray() 方法要求传入一个规范的xml字符串，
 * 返回一个格式化的数组
 *
 * getString() 方法要求传入一个格式化的数组，反
 * 回一个规范的xml字符串
 * 在使用getString() 方法时，传入的格式化数组可
 * 通过 createTag() 方法获得。
 *
 */
class LtXml {
	/**
	 * 只支持 ISO-8859-1, UTF-8 和 US-ASCII三种编码
	 */
	private $_supportedEncoding = array("ISO-8859-1", "UTF-8", "US-ASCII");

	/**
	 * XMLParser 操作句柄
	 */
	private $_handler;

	/**
	 *     READMODE 0:读模式，encoding参数不生效，通过输入的string获取version和encoding（getString方法不可用） 
	 *     WRITEMODE 1:写模式，按照制定的encoding和array生成string（getArray方法不可用） 
	 */
	public $mode;

	/**
	 * 该 XML 对象的编码，ISO-8859-1, UTF-8（默认） 或 US-ASCII
	 */
	public $encoding;
	
	/**
	 * 该 XML 对象的版本，1.0（默认）
	 */
	public $version;

	public function init($mode = 0, $encoding = "UTF-8", $version = "1.0") {
		$this->mode = $mode;

		$this->encoding = $encoding;
		$this->version = $version;

		$this->_getParser($encoding);
	}

	public function getArray($xmlString) {
		if (READMODE !== $this->mode) {
			trigger_error("LtXml is on WRITEMODE, and cannot convert XML string to array.");
			return WRONG_MODE;
		}

		if (0 === preg_match("/version=[\"|\']([1-9]\d*\.\d*)[\"|\']/", $xmlString, $res)) {
			trigger_error("Cannot find the version in this XML document.");
			return INTERNAL_ERR;
		}
		else {
			$this->version = $res[1];
		}

		if (0 === preg_match("/encoding=[\"|\'](.*?)[\"|\']/", $xmlString, $res)) {
			$this->encoding = "UTF-8";
		}
		else {
			$this->encoding = strtoupper($res[1]);
		}

		$_array = $this->_stringToArray($xmlString);
		if (NULL === $_array) {
			trigger_error("Fail to get the tag template.");
			return INTERNAL_ERR;
		}
		$currentArray = NULL;
		$openingTags = array();
		$array = $this->_getArrayTemplate();

		foreach ($_array as $tag) {
			$tag["tag"] = strtolower($tag["tag"]);
			if (isset($tag["type"]) && "close" == $tag["type"]
					&& isset($tag["tag"]) && ! empty($tag["tag"])) {
				if ($openingTags[count($openingTags) - 1]["tag"] == $tag["tag"]) {
					unset($openingTags[count($openingTags) - 1]);
				}
				else {
					return -1;
				}
			}
			else if ((isset($tag["type"]) && "complete" == $tag["type"])
						|| (isset($tag["type"]) && "open" == $tag["type"])
						&& isset($tag["tag"]) && ! empty($tag["tag"])){
				$currentArray = $this->_getArrayTemplate();
				$currentArray["tag"] = $tag["tag"];
				$cdata = $tag["value"];
				$cdata = preg_replace("/^\s*/", "", $cdata);
				$cdata = preg_replace("/\s*$/", "", $cdata);
				$currentArray["cdata"] = $cdata;
				if (isset($tag["attributes"]) && is_array($tag["attributes"])) {
					foreach($tag["attributes"] as $k => $v) {
						$currentArray["attributes"][strtolower($k)] = $v;
					}
				}

				if (0 == count($openingTags)) {
					$openingTags[] = &$array;
					$openingTags[0] = $currentArray;
				}
				else {
					$subCount = count($openingTags[count($openingTags) - 1]["sub"]);
					$openingTags[count($openingTags) - 1]["sub"][$subCount] = $currentArray;
					$openingTags[count($openingTags)] = &$openingTags[count($openingTags) - 1]["sub"][$subCount];
				}

				if ("complete" == $tag["type"]) {
					unset($openingTags[count($openingTags) - 1]);
				}
			}
			else if (isset($tag["type"]) && "cdata" == $tag["type"]
					&& isset($tag["tag"]) && ! empty($tag["tag"])) {
				if ($tag["tag"] == $openingTags[count($openingTags) - 1]["tag"]) {
					$cdata = $tag["value"];
					$cdata = preg_replace("/^\s*/", "", $cdata);
					$cdata = preg_replace("/\s*$/", "", $cdata);
					$openingTags[count($openingTags) - 1]["cdata"] .= $cdata;
				}
				else {
					return -2;
				}
			}
		}

		if (0 < count($openingTags)) {
			return -3;
		}

		return $array;
	}

	public function getString($xmlArray) {
		if (WRITEMODE !== $this->mode) {
			trigger_error("LtXml is on READMODE, and cannot convert array to string.");
			return WRONG_MODE;
		}

		$header = "<?xml version=\"{$this->version}\" encoding=\"{$this->encoding}\"". " ?" . ">\n";

		$xmlString = $header;
		
		$processingTags = array($xmlArray);
		while (! empty($processingTags)) {
			if (! isset($processingTags[count($processingTags) -1]["close"])) {
				$tagArray = $processingTags[count($processingTags) - 1];

				if (0 === $this->_isTag($tagArray)) {
					trigger_error("The array do not match the format.");
					return INTERNAL_ERR;
				}

				$processingTags[count($processingTags) -1]["close"] = "YES";
				$tagName = $tagArray["tag"];

				$tag = "<{$tagName}";
				foreach ($tagArray["attributes"] as $key => $value) {
					$tag .= " {$key}=\"{$value}\"";
				}
				if (! empty($tagArray["sub"]) || ! empty($tagArray["cdata"])) {
					$cdata = $this->_convertEntity($tagArray["cdata"]);
					$tag .= ">\n{$cdata}\n";
					for ($i=count($tagArray["sub"]) - 1; $i>=0; $i--) {
						$subArray = $tagArray["sub"][$i];
						$processingTags[count($processingTags)] = $subArray;
					}
				}
				else {
					$processingTags[count($processingTags) - 1]["complete"] = "YES";
				}
			}
			else {
				$tag = (isset($processingTags[count($processingTags) - 1]["complete"]))
					? "/>\n"
					: "</{$processingTags[count($processingTags) - 1]["tag"]}>\n";
				unset($processingTags[count($processingTags) - 1]);
			}

			$xmlString .= $tag;
		}
		$xmlString = preg_replace("/\n\s*/", "\n", $xmlString);

		return $xmlString;
	}

	/**
	 * 生成一个xml节点
	 * @param string tag 标签名
	 * @param string cdata 数据
	 * @param array attr 属性列表
	 * @param array sub 子标签列表
	 */
	public function createTag($tag, $cdata = "", $attr = array(), $sub = array()) {
		$newTag = $this->_getArrayTemplate();
		if (! is_string($tag)) {
			trigger_error("Cannot read the tag name.");
			return INTERNAL_ERR;
		}

		$newTag["tag"] = $tag;
		$newTag["cdata"] = $cdata;
		$newTag["attributes"] = $attr;
		$newTag["sub"] = $sub;

		return $newTag;
	}

	/**
	 * 释放xml_parser
	 */
	public function free() {
		xml_parser_free($this->_handler);
	}

	private function _getParser($encoding) {
		if (in_array($encoding, $this->_supportedEncoding))
			$this->_handler = xml_parser_create($encoding);
		else
			$this->_handler = NULL;
	}

	private function _stringToArray($xmlString) {
		$res = xml_parse_into_struct($this->_handler, $xmlString, $array);
		if (1 === $res)
			return $array;
		else
			return NULL;
	}

	private function _convertEntity($string) {
		$patterns = array("/</", "/</", "/&/", "/'/", "/\"/");
		$replacement = array("&lt;", "&gt;", "&amp;", "&apos;", "&quot;");

		return preg_replace($patterns, $replacement, $string);
	}

	private function _rConvertEntity($string) {
		$patterns = array("/&lt;/", "/&gt;/", "/&amp;/", "/&apos;/", "/&quot;/");
		$replacement = array("<", "<", "&", "'", "\"");

		return preg_replace($patterns, $replacement, $string);
	}

	private function _getArrayTemplate() {
		return array("tag" => "", "attributes" => array(), "sub" => array(), "cdata" => "");
	}

	/**
	 * 检测传入的参数是否是一个合法的tag数组
	 * @return 0 非法
	 * @return 1 合法
	 */
	private function _isTag($tag) {
		if (! is_array($tag)) {
			return 0;
		}

		if (! isset($tag["tag"]) || ! is_string($tag["tag"]) || empty($tag["tag"])) {
			return 0;
		}

		if (! isset($tag["attributes"]) || ! is_array($tag["attributes"])) {
			return 0;
		}

		if (! isset($tag["sub"]) || ! is_array($tag["sub"])) {
			return 0;
		}

		if (! isset($tag["cdata"]) || ! is_string($tag["cdata"])) {
			return 0;
		}

		return 1;
	}
}

