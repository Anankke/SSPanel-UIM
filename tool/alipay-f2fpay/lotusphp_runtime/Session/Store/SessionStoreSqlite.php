<?php
class LtSessionSqlite implements LtSessionStore
{
	public $conf;

	private $lifeTime; //session.gc_maxlifetime
	private $dbHandle;
	private $dbName;
	private $tableName;

	public function __construct()
	{ 
		// --
	}
	public function init()
	{
		if (isset($this->conf['gc_maxlifetime']))
		{
			$this->lifeTime = $this->conf['gc_maxlifetime'];
		}
		else
		{
			$this->lifeTime = get_cfg_var("session.gc_maxlifetime");
		}
		if (isset($this->conf['table_name']))
		{
			$this->tableName = $this->conf['table_name'];
		}
		else
		{
			$this->tableName = 'lotus_session';
		}
		if (isset($this->conf['db_name']))
		{
			$this->dbName = $this->conf['db_name'];
		}
		else
		{
			$this->dbName = '/tmp/Lotus/session/session_sqlite2.db';
		}

		if (!$this->dbHandle = sqlite_open($this->dbName, 0666))
		{
			trigger_error('session sqlite db error');
			return false;
		}
		return true;
	}

	public function open($savePath, $sessName)
	{
		return true;
	}

	public function close()
	{
		$this->gc($this->lifeTime);
		return @sqlite_close($this->dbHandle);
	}

	public function read($sessID)
	{
		$res = sqlite_query("SELECT session_data AS d FROM $this->tableName
                           WHERE session_id = '$sessID'
                           AND session_expires > " . time(), $this->dbHandle);
		if ($row = sqlite_fetch_array($res, SQLITE_ASSOC))
		{
			return $row['d'];
		}
		else
		{
			return "";
		}
	}

	public function write($sessID, $sessData)
	{
		$newExp = time() + $this->lifeTime;
		$res = sqlite_query("SELECT * FROM $this->tableName
                           WHERE session_id = '$sessID'", $this->dbHandle);
		if (sqlite_num_rows($res))
		{
			sqlite_exec("UPDATE $this->tableName
                        SET session_expires = '$newExp',
                        session_data = '$sessData'
                        WHERE session_id = '$sessID'", $this->dbHandle);
			if (sqlite_changes($this->dbHandle))
			{
				return true;
			}
		}
		else
		{
			sqlite_exec("INSERT INTO $this->tableName (
                        session_id,
                        session_expires,
                        session_data)
                        VALUES(
                        '$sessID',
                        '$newExp',
                        '$sessData')", $this->dbHandle);
			if (sqlite_changes($this->dbHandle))
			{
				return true;
			}
		}
		return false;
	}

	public function destroy($sessID)
	{
		sqlite_exec("DELETE FROM $this->tableName WHERE session_id = '$sessID'", $this->dbHandle);
		if (sqlite_changes($this->dbHandle))
		{
			return true;
		}
		return false;
	}

	public function gc($sessMaxLifeTime)
	{
		sqlite_exec("DELETE FROM $this->tableName WHERE session_expires < " . time(), $this->dbHandle);
		return sqlite_changes($this->dbHandle);
	}

	public function runOnce()
	{
		$sql = "SELECT name FROM sqlite_master WHERE type='table' UNION ALL SELECT name FROM sqlite_temp_master WHERE type='table' AND name='" . $this->tableName . "'";
		$res = sqlite_query($sql, $this->dbHandle);
		$row = sqlite_fetch_array($res, SQLITE_ASSOC);
		if (empty($row))
		{
			$sql = "CREATE TABLE $this->tableName (
			[session_id] VARCHAR(255)  NOT NULL PRIMARY KEY,
			[session_expires] INTEGER DEFAULT '0' NOT NULL,
			[session_data] TEXT  NULL
		)";
			return sqlite_exec($sql, $this->dbHandle);
		}
		return false;
	}
}
