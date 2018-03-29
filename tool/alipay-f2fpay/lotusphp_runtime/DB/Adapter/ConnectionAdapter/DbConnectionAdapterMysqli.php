<?php
class LtDbConnectionAdapterMysqli implements LtDbConnectionAdapter
{
	public function connect($connConf)
	{
		return new mysqli($connConf["host"], $connConf["username"], $connConf["password"], $connConf["dbname"], $connConf["port"]);
	}

	public function exec($sql, $connResource)
	{
		$connResource->query($sql);
		return $connResource->affected_rows;
	}

	public function query($sql, $connResource)
	{
		$rows = array();
		$result = $connResource->query($sql);
		while($row = $result->fetch_assoc())
		{
			$rows[] = $row;
		}
		return $rows;
	}

	public function lastInsertId($connResource)
	{
		return $connResource->insert_id;
	}

	public function escape($sql, $connResource)
	{
		return mysqli_real_escape_string($connResource, $sql);
	}
}