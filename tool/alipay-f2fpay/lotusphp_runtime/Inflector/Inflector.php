<?php
class LtInflector
{
	public $conf = array("separator" => "_");

	public function camelize($uncamelized_words)
	{
		$uncamelized_words = $this->conf["separator"] . str_replace($this->conf["separator"] , " ", strtolower($uncamelized_words));
		return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $this->conf["separator"] );
	}

	public function uncamelize($camelCaps)
	{
		return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $this->conf["separator"] . "$2", $camelCaps));
	}
}