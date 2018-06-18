<?php
/**
 * The Dispatcher class
 */
class LtDispatcher
{
	public $viewDir;
	public $viewTplDir;
	public $viewTplAutoCompile;
	public $data;

	public function __construct()
	{

	}

	protected function _dispatch($module, $action, $context = null, $classType = "Action")
	{
		$classType = ucfirst($classType);
		$actionClassName = $module . $action . $classType;
		if (!class_exists($actionClassName))
		{
			//DebugHelper::debug("{$classType}_CLASS_NOT_FOUND", array(strtolower($classType) => $action));
			trigger_error("{$actionClassName} CLASS NOT FOUND! module={$module} action={$action} classType={$classType}");
		}
		else
		{
			if (!($context instanceof LtContext))
			{
				$newContext = new LtContext;
			}
			else
			{
				$newContext = clone $context;
			}
			$newContext->uri['module'] = $module;
			$newContext->uri[strtolower($classType)] = $action;
			$actionInstance = new $actionClassName();
			$actionInstance->context = $newContext;
			$actionInstance->viewDir = $this->viewDir;
			$actionInstance->viewTplDir = $this->viewTplDir; // 模板编译目录
			$actionInstance->viewTplAutoCompile = $this->viewTplAutoCompile;
			$actionInstance->executeChain();
			$this->data = $actionInstance->data;
		}
	}

	/**
	 * Disptach the module/action calling.
	 *
	 * @param $module string
	 * @param $action string
	 * @return void
	 * @todo allow one action dispatch another action
	 */
	public function dispatchAction($module, $action, $context = null)
	{
		$this->_dispatch($module, $action, $context);
	}

	/**
	 * Disptach the module/component calling.
	 *
	 * @param $module string
	 * @param $component string
	 * @param $data mixed
	 * @return void
	 */
	public function dispatchComponent($module, $component, $context = null)
	{
		$cloneOfContext = clone $context;
		$this->_dispatch($module, $component, $cloneOfContext, "Component");
	}
}
