<?php
/**
 * The View class
 */
class LtView
{
	public $layoutDir;

	public $templateDir;

	public $layout;

	public $template;

	public function render()
	{
		if (!empty($this->layout))
		{
			include($this->layoutDir . $this->layout . '.php');
		}
		else
		{
			include($this->templateDir . $this->template . '.php');
		}
	}
}
