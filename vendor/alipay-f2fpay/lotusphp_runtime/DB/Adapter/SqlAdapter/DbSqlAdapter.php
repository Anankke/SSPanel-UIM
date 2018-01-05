<?php
interface LtDbSqlAdapter
{
	/**
	 * Return SQL statements
	 */
	public function setCharset($charset);
	public function setSchema($schema);

	public function showSchemas($database);
	public function showTables($schema);
	public function showFields($table);

	public function beginTransaction();
	public function commit();
	public function rollBack();

	public function limit($limit, $offset);

	/**
	 * Retrive recordset
	 */
	public function getSchemas($queryResult);
	public function getTables($queryResult);
	public function getFields($queryResult);

	/**
	 * Parse SQL
	 */
	public function detectQueryType($sql);
}
