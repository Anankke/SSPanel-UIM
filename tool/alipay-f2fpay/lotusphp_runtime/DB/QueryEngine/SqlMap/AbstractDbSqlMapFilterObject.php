<?php
/**
 * 加工工厂类由开发者自行开发，继承自这个类
 */
abstract class LtAbstractDbSqlMapFilterObject {

	// query()方法返回的结果集，用于加工的原料
	public $result;

	/**
	 * 需要被继承，实现逻辑的操作类，输入query()方法返回的结果集
	 * 经过处理后返回开发者定义的对象或结构
	 */
	abstract protected function process();
}

