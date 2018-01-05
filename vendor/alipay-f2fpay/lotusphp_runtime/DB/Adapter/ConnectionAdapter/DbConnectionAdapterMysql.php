<?php
class LtDbConnectionAdapterMysql implements LtDbConnectionAdapter
{
	public function connect($connConf)
	{
		return mysql_connect($connConf["host"] . ":" . $connConf["port"], $connConf["username"], $connConf["password"]);
	}

	public function exec($sql, $connResource)
	{
		return mysql_query($sql, $connResource) ? mysql_affected_rows($connResource) : false;
	}

	public function query($sql, $connResource)
	{
		$result = mysql_query($sql, $connResource);
		$rows = array();
		while($row = mysql_fetch_assoc($result))
		{
			$rows[] = $row;
		}
		return $rows;
	}

	public function lastInsertId($connResource)
	{
		return mysql_insert_id($connResource);
	}

	public function escape($sql, $connResource)
	{
		return mysql_real_escape_string($sql, $connResource);
	}
}