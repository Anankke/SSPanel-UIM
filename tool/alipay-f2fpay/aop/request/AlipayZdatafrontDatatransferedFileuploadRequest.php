<?php
/**
 * ALIPAY API: alipay.zdatafront.datatransfered.fileupload request
 *
 * @author auto create
 * @since 1.0, 2015-04-22 11:31:48
 */
class AlipayZdatafrontDatatransferedFileuploadRequest
{
	/** 
	 * 合作伙伴上传文件中的各字段,使用英文半角","分隔，file_type为json_data时必选
	 **/
	private $columns;
	
	/** 
	 * 二进制字节数组，由文件转出
	 **/
	private $file;
	
	/** 
	 * 文件描述信息，非解析数据类型必选
	 **/
	private $fileDescription;
	
	/** 
	 * 文件摘要，算法SHA
	 **/
	private $fileDigest;
	
	/** 
	 * 描述上传文件的类型
	 **/
	private $fileType;
	
	/** 
	 * 上传数据文件的主键字段，注意该pk若出现重复则后入数据会覆盖前面的，file_type为json_data时必选
	 **/
	private $primaryKey;
	
	/** 
	 * 上传数据文件包含的记录数，file_type为json_data时必选
	 **/
	private $records;
	
	/** 
	 * 外部公司的数据源标识信息，由联接网络分配
	 **/
	private $typeId;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
    private $needEncrypt=false;

	
	public function setColumns($columns)
	{
		$this->columns = $columns;
		$this->apiParas["columns"] = $columns;
	}

	public function getColumns()
	{
		return $this->columns;
	}

	public function setFile($file)
	{
		$this->file = $file;
		$this->apiParas["file"] = $file;
	}

	public function getFile()
	{
		return $this->file;
	}

	public function setFileDescription($fileDescription)
	{
		$this->fileDescription = $fileDescription;
		$this->apiParas["file_description"] = $fileDescription;
	}

	public function getFileDescription()
	{
		return $this->fileDescription;
	}

	public function setFileDigest($fileDigest)
	{
		$this->fileDigest = $fileDigest;
		$this->apiParas["file_digest"] = $fileDigest;
	}

	public function getFileDigest()
	{
		return $this->fileDigest;
	}

	public function setFileType($fileType)
	{
		$this->fileType = $fileType;
		$this->apiParas["file_type"] = $fileType;
	}

	public function getFileType()
	{
		return $this->fileType;
	}

	public function setPrimaryKey($primaryKey)
	{
		$this->primaryKey = $primaryKey;
		$this->apiParas["primary_key"] = $primaryKey;
	}

	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}

	public function setRecords($records)
	{
		$this->records = $records;
		$this->apiParas["records"] = $records;
	}

	public function getRecords()
	{
		return $this->records;
	}

	public function setTypeId($typeId)
	{
		$this->typeId = $typeId;
		$this->apiParas["type_id"] = $typeId;
	}

	public function getTypeId()
	{
		return $this->typeId;
	}

	public function getApiMethodName()
	{
		return "alipay.zdatafront.datatransfered.fileupload";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

     $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
    return $this->needEncrypt;
  }

}
