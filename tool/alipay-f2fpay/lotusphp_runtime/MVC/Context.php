<?php
class LtContext
{
	/**
	 * The uri property
	 * 
	 * @var array 
	 */
	public $uri;

	protected $strip;

	public function __construct()
	{

	}

	/**
	 * return the client input in $_SERVER['argv']
	 * 
	 * @param integer $offset 
	 * @return string 
	 */
	public function argv($offset)
	{
		return isset($_SERVER['argv']) && isset($_SERVER['argv'][$offset]) ? $_SERVER['argv'][$offset] : null;
	}

	/**
	 * return the client input in $_FILES
	 * 
	 * @param string $name 
	 * @return array 
	 */
	public function file($name)
	{
		return isset($_FILES[$name]) ? $_FILES[$name] : null;
	}

	/**
	 * return the client input in $_GET
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function get($name)
	{
		return isset($_GET[$name]) ? $_GET[$name] : null;
	}

	/**
	 * return the client input in $_POST
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function post($name)
	{
		return isset($_POST[$name]) ? $_POST[$name] : null;
	}

	/**
	 * return the client input in $_REQUEST
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function request($name)
	{
		return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
	}

	/**
	 * return the client input in $_SERVER
	 * 
	 * @param string $name 
	 * @return string 
	 */
	public function server($name)
	{
		if ('REMOTE_ADDR' == $name)
		{
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				$clientIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else
			{
				$clientIp = $_SERVER[$name];
			}
			return $clientIp;
		}
		else
		{
			return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
		}
	}
}
