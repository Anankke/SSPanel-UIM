<?php

require_once 'AopEncrypt.php';

class AopClient {
	//应用ID
	public $appId;
	//私钥文件路径
	public $rsaPrivateKeyFilePath;

	//私钥值
	public $rsaPrivateKey;
	//网关
	public $gatewayUrl = "https://openapi.alipay.com/gateway.do";
	//返回数据格式
	public $format = "json";
	//api版本
	public $apiVersion = "1.0";

	// 表单提交字符集编码
	public $postCharset = "UTF-8";


	public $alipayPublicKey = null;

	public $alipayrsaPublicKey;


	public $debugInfo = false;

	private $fileCharset = "UTF-8";

	private $RESPONSE_SUFFIX = "_response";

	private $ERROR_RESPONSE = "error_response";

	private $SIGN_NODE_NAME = "sign";


	//加密XML节点名称
	private $ENCRYPT_XML_NODE_NAME = "response_encrypted";

	private $needEncrypt = false;


	//签名类型
	public $signType = "RSA";


	//加密密钥和类型

	public $encryptKey;

	public $encryptType = "AES";

	protected $alipaySdkVersion = "alipay-sdk-php-20161101";

	public function generateSign($params, $signType = "RSA") {
		return $this->sign($this->getSignContent($params), $signType);
	}

	public function rsaSign($params, $signType = "RSA") {
		return $this->sign($this->getSignContent($params), $signType);
	}

