<?php
interface LtDbConnectionAdapter
{
	/**
	 * @todo 兼容使用Unix Domain Socket方式连接数据库（即：可以不指定port）
	 */
	public function connect($connConf);
	public function exec($sql, $connResource);
	public function query($sql, $connResource);
	public function lastInsertId($connResource);
	public function escape($sql, $connResource);
}