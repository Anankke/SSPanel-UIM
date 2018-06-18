<?php

/**
 * 多媒体文件客户端
 * @author yuanwai.wang
 * @version $Id: AlipayMobilePublicMultiMediaExecute.php, v 0.1 Aug 15, 2014 10:19:01 AM yuanwai.wang Exp $
 */

//namespace alipay\api ;



class AlipayMobilePublicMultiMediaExecute{

	private $code = 200 ;
	private $msg = '';
	private $body = '';
	private $params = '';

	private $fileSuffix = array(
		"image/jpeg" 		=> 'jpg', //+
		"text/plain"		=> 'text'
	);

	/*
	 * @$header : 头部
	 * */
	function __construct( $header, $body, $httpCode ){
		$this -> code = $httpCode;
		$this -> msg = '';
		$this -> params = $header ;
		$this -> body = $body;
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getCode(){
		return $this -> code ;
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getMsg(){
		return $this -> msg ;
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getType(){
		$subject = $this -> params ;
		$pattern = '/Content\-Type:([^;]+)/';
		preg_match($pattern, $subject, $matches);
		if( $matches ){
			$type = $matches[1];
		}else{
			$type = 'application/download';
		}

		return str_replace( ' ', '', $type );
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getContentLength(){
		$subject = $this -> params ;
		$pattern = '/Content-Length:\s*([^\n]+)/';
		preg_match($pattern, $subject, $matches);
		return (int)( isset($matches[1] ) ? $matches[1]  : '' );
	}


	public function getFileSuffix( $fileType ){
		$type = isset( $this -> fileSuffix[ $fileType ] ) ? $this -> fileSuffix[ $fileType ] : 'text/plain' ;
		if( !$type ){
			$type = 'json';
		}
		return $type;
	}



	/**
	 *
	 * @return text | bin
	 */
	public function getBody(){
		//header('Content-type: image/jpeg');
		return $this -> body ;
	}

	/**
	 * 获取参数
	 * @return text | bin
	 */
	public function getParams(){
		return $this -> params ;
	}


}
