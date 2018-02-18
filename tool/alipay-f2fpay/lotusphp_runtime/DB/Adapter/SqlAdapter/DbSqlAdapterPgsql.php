<?php
class LtDbSqlAdapterPgsql implements LtDbSqlAdapter
{
	public function setCharset($charset)
	{
		return "SET client_encoding TO '$charset'";
	}
	public function setSchema($schema)
	{
		return "SET search_path TO $schema";
	}

	public function beginTransaction()
	{
		return "";
	}
	public function commit()
	{
		return "";
	}
	public function rollBack()
	{
		return "";
	}

	public function showSchemas($database)
	{

	}
	public function showTables($schema)
	{
		return "SELECT case when n.nspname='public' then c.relname else n.nspname||'.'||c.relname end as relname 
				FROM pg_class c join pg_namespace n on (c.relnamespace=n.oid)
				WHERE c.relkind = 'r'
					AND n.nspname NOT IN ('information_schema','pg_catalog')
					AND n.nspname NOT LIKE 'pg_temp%'
					AND n.nspname NOT LIKE 'pg_toast%'
				ORDER BY relname";
	}
	public function showFields($table)
	{
		return "SELECT a.attnum, a.attname AS field, t.typname AS type, 
				format_type(a.atttypid, a.atttypmod) AS complete_type, 
				a.attnotnull AS isnotnull, 
				( SELECT 't' FROM pg_index 
				WHERE c.oid = pg_index.indrelid 
				AND pg_index.indkey[0] = a.attnum 
				AND pg_index.indisprimary = 't') AS pri, 
				(SELECT pg_attrdef.adsrc FROM pg_attrdef 
				WHERE c.oid = pg_attrdef.adrelid 
				AND pg_attrdef.adnum=a.attnum) AS default 
				FROM pg_attribute a, pg_class c, pg_type t 
				WHERE c.relname = '$table' 
				AND a.attnum > 0 
				AND a.attrelid = c.oid 
				AND a.atttypid = t.oid 
				ORDER BY a.attnum";
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
		
	}
	public function detectQueryType($sql)
	{
		
	}
}