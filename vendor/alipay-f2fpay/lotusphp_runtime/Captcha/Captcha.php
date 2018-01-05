<?php
class LtCaptcha
{
	public $configHandle;
	public $storeHandle;

	public $imageEngine;

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
		if (!is_object($this->storeHandle))
		{
			$this->storeHandle = new LtStoreFile;
			$this->storeHandle->prefix = 'LtCaptcha-seed-';
			$this->storeHandle->init();
		}
	}

	public function getImageResource($seed)
	{
		if (empty($seed))
		{
			trigger_error("empty seed");
			return false;
		}
		if (!is_object($this->imageEngine))
		{
			if ($imageEngine = $this->configHandle->get("captcha.image_engine"))
			{
				if (class_exists($imageEngine))
				{
					$this->imageEngine = new $imageEngine;
					$this->imageEngine->conf = $this->configHandle->get("captcha.image_engine_conf");
				}
				else
				{
					trigger_error("captcha.image_engine : $imageEngine not exists");
				}
			}
			else
			{
				trigger_error("empty captcha.image_engine");
				return false;
			}
		}
		$word = $this->generateRandCaptchaWord($seed);
		$this->storeHandle->add($seed, $word);
		return $this->imageEngine->drawImage($word);
	}

	public function verify($seed, $userInput)
	{
		if ($word = $this->storeHandle->get($seed))
		{
			$this->storeHandle->del($seed);
			return $userInput === $word;
		}
		else
		{
			return false;
		}
	}

	protected function generateRandCaptchaWord()
	{
		$allowChars = $this->configHandle->get("captcha.allow_chars");
		$length = $this->configHandle->get("captcha.length");
		$allowedSymbolsLength = strlen($allowChars) - 1;
		$captchaWord = "";
		for ($i = 0; $i < $length; $i ++)
		{
			$captchaWord .= $allowChars[mt_rand(0, $allowedSymbolsLength)];
		}
		return $captchaWord;
	}
}
