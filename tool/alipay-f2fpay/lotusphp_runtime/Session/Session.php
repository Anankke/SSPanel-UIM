<?php
class LtSession
{
	public $storeHandle;
	public $configHandle;

	public function __construct()
	{
		if (! $this->configHandle instanceof LtConfig)
		{
			if (class_exists("LtObjectUtil", false))
			{
				$this->configHandle = LtObjectUtil::singleton("LtConfig");
			}
			else
			{
				$this->configHandle = new LtConfig;
			}
		}
	}

	public function init()
	{
		if(!$sessionSavePath = $this->configHandle->get("session.save_path"))
		{
			$sessionSavePath = '/tmp/Lotus/session/';
		}
		if (!is_object($this->storeHandle))
		{
			ini_set('session.save_handler', 'files');
			if (!is_dir($sessionSavePath))
			{
				if (!@mkdir($sessionSavePath, 0777, true))
				{
					trigger_error("Can not create $sessionSavePath");
				}
			}
			session_save_path($sessionSavePath);
		}
		else
		{
			$this->storeHandle->conf = $this->configHandle->get("session.conf");
			$this->storeHandle->init();
			session_set_save_handler(
				array(&$this->storeHandle, 'open'), 
				array(&$this->storeHandle, 'close'),
				array(&$this->storeHandle, 'read'), 
				array(&$this->storeHandle, 'write'), 
				array(&$this->storeHandle, 'destroy'), 
				array(&$this->storeHandle, 'gc')
				);
		}
		//session_start();
		//header("Cache-control: private"); // to overcome/fix a bug in IE 6.x
	}
}
