<?php
class LtDb
{
	public $configHandle;

	public $group;
	public $node;
	protected $dbh;

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
		$this->dbh = new LtDbHandle;
		$this->dbh->configHandle = $this->configHandle;
		$this->dbh->group = $this->getGroup();
		$this->dbh->node = $this->getNode();
		$this->dbh->init();
	}

	public function getDbHandle()
	{
		return $this->dbh;
	}

	public function getTDG($tableName)
	{
		$tg = new LtDbTableDataGateway;
		$tg->configHandle = $this->configHandle;
		$tg->tableName = $tableName;
		$tg->createdColumn = 'created';
		$tg->modifiedColumn = 'modified';
		$tg->dbh = $this->dbh;
		return $tg;
	}

	public function getSqlMapClient()
	{
		$smc = new LtDbSqlMapClient;
		$smc->configHandle = $this->configHandle;
		$smc->dbh = $this->dbh;
		return $smc;
	}

	public function changeNode($node)
	{
		$this->node = $node;
		$this->dbh->node = $node;
	}

	protected function getGroup()
	{
		if ($this->group)
		{
			return $this->group;
		}
		$servers = $this->configHandle->get("db.servers");
		if (1 == count($servers))
		{
			return key($servers);
		}
		return false;
	}

	protected function getNode()
	{
		if ($this->node)
		{
			return $this->node;
		}
		$servers = $this->configHandle->get("db.servers");
		if (1 == count($servers[$this->getGroup()]))
		{
			return key($servers[$this->getGroup()]);
		}
		return false;
	}
}