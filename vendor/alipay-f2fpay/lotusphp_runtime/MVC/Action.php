<?php
/**
 * The Action class
 */
abstract class LtAction
{
	/**
	 * The context object
	 * 
	 * @var object 
	 */
	public $context;

	public $viewDir;
	public $viewTplDir;
	public $viewTplAutoCompile;

	/**
	 * The dtd config for validator
	 * 
	 * @var array 
	 */
	protected $dtds = array();

	/**
	 * The Access Control List
	 * 
	 * @var array 
	 */
	protected $acl;

	/**
	 * The current user's roles
	 * 
	 * @var array 
	 */
	protected $roles = array();

	/**
	 * A flag to indicate if subclass call LtAction::__construct()
	 * 
	 * @var boolean 
	 */
	protected $constructed = false;

	/**
	 * The response type
	 * 
	 * @var string 
	 */
	protected $responseType = "html";

	/**
	 * Result properties
	 */
	protected $code;

	protected $message;

	public $data;

	protected $view;

	protected $layout;

	/**
	 * The constructor function, initialize the URI property
	 */
	public function __construct()
	{
		$this->constructed = true;
	}

	public function executeChain()
	{
		if (!$this->constructed)
		{
			//DebugHelper::debug('SUBCLASS_NOT_CALL_PARENT_CONSTRUCTOR', array('class' => $actionClassName));
			trigger_error('SUBCLASS_NOT_CALL_PARENT_CONSTRUCTOR');
		}
		$this->afterConstruct();
		$validateResult = $this->validateInput();
		if (0 == $validateResult["error_total"])
		{
			if ($this->checkPrivilege())
			{
				$this->beforeExecute();
				$this->execute();
			}
			else
			{
				$this->code = 403;
				$this->message = "Access denied";
			}
		}
		else
		{
			$this->code = 407;
			$this->message = "Invalid input";
			$this->data['error_messages'] = $validateResult["error_messages"];
		}
		$this->writeResponse();
	}

	/**
	 * Do something after subClass::__construct().
	 */
	protected function afterConstruct()
	{

	}

	/**
	 * Validate the data from client
	 * 
	 * @return array 
	 */
	protected function validateInput()
	{
		$validateResult = array("error_total" => 0, "error_messages" => array());
		if (!empty($this->dtds) && class_exists('LtValidator'))
		{
			$validator = new LtValidator;
			$validator->init();
			foreach ($this->dtds as $variable => $dtd)
			{
				$from = isset($dtd->from) ? $dtd->from : 'request';

				foreach ($dtd->rules as $ruleKey => $ruleValue)
				{
					if ($ruleValue instanceof ConfigExpression)
					{
						eval('$_ruleValue = ' . $ruleValue->__toString());
						$dtd->rules[$ruleKey] = $_ruleValue;
					}
				}
				$error_messages = $validator->validate($this->context->$from($variable), $dtd);
				if (!empty($error_messages))
				{
					$validateResult['error_total'] ++;
					$validateResult['error_messages'][$variable] = $error_messages;
				}
			}
		}
		return $validateResult;
	}

	/**
	 * Check if current user have privilege to do this
	 * 
	 * @return boolen 
	 */
	protected function checkPrivilege()
	{
		$allow = true;
		if (!empty($this->roles) && class_exists('LtRbac'))
		{
			$module = $this->context->uri["module"];
			$action = $this->context->uri["action"];
			$roles = array_merge(array("*"), $this->roles);
			$rbac = new LtRbac();
			$rbac->init();
			$allow = $rbac->checkAcl($roles, "$module/$action");
		}
		return $allow;
	}

	/**
	 * Do something before subClass::execute().
	 */
	protected function beforeExecute()
	{
	}

	protected function execute()
	{
	}

	protected function writeResponse()
	{
		switch ($this->responseType)
		{
			case 'json':
				echo json_encode(array("code" => $this->code,
						"message" => $this->message,
						"data" => $this->data
						));
				exit; //
				break;
			case 'tpl':
				if (null === $this->view)
				{
					$this->view = new LtTemplateView;
				}
				$this->view->component = false; // 是否组件
				$this->view->context = $this->context;
				$this->view->code = $this->code;
				$this->view->message = $this->message;
				$this->view->data = $this->data;
				$this->view->layoutDir = $this->viewDir . "layout/";
				$this->view->layout = $this->layout;
				$this->view->templateDir = $this->viewDir;
				$this->view->compiledDir = $this->viewTplDir;
				$this->view->autoCompile = $this->viewTplAutoCompile;
				if (empty($this->template))
				{
					$this->template = $this->context->uri["module"] . "-" . $this->context->uri["action"];
				}
				$this->view->template = $this->template;
				$this->view->render();
				break;

			case 'html':
			case 'wml':
			default:
				if (null === $this->view)
				{
					$this->view = new LtView;
				}
				$this->view->context = $this->context;
				$this->view->code = $this->code;
				$this->view->message = $this->message;
				$this->view->data = $this->data;
				$this->view->layoutDir = $this->viewDir . "layout/";
				$this->view->layout = $this->layout;
				$this->view->templateDir = $this->viewDir;
				if (empty($this->template))
				{
					$this->template = $this->context->uri["module"] . "-" . $this->context->uri["action"];
				}
				$this->view->template = $this->template;
				$this->view->render();
				break;
		}
	}
}
