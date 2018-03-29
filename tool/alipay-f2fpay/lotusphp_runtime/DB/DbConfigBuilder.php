<?php
class LtDbConfigBuilder
{
	protected $servers = array();

	protected $tables = array();

	protected $adapters = array(
	  //"php_ext"  => array("connection_adapter" => "",        "sql_adapter" => "")
		"pgsql"      => array("connection_adapter" => "pgsql",   "sql_adapter" => "pgsql"),
		"pdo_pgsql"  => array("connection_adapter" => "pdo",     "sql_adapter" => "pgsql"),
		"oci"        => array("connection_adapter" => "oci",     "sql_adapter" => "oracle"),
		"pdo_oci"    => array("connection_adapter" => "pdo",     "sql_adapter" => "oracle"),
		"mssql"      => array("connection_adapter" => "mssql",   "sql_adapter" => "mssql"),
		"pdo_dblib"  => array("connection_adapter" => "pdo",     "sql_adapter" => "mssql"),
		"mysql"      => array("connection_adapter" => "mysql",   "sql_adapter" => "mysql"),
		"mysqli"     => array("connection_adapter" => "mysqli",  "sql_adapter" => "mysql"),
		"pdo_mysql"  => array("connection_adapter" => "pdo",     "sql_adapter" => "mysql"),
		"sqlite"     => array("connection_adapter" => "sqlite",  "sql_adapter" => "sqlite"),
		"sqlite3"    => array("connection_adapter" => "sqlite3", "sql_adapter" => "sqlite"),
		"pdo_sqlite" => array("connection_adapter" => "pdo",     "sql_adapter" => "sqlite"),
	);

	protected $defaultConfig = array(
		"host"               => "localhost",          //some ip, hostname
	//"port"             => 3306,
		"username"           => "root",
		"password"           => null,
	//"adapter"          => "mysql",              //mysql,mysqli,pdo_mysql,sqlite,pdo_sqlite
		"charset"            => "UTF-8",
		"pconnect"           => true,                 //true,false
		"connection_ttl"     => 3600,                 //any seconds
		"dbname"             => null,                 //default dbname
		"schema"             => null,                 //default schema
		"connection_adapter" => null,
		"sql_adapter"        => null,
	);

	protected $defaultAdapterConfigs = array(
		"pgsql" => array(
			"port"           => 5432,
		),
		"oracle" => array(
			"port"           => 1521,
		),
		"mssql" => array(
			"port"           => 1433,
		),
		"mysql" => array(
			"port"           => 3306,
			"pconnect"       => false,
			"connection_ttl" => 30,
		),
	);

	public function addSingleHost($hostConfig)
	{
		$this->addHost("group_0", "node_0", "master", $hostConfig);
	}

	public function addHost($groupId, $nodeId = "node_0", $role = "master", $hostConfig)
	{
		if (isset($this->servers[$groupId][$nodeId][$role]))
		{//以相同role的第一个host为默认配置
			$ref = $this->servers[$groupId][$nodeId][$role][0];
		}
		else if ("slave" == $role && isset($this->servers[$groupId][$nodeId]["master"]))
		{//slave host以master的第一个host为默认配置
			$ref = $this->servers[$groupId][$nodeId]["master"][0];
		}
		else if (isset($this->servers[$groupId]) && count($this->servers[$groupId]))
		{//以本group第一个node的master第一个host为默认配置
			$refNode = key($this->servers[$groupId]);
			$ref = $this->servers[$groupId][$refNode]["master"][0];
		}
		else
		{
			if (!isset($hostConfig["adapter"]))
			{
				trigger_error("No db adapter specified");
			}
			$ref = $this->defaultConfig;
			if (isset($this->defaultAdapterConfigs[$this->adapters[$hostConfig["adapter"]]["sql_adapter"]]))
			{
				$ref = array_merge($ref, $this->defaultAdapterConfigs[$this->adapters[$hostConfig["adapter"]]["sql_adapter"]]);
			}
		}
		$conf = array_merge($ref, $hostConfig);
		$conf = array_merge($conf, $this->adapters[$conf["adapter"]]);
		$conf = $this->convertDbnameToSchema($conf);
		$this->servers[$groupId][$nodeId][$role][] = $conf;
	}

	public function getServers()
	{
		return $this->servers;
	}

	public function getTables()
	{
		return $this->tables;
	}

	public function buildTablesConfig()
	{

	}

	/**
	 * Convert dbname to schema for: FrontBase, MySQL, mSQL, MS SQL Server, MaxDB, Sybase
	 * See: http://www.php.net/manual-lookup.php?pattern=_select_db
	 */
	protected function convertDbnameToSchema($conf)
	{
		if (preg_match("/fbsql|mysql|msql|mssql|maxdb|sybase/i", $conf["sql_adapter"]) && isset($conf["dbname"]))
		{
			$conf["schema"] = $conf["dbname"];
			$conf["dbname"] = null;
		}
		return $conf;
	}
}