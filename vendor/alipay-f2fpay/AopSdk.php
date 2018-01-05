<?php
/**
 * AOP SDK 入口文件
 * 请不要修改这个文件，除非你知道怎样修改以及怎样恢复
 * @author wuxiao
 */

/**
 * 定义常量开始
 * 在include("AopSdk.php")之前定义这些常量，不要直接修改本文件，以利于升级覆盖
 */
/**
 * SDK工作目录
 * 存放日志，AOP缓存数据
 */
if (!defined("AOP_SDK_WORK_DIR"))
{
	define("AOP_SDK_WORK_DIR", "/tmp/");
}
/**
 * 是否处于开发模式
 * 在你自己电脑上开发程序的时候千万不要设为false，以免缓存造成你的代码修改了不生效
 * 部署到生产环境正式运营后，如果性能压力大，可以把此常量设定为false，能提高运行速度（对应的代价就是你下次升级程序时要清一下缓存）
 */
if (!defined("AOP_SDK_DEV_MODE"))
{
	define("AOP_SDK_DEV_MODE", true);
}
/**
 * 定义常量结束
 */

/**
 * 找到lotusphp入口文件，并初始化lotusphp
 * lotusphp是一个第三方php框架，其主页在：lotusphp.googlecode.com
 */
$lotusHome = dirname(__FILE__) . DIRECTORY_SEPARATOR . "lotusphp_runtime" . DIRECTORY_SEPARATOR;
include($lotusHome . "Lotus.php");
$lotus = new Lotus;
$lotus->option["autoload_dir"] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'aop';
$lotus->devMode = AOP_SDK_DEV_MODE;
$lotus->defaultStoreDir = AOP_SDK_WORK_DIR;
$lotus->init();