<?php
class LtDbSqlAdapterMysql implements LtDbSqlAdapter
{
	public function setCharset($charset)
	{
		return "SET NAMES " . str_replace('-', '', $charset);
	}
	public function setSchema($schema)
	{
		return "USE $schema";
	}

	public function showSchemas($database)
	{
		return "SHOW DATABASES";
	}
	public function showTables($schema)
	{
		return "SHOW TABLES";
	}
	public function showFields($table)
	{
		return "DESCRIBE $table";
	}

	public function beginTransaction()
	{
		return "START TRANSACTION";
	}
	public function commit()
	{
		return "COMMIT";
	}
	public function rollBack()
	{
		return "ROLLBACK";
	}

	public function limit($limit, $offset)
	{
		return " LIMIT $limit OFFSET $offset";
	}
	public function getSchemas($queryResult)
	{

	}
	public function getTables($queryResult)
	{

	}
	public function getFields($queryResult)
	{
		foreach ($queryResult as $key => $value)
		{
			$fields[$value['Field']]['name'] = $value['Field'];
			$fields[$value['Field']]['type'] = $value['Type'];
			/*
			 * not null is NO or empty, null is YES
			 */
			$fields[$value['Field']]['notnull'] = (bool) ($value['Null'] != 'YES');
			$fields[$value['Field']]['default'] = $value['Default'];
			$fields[$value['Field']]['primary'] = (strtolower($value['Key']) == 'pri');
		}
		return $fields;
	}
	public function detectQueryType($sql)
	{
		if (preg_match("/^\s*SELECT|^\s*EXPLAIN|^\s*SHOW|^\s*DESCRIBE/i", $sql))
		{
			$ret = 'SELECT';
		}
		else if (preg_match("/^\s*INSERT/i", $sql))
		{
			$ret = 'INSERT';
		}
		else if (preg_match("/^\s*UPDATE|^\s*DELETE|^\s*REPLACE/i", $sql))
		{
			$ret = 'CHANGE_ROWS';
		}
		else if (preg_match("/^\s*USE|^\s*SET/i", $sql))
		{
			$ret = 'SET_SESSION_VAR';
		}
		else
		{
			$ret = 'OTHER';
		}
		return $ret;
	}
}