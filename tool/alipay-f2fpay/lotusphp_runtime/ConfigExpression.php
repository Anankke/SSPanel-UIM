<?php
class LtConfigExpression
{
	private $_expression;
	public $autoRetrived;
	
	public function __construct($string, $autoRetrived = true)
	{
		$this->_expression = (string) $string;
		$this->autoRetrived = $autoRetrived;
	}
	
	public function __toString()
	{
		return $this->_expression;
	}
}