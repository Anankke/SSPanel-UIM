<?php
class LtValidatorDtd
{
	public $label;
	public $rules;
	public $messages;

	public function __construct($label, $rules, $messages = null)
	{
		$this->label = $label;
		foreach($rules as $key => $rule)
		{
			$this->rules[$key] = $rule;
		}
		if ($messages)
		{
			foreach($messages as $key => $message)
			{
				$this->messages[$key] = $message;
			}
		}
	}
}
