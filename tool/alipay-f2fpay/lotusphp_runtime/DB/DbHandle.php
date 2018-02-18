<?php
class LtDbHandle
{
	public $configHandle;
	public $group;
	public $node;
	public $role = "master";
	public $connectionAdapter;
	public $connectionResource;
	public $sqlAdapter;
	protected $connectionManager;
	private $servers;

	public function __construct()
	{
	}

	public function init()
	{
		if(empty($this->servers))
		{
			$this->servers = $this->configHandle->get("db.servers");
		}
		$this->connectionManager = new LtDbConnectionManager;
		$this->connectionManager->configHandle = $this->configHandle;
		$this->sqlAdapter = $this->getCurrentSqlAdapter();
		$connectionInfo = $this->connectionManager->getConnection($this->group, $this->node, $this->role);
		$this->connectionAdapter = $connectionInfo["connectionAdapter"];
		$this->connectionResource = $connectionInfo["connectionResource"];
	}

	/**
	 * Trancaction methods
	 */
	public function beginTransaction()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->beginTransaction(), $this->connectionResource);
	}

	public function commit()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->commit(), $this->connectionResource);
	}

	public function rollBack()
	{
		return $this->connectionAdapter->exec($this->sqlAdapter->rollBack(), $this->connectionResource);
	}

	/**
	 * Execute an sql query
	 * 
	 * @param  $sql 
	 * @param  $bind 
	 * @param  $forceUseMaster 
	 * @return false on query failed
	 *           --sql type--                         --return value--
	 *           SELECT, SHOW, DESECRIBE, EXPLAIN     rowset or NULL when no record found
	 *           INSERT                               the ID generated for an AUTO_INCREMENT column
	 *           UPDATE, DELETE, REPLACE              affected count
	 *           USE, DROP, ALTER, CREATE, SET etc    true
	 * @notice 每次只能执行一条SQL
	 *           不要通过此接口执行USE DATABASE, SET NAMES这样的语句
	 */
	public function query($sql, $bind = null, $forceUseMaster = false)
	{
		$sql = trim($sql);
		if (empty($sql))
		{
			trigger_error('Empty the SQL statement');
		}
		$queryType = $this->sqlAdapter->detectQueryType($sql);
		switch ($queryType)
		{
			case "SELECT":
				if (!$forceUseMaster && isset($this->servers[$this->group][$this->node]["slave"]))
				{
					$this->role = "slave";
				}
				$queryMethod = "select";
				break;
			case "INSERT":
				$this->role = "master";
				$queryMethod = "insert";
				break;
			case "CHANGE_ROWS":
				$this->role = "master";
				$queryMethod = "changeRows";
				break;
			case "SET_SESSION_VAR":
				$queryMethod = "setSessionVar";
				break;
			case "OTHER":
			default:
				$this->role = "master";
				$queryMethod = "other";
				break;
		}
		$connectionInfo = $this->connectionManager->getConnection($this->group, $this->node, $this->role);
		$this->connectionAdapter = $connectionInfo["connectionAdapter"];
		$this->connectionResource = $connectionInfo["connectionResource"];
		if (is_array($bind) && 0 < count($bind))
		{
			$sql = $this->bindParameter($sql, $bind);
		}
		return $this->$queryMethod($sql, $this->connectionResource);
	}
	/**
	 * function posted by renlu
	 */
	public function escape($str)
	{
		return $this->connectionAdapter->escape($str, $this->connectionResource);
	}
	/**
	 * function posted by renlu
	 */
	public function insertid()
	{
		return $this->connectionAdapter->lastInsertId($this->connectionResource);
	}
	/**
	 * Generate complete sql from sql template (with placeholder) and parameter
	 * 
	 * @param  $sql 
	 * @param  $parameter 
	 * @return string 
	 * @todo 兼容pgsql等其它数据库，pgsql的某些数据类型不接受单引号引起来的值
	 */
	public function bindParameter($sql, $parameter)
	{ 
		// 注意替换结果尾部加一个空格
		$sql = preg_replace("/:([a-zA-Z0-9_\-\x7f-\xff][a-zA-Z0-9_\-\x7f-\xff]*)\s*([,\)]?)/", "\x01\x02\x03\\1\x01\x02\x03\\2 ", $sql);
		foreach($parameter as $key => $value)
		{
			$find[] = "\x01\x02\x03$key\x01\x02\x03";
			if ($value instanceof LtDbSqlExpression)
			{
				$replacement[] = $value->__toString();
			}
			else
			{
				$replacement[] = "'" . $this->connectionAdapter->escape($value, $this->connectionResource) . "'";
			}
		}
		$sql = str_replace($find, $replacement, $sql);
		return $sql;
	}

	protected function getCurrentSqlAdapter()
	{
		$factory = new LtDbAdapterFactory;
		$host = key($this->servers[$this->group][$this->node][$this->role]);
		return $factory->getSqlAdapter($this->servers[$this->group][$this->node][$this->role][$host]["sql_adapter"]);
	}

	protected function select($sql, $connResource)
	{
		$result = $this->connectionAdapter->query($sql, $connResource);
		if (empty($result))
		{
			return null;
		}
		else
		{
			return $result;
		}
	}

	protected function insert($sql, $connResource)
	{
		if ($result = $this->connectionAdapter->exec($sql, $connResource))
		{
			return $this->connectionAdapter->lastInsertId($connResource);
		}
		else
		{
			return $result;
		}
	}

	protected function changeRows($sql, $connResource)
	{
		return $this->connectionAdapter->exec($sql, $connResource);
	}

	/**
	 * 
	 * @todo 更新连接缓存
	 */
	protected function setSessionVar($sql, $connResource)
	{
		return false === $this->connectionAdapter->exec($sql, $connResource) ? false : true;
	}

	protected function other($sql, $connResource)
	{
		return false === $this->connectionAdapter->exec($sql, $connResource) ? false : true;
	}
}