	protected function getSignContent($params) {
		ksort($params);

		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);

				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}

		unset ($k, $v);
		return $stringToBeSigned;
	}

	protected function sign($data, $signType = "RSA") {
		if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
			$priKey=$this->rsaPrivateKey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}else {
			$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
			$res = openssl_get_privatekey($priKey);
		}

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 

		if ("RSA2" == $signType) {
			openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
		} else {
			openssl_sign($data, $sign, $res);
		}

		if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
			openssl_free_key($res);
		}
		$sign = base64_encode($sign);
		return $sign;
	}


	protected function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$postBodyString = "";
		$encodeArray = Array();
		$postMultipart = false;


		if (is_array($postFields) && 0 < count($postFields)) {

			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
				{

					$postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
					$encodeArray[$k] = $this->characet($v, $this->postCharset);
				} else //文件上传用multipart/form-data，否则用www-form-urlencoded
				{
					$postMultipart = true;
					$encodeArray[$k] = new \CURLFile(substr($v, 1));
				}

			}
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}

		if ($postMultipart) {

			$headers = array('content-type: multipart/form-data;charset=' . $this->postCharset . ';boundary=' . $this->getMillisecond());
		} else {

			$headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);




		$reponse = curl_exec($ch);

		if (curl_errno($ch)) {

			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}

		curl_close($ch);
		return $reponse;
	}

	protected function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}


	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
		$localIp = isset ($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "CLI";
		$logger = new LtLogger;
		$logger->conf["log_file"] = rtrim(AOP_SDK_WORK_DIR, '\\/') . '/' . "logs/aop_comm_err_" . $this->appId . "_" . date("Y-m-d") . ".log";
		$logger->conf["separator"] = "^_^";
		$logData = array(
			date("Y-m-d H:i:s"),
			$apiName,
			$this->appId,
			$localIp,
			PHP_OS,
			$this->alipaySdkVersion,
			$requestUrl,
			$errorCode,
			str_replace("\n", "", $responseTxt)
		);
		$logger->log($logData);
	}

    /**
     * 生成用于调用收银台SDK的字符串
     * @param $request SDK接口的请求参数对象
     * @return string 
     * @author guofa.tgf
     */
	public function sdkExecute($request) {
		
		$this->setupCharsets($request);

		$params['app_id'] = $this->appId;
		$params['method'] = $request->getApiMethodName();
		$params['format'] = $this->format; 
		$params['sign_type'] = $this->signType;
		$params['timestamp'] = date("Y-m-d H:i:s");
		$params['alipay_sdk'] = $this->alipaySdkVersion;
		$params['charset'] = $this->postCharset;

		$version = $request->getApiVersion();
		$params['version'] = $this->checkEmpty($version) ? $this->apiVersion : $version;

		if ($notify_url = $request->getNotifyUrl()) {
			$params['notify_url'] = $notify_url;
		}

		$dict = $request->getApiParas();
		$params['biz_content'] = $dict['biz_content'];

		ksort($params);

		$params['sign'] = $this->generateSign($params, $this->signType);

		foreach ($params as &$value) {
			$value = $this->characet($value, $params['charset']);
		}
		
		return http_build_query($params);
	}

	/*
		页面提交执行方法
		@param：跳转类接口的request; $httpmethod 提交方式。两个值可选：post、get
		@return：构建好的、签名后的最终跳转URL（GET）或String形式的form（POST）
		auther:笙默
	*/
	public function pageExecute($request,$httpmethod = "POST") {

		$this->setupCharsets($request);

		if (strcasecmp($this->fileCharset, $this->postCharset)) {

			// writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
			throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
		}

		$iv=null;

		if(!$this->checkEmpty($request->getApiVersion())){
			$iv=$request->getApiVersion();
		}else{
			$iv=$this->apiVersion;
		}

		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();
		$sysParams["notify_url"] = $request->getNotifyUrl();
		$sysParams["return_url"] = $request->getReturnUrl();
		$sysParams["charset"] = $this->postCharset;

		//获取业务参数
		$apiParams = $request->getApiParas();

		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){

			$sysParams["encrypt_type"] = $this->encryptType;

			if ($this->checkEmpty($apiParams['biz_content'])) {

				throw new Exception(" api request Fail! The reason : encrypt request is not supperted!");
			}

			if ($this->checkEmpty($this->encryptKey) || $this->checkEmpty($this->encryptType)) {

				throw new Exception(" encryptType and encryptKey must not null! ");
			}

			if ("AES" != $this->encryptType) {

				throw new Exception("加密类型只支持AES");
			}

			// 执行加密
			$enCryptContent = encrypt($apiParams['biz_content'], $this->encryptKey);
			$apiParams['biz_content'] = $enCryptContent;

		}

		//print_r($apiParams);
		$totalParams = array_merge($apiParams, $sysParams);
		
		//待签名字符串
		$preSignStr = $this->getSignContent($totalParams);

		//签名
		$totalParams["sign"] = $this->generateSign($totalParams, $this->signType);

		if ("GET" == $httpmethod) {

			//拼接GET请求串
			$requestUrl = $this->gatewayUrl."?".$preSignStr."&sign=".urlencode($totalParams["sign"]);
			
			return $requestUrl;
		} else {
			//拼接表单字符串
			return $this->buildRequestForm($totalParams);
		}


	}

	/**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @return 提交表单HTML文本
     */
	protected function buildRequestForm($para_temp) {
		
		$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gatewayUrl."?charset=".trim($this->postCharset)."' method='POST'>";
		while (list ($key, $val) = each ($para_temp)) {
			if (false === $this->checkEmpty($val)) {
				//$val = $this->characet($val, $this->postCharset);
				$val = str_replace("'","&apos;",$val);
				//$val = str_replace("\"","&quot;",$val);
				$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
			}
        }

		//submit按钮控件请不要含有name属性
        $sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";
		
		$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";
		
		return $sHtml;
	}


	public function execute($request, $authToken = null, $appInfoAuthtoken = null) {

		$this->setupCharsets($request);

		//		//  如果两者编码不一致，会出现签名验签或者乱码
		if (strcasecmp($this->fileCharset, $this->postCharset)) {

			// writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
			throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
		}

		$iv = null;

		if (!$this->checkEmpty($request->getApiVersion())) {
			$iv = $request->getApiVersion();
		} else {
			$iv = $this->apiVersion;
		}


		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["auth_token"] = $authToken;
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();
		$sysParams["notify_url"] = $request->getNotifyUrl();
		$sysParams["charset"] = $this->postCharset;
		$sysParams["app_auth_token"] = $appInfoAuthtoken;


		//获取业务参数
		$apiParams = $request->getApiParas();

			if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){

			$sysParams["encrypt_type"] = $this->encryptType;

			if ($this->checkEmpty($apiParams['biz_content'])) {

				throw new Exception(" api request Fail! The reason : encrypt request is not supperted!");
			}

			if ($this->checkEmpty($this->encryptKey) || $this->checkEmpty($this->encryptType)) {

				throw new Exception(" encryptType and encryptKey must not null! ");
			}

			if ("AES" != $this->encryptType) {

				throw new Exception("加密类型只支持AES");
			}

			// 执行加密
			$enCryptContent = encrypt($apiParams['biz_content'], $this->encryptKey);
			$apiParams['biz_content'] = $enCryptContent;

		}


		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams), $this->signType);


		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);


		//发起HTTP请求
		try {
			$resp = $this->curl($requestUrl, $apiParams);
		} catch (Exception $e) {

			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}

		//解析AOP返回结果
		$respWellFormed = false;


		// 将返回结果转换本地文件编码
		$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);



		$signData = null;

		if ("json" == $this->format) {

			$respObject = json_decode($r);
			if (null !== $respObject) {
				$respWellFormed = true;
				$signData = $this->parserJSONSignData($request, $resp, $respObject);
			}
		} else if ("xml" == $this->format) {

			$respObject = @ simplexml_load_string($resp);
			if (false !== $respObject) {
				$respWellFormed = true;

				$signData = $this->parserXMLSignData($request, $resp);
			}
		}


		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_RESPONSE_NOT_WELL_FORMED", $resp);
			return false;
		}

		// 验签
		$this->checkResponseSign($request, $signData, $resp, $respObject);

		// 解密
		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){

			if ("json" == $this->format) {


				$resp = $this->encryptJSONSignSource($request, $resp);

				// 将返回结果转换本地文件编码
				$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
				$respObject = json_decode($r);
			}else{

				$resp = $this->encryptXMLSignSource($request, $resp);

				$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
				$respObject = @ simplexml_load_string($r);

			}
		}

		return $respObject;
	}

	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	function characet($data, $targetCharset) {
		
		if (!empty($data)) {
			$fileType = $this->fileCharset;
			if (strcasecmp($fileType, $targetCharset) != 0) {
				$data = mb_convert_encoding($data, $targetCharset, $fileType);
				//				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
			}
		}


		return $data;
	}

	public function exec($paramsArray) {
		if (!isset ($paramsArray["method"])) {
			trigger_error("No api name passed");
		}
		$inflector = new LtInflector;
		$inflector->conf["separator"] = ".";
		$requestClassName = ucfirst($inflector->camelize(substr($paramsArray["method"], 7))) . "Request";
		if (!class_exists($requestClassName)) {
			trigger_error("No such api: " . $paramsArray["method"]);
		}

		$session = isset ($paramsArray["session"]) ? $paramsArray["session"] : null;

		$req = new $requestClassName;
		foreach ($paramsArray as $paraKey => $paraValue) {
			$inflector->conf["separator"] = "_";
			$setterMethodName = $inflector->camelize($paraKey);
			$inflector->conf["separator"] = ".";
			$setterMethodName = "set" . $inflector->camelize($setterMethodName);
			if (method_exists($req, $setterMethodName)) {
				$req->$setterMethodName ($paraValue);
			}
		}
		return $this->execute($req, $session);
	}

	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	/** rsaCheckV1 & rsaCheckV2
	 *  验证签名
	 *  在使用本方法前，必须初始化AopClient且传入公钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaCheckV1($params, $rsaPublicKeyFilePath,$signType='RSA') {
		$sign = $params['sign'];
		$params['sign_type'] = null;
		$params['sign'] = null;
		return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath,$signType);
	}
	public function rsaCheckV2($params, $rsaPublicKeyFilePath, $signType='RSA') {
		print_r($params);
		$sign = $params['sign'];
		$params['sign'] = null;
		return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath, $signType);
	}

	function verify($data, $sign, $rsaPublicKeyFilePath, $signType = 'RSA') {

		if($this->checkEmpty($this->alipayPublicKey)){

			$pubKey= $this->alipayrsaPublicKey;
			$res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
		}else {
			//读取公钥文件
			$pubKey = file_get_contents($rsaPublicKeyFilePath);
			//转换为openssl格式密钥
			$res = openssl_get_publickey($pubKey);
		}

		($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');  

		//调用openssl内置方法验签，返回bool值

		if ("RSA2" == $signType) {
			$result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
		} else {
			$result = (bool)openssl_verify($data, base64_decode($sign), $res);
		}

		if(!$this->checkEmpty($this->alipayPublicKey)) {
			//释放资源
			openssl_free_key($res);
		}

		return $result;
	}

	public function checkSignAndDecrypt($params, $rsaPublicKeyPem, $rsaPrivateKeyPem, $isCheckSign, $isDecrypt) {
		$charset = $params['charset'];
		$bizContent = $params['biz_content'];
		if ($isCheckSign) {
			if (!$this->rsaCheckV2($params, $rsaPublicKeyPem)) {
				echo "<br/>checkSign failure<br/>";
				exit;
			}
		}
		if ($isDecrypt) {
			return $this->rsaDecrypt($bizContent, $rsaPrivateKeyPem, $charset);
		}

		return $bizContent;
	}

	public function encryptAndSign($bizContent, $rsaPublicKeyPem, $rsaPrivateKeyPem, $charset, $isEncrypt, $isSign) {
		// 加密，并签名
		if ($isEncrypt && $isSign) {
			$encrypted = $this->rsaEncrypt($bizContent, $rsaPublicKeyPem, $charset);
			$sign = $this->sign($bizContent);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>RSA</encryption_type><sign>$sign</sign><sign_type>RSA</sign_type></alipay>";
			return $response;
		}
		// 加密，不签名
		if ($isEncrypt && (!$isSign)) {
			$encrypted = $this->rsaEncrypt($bizContent, $rsaPublicKeyPem, $charset);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>RSA</encryption_type></alipay>";
			return $response;
		}
		// 不加密，但签名
		if ((!$isEncrypt) && $isSign) {
			$sign = $this->sign($bizContent);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$bizContent</response><sign>$sign</sign><sign_type>RSA</sign_type></alipay>";
			return $response;
		}
		// 不加密，不签名
		$response = "<?xml version=\"1.0\" encoding=\"$charset\"?>$bizContent";
		return $response;
	}

	public function rsaEncrypt($data, $rsaPublicKeyPem, $charset) {
		//读取公钥文件
		$pubKey = file_get_contents($rsaPublicKeyPem);
		//转换为openssl格式密钥
		$res = openssl_get_publickey($pubKey);
		$blocks = $this->splitCN($data, 0, 30, $charset);
		$chrtext  = null;
		$encodes  = array();
		foreach ($blocks as $n => $block) {
			if (!openssl_public_encrypt($block, $chrtext , $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$encodes[] = $chrtext ;
		}
		$chrtext = implode(",", $encodes);

		return $chrtext;
	}

	public function rsaDecrypt($data, $rsaPrivateKeyPem, $charset) {
		//读取私钥文件
		$priKey = file_get_contents($rsaPrivateKeyPem);
		//转换为openssl格式密钥
		$res = openssl_get_privatekey($priKey);
		$decodes = explode(',', $data);
		$strnull = "";
		$dcyCont = "";
		foreach ($decodes as $n => $decode) {
			if (!openssl_private_decrypt($decode, $dcyCont, $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$strnull .= $dcyCont;
		}
		return $strnull;
	}

	function splitCN($cont, $n = 0, $subnum, $charset) {
		//$len = strlen($cont) / 3;
		$arrr = array();
		for ($i = $n; $i < strlen($cont); $i += $subnum) {
			$res = $this->subCNchar($cont, $i, $subnum, $charset);
			if (!empty ($res)) {
				$arrr[] = $res;
			}
		}

		return $arrr;
	}

	function subCNchar($str, $start = 0, $length, $charset = "gbk") {
		if (strlen($str) <= $length) {
			return $str;
		}
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
		return $slice;
	}

	function parserResponseSubCode($request, $responseContent, $respObject, $format) {

		if ("json" == $format) {

			$apiName = $request->getApiMethodName();
			$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
			$errorNodeName = $this->ERROR_RESPONSE;

			$rootIndex = strpos($responseContent, $rootNodeName);
			$errorIndex = strpos($responseContent, $errorNodeName);

			if ($rootIndex > 0) {
				// 内部节点对象
				$rInnerObject = $respObject->$rootNodeName;
			} elseif ($errorIndex > 0) {

				$rInnerObject = $respObject->$errorNodeName;
			} else {
				return null;
			}

			// 存在属性则返回对应值
			if (isset($rInnerObject->sub_code)) {

				return $rInnerObject->sub_code;
			} else {

				return null;
			}


		} elseif ("xml" == $format) {

			// xml格式sub_code在同一层级
			return $respObject->sub_code;

		}


	}

	function parserJSONSignData($request, $responseContent, $responseJSON) {

		$signData = new SignData();

		$signData->sign = $this->parserJSONSign($responseJSON);
		$signData->signSourceData = $this->parserJSONSignSource($request, $responseContent);


		return $signData;

	}

	function parserJSONSignSource($request, $responseContent) {

		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;

		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);


		if ($rootIndex > 0) {

			return $this->parserJSONSource($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserJSONSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	function parserJSONSource($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
		$signIndex = strpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		$indexLen = $signDataEndIndex - $signDataStartIndex;
		if ($indexLen < 0) {

			return null;
		}

		return substr($responseContent, $signDataStartIndex, $indexLen);

	}

	function parserJSONSign($responseJSon) {

		return $responseJSon->sign;
	}

	function parserXMLSignData($request, $responseContent) {


		$signData = new SignData();

		$signData->sign = $this->parserXMLSign($responseContent);
		$signData->signSourceData = $this->parserXMLSignSource($request, $responseContent);


		return $signData;


	}

	function parserXMLSignSource($request, $responseContent) {


		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;


		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		//		$this->echoDebug("<br/>rootNodeName:" . $rootNodeName);
		//		$this->echoDebug("<br/> responseContent:<xmp>" . $responseContent . "</xmp>");


		if ($rootIndex > 0) {

			return $this->parserXMLSource($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserXMLSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	function parserXMLSource($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 1;
		$signIndex = strpos($responseContent, "<" . $this->SIGN_NODE_NAME . ">");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		$indexLen = $signDataEndIndex - $signDataStartIndex + 1;

		if ($indexLen < 0) {
			return null;
		}


		return substr($responseContent, $signDataStartIndex, $indexLen);


	}

	function parserXMLSign($responseContent) {
		$signNodeName = "<" . $this->SIGN_NODE_NAME . ">";
		$signEndNodeName = "</" . $this->SIGN_NODE_NAME . ">";

		$indexOfSignNode = strpos($responseContent, $signNodeName);
		$indexOfSignEndNode = strpos($responseContent, $signEndNodeName);


		if ($indexOfSignNode < 0 || $indexOfSignEndNode < 0) {
			return null;
		}

		$nodeIndex = ($indexOfSignNode + strlen($signNodeName));

		$indexLen = $indexOfSignEndNode - $nodeIndex;

		if ($indexLen < 0) {
			return null;
		}

		// 签名
		return substr($responseContent, $nodeIndex, $indexLen);

	}

	/**
	 * 验签
	 * @param $request
	 * @param $signData
	 * @param $resp
	 * @param $respObject
	 * @throws Exception
	 */
	public function checkResponseSign($request, $signData, $resp, $respObject) {

		if (!$this->checkEmpty($this->alipayPublicKey) || !$this->checkEmpty($this->alipayrsaPublicKey)) {


			if ($signData == null || $this->checkEmpty($signData->sign) || $this->checkEmpty($signData->signSourceData)) {

				throw new Exception(" check sign Fail! The reason : signData is Empty");
			}


			// 获取结果sub_code
			$responseSubCode = $this->parserResponseSubCode($request, $resp, $respObject, $this->format);


			if (!$this->checkEmpty($responseSubCode) || ($this->checkEmpty($responseSubCode) && !$this->checkEmpty($signData->sign))) {

				$checkResult = $this->verify($signData->signSourceData, $signData->sign, $this->alipayPublicKey, $this->signType);


				if (!$checkResult) {

					if (strpos($signData->signSourceData, "\\/") > 0) {

						$signData->signSourceData = str_replace("\\/", "/", $signData->signSourceData);

						$checkResult = $this->verify($signData->signSourceData, $signData->sign, $this->alipayPublicKey, $this->signType);

						if (!$checkResult) {
							throw new Exception("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
						}

					} else {

						throw new Exception("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
					}

				}
			}


		}
	}

	private function setupCharsets($request) {
		if ($this->checkEmpty($this->postCharset)) {
			$this->postCharset = 'UTF-8';
		}
		$str = preg_match('/[\x80-\xff]/', $this->appId) ? $this->appId : print_r($request, true);
		$this->fileCharset = mb_detect_encoding($str, "UTF-8, GBK") == 'UTF-8' ? 'UTF-8' : 'GBK';
	}

	// 获取加密内容

	private function encryptJSONSignSource($request, $responseContent) {

		$parsetItem = $this->parserEncryptJSONSignSource($request, $responseContent);

		$bodyIndexContent = substr($responseContent, 0, $parsetItem->startIndex);
		$bodyEndContent = substr($responseContent, $parsetItem->endIndex, strlen($responseContent) + 1 - $parsetItem->endIndex);

		$bizContent = decrypt($parsetItem->encryptContent, $this->encryptKey);
		return $bodyIndexContent . $bizContent . $bodyEndContent;

	}


	private function parserEncryptJSONSignSource($request, $responseContent) {

		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;

		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);


		if ($rootIndex > 0) {

			return $this->parserEncryptJSONItem($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserEncryptJSONItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}


	private function parserEncryptJSONItem($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
		$signIndex = strpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;

		if ($signDataEndIndex < 0) {

			$signDataEndIndex = strlen($responseContent)-1 ;
		}

		$indexLen = $signDataEndIndex - $signDataStartIndex;

		$encContent = substr($responseContent, $signDataStartIndex+1, $indexLen-2);


		$encryptParseItem = new EncryptParseItem();

		$encryptParseItem->encryptContent = $encContent;
		$encryptParseItem->startIndex = $signDataStartIndex;
		$encryptParseItem->endIndex = $signDataEndIndex;

		return $encryptParseItem;

	}

	// 获取加密内容

	private function encryptXMLSignSource($request, $responseContent) {

		$parsetItem = $this->parserEncryptXMLSignSource($request, $responseContent);

		$bodyIndexContent = substr($responseContent, 0, $parsetItem->startIndex);
		$bodyEndContent = substr($responseContent, $parsetItem->endIndex, strlen($responseContent) + 1 - $parsetItem->endIndex);
		$bizContent = decrypt($parsetItem->encryptContent, $this->encryptKey);

		return $bodyIndexContent . $bizContent . $bodyEndContent;

	}

	private function parserEncryptXMLSignSource($request, $responseContent) {


		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;


		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		//		$this->echoDebug("<br/>rootNodeName:" . $rootNodeName);
		//		$this->echoDebug("<br/> responseContent:<xmp>" . $responseContent . "</xmp>");


		if ($rootIndex > 0) {

			return $this->parserEncryptXMLItem($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserEncryptXMLItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	private function parserEncryptXMLItem($responseContent, $nodeName, $nodeIndex) {

		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 1;

		$xmlStartNode="<".$this->ENCRYPT_XML_NODE_NAME.">";
		$xmlEndNode="</".$this->ENCRYPT_XML_NODE_NAME.">";

		$indexOfXmlNode=strpos($responseContent,$xmlEndNode);
		if($indexOfXmlNode<0){

			$item = new EncryptParseItem();
			$item->encryptContent = null;
			$item->startIndex = 0;
			$item->endIndex = 0;
			return $item;
		}

		$startIndex=$signDataStartIndex+strlen($xmlStartNode);
		$bizContentLen=$indexOfXmlNode-$startIndex;
		$bizContent=substr($responseContent,$startIndex,$bizContentLen);

		$encryptParseItem = new EncryptParseItem();
		$encryptParseItem->encryptContent = $bizContent;
		$encryptParseItem->startIndex = $signDataStartIndex;
		$encryptParseItem->endIndex = $indexOfXmlNode+strlen($xmlEndNode);

		return $encryptParseItem;

	}


	function echoDebug($content) {

		if ($this->debugInfo) {
			echo "<br/>" . $content;
		}

	}


}