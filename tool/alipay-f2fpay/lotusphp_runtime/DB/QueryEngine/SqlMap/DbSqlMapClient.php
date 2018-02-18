<?php
class LtDbSqlMapClient
{
	public $configHandle;
	public $dbh;

	public function execute($mapId, $bind = null)
	{
		$sqlMap = $this->configHandle->get($this->dbh->group . "." . $mapId);
		$forceUseMaster = isset($sqlMap["force_use_master"]) ? $sqlMap["force_use_master"] : false;
		return $this->dbh->query($sqlMap["sql"], $bind, $forceUseMaster);
	}
}

