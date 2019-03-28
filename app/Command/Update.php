<?php

namespace App\Command;

use App\Services\Config;

use Ozdemir\Datatables\Datatables;
use App\Utils\DatatablesHelper;

class Update
{
	public static function update($xcat)
    {
        global $System_Config;
	    $copy_result=copy(BASE_PATH."/config/.config.php",BASE_PATH."/config/.config.php.bak");
		if($copy_result==true){
			echo('备份成功'.PHP_EOL);
		}
		else{
			echo('备份失败，迁移终止'.PHP_EOL);
			return false;
		}

		echo(PHP_EOL);

		echo('开始升级ssrdownload...'.PHP_EOL);
		Job::updatedownload();
		echo('升级ssrdownload结束'.PHP_EOL);

		echo('开始升级QQWry...'.PHP_EOL);
		$xcat->initQQWry();
		echo('升级QQWry结束'.PHP_EOL);

		echo(PHP_EOL);

		$config_old=file_get_contents(BASE_PATH."/config/.config.php");
		$config_new=file_get_contents(BASE_PATH."/config/.config.php.example");

		//执行版本升级
		$version_old=0;
		if(isset($System_Config['version'])){
			$version_old=$System_Config['version'];
		}		
		Update::old_to_new($version_old);

		//将旧config迁移到新config上
		$migrated=array();
		foreach($System_Config as $key => $value_reserve){
			if($key=='config_migrate_notice'||$key=='version'){
				continue;
			}

			$regex='/System_Config\[\''.$key.'\'\].*?;/s';
			$matches_new=array();
			preg_match($regex,$config_new,$matches_new);
			if(isset($matches_new[0])==false){
				echo('未找到配置项：'.$key.' 未能在新config文件中找到，可能已被更名或废弃'.PHP_EOL);
				continue;
			}

			$matches_old=array();
			preg_match($regex,$config_old,$matches_old);

			$config_new=str_replace($matches_new[0],$matches_old[0],$config_new);
			array_push($migrated,'System_Config[\''.$key.'\']');
		}
		echo(PHP_EOL);

		//检查新增了哪些config
		$regex_new='/System_Config\[\'.*?\'\]/s';
		$matches_new_all=array();
		preg_match_all($regex_new,$config_new,$matches_new_all);
		$differences=array_diff($matches_new_all[0],$migrated);
		foreach($differences as $difference){
			if($difference=='System_Config[\'config_migrate_notice\']'||
			$difference=='System_Config[\'version\']'){
				continue;
			}
			//匹配注释
			$regex_comment='/'.$difference.'.*?;.*?(?=\n)/s';
			$regex_comment=str_replace('[','\[',$regex_comment);
			$regex_comment=str_replace(']','\]',$regex_comment);
			$matches_comment=array();
			preg_match($regex_comment,$config_new,$matches_comment);
			$comment="";
			if(isset($matches_comment[0])){
				$comment=$matches_comment[0];
				$comment=substr(
					$comment,strpos(
						$comment,'//',strpos($comment,';') //查找';'之后的第一个'//'，然后substr其后面的comment
					)+2
				);
			}
			//裁去首尾
			$difference=substr($difference,15);
			$difference=substr($difference, 0, -2);

			echo('新增配置项：'.$difference.':'.$comment.PHP_EOL);
		}
		echo('新增配置项通常带有默认值，因此通常即使不作任何改动网站也可以正常运行'.PHP_EOL);

		//输出notice
		$regex_notice='/System_Config\[\'config_migrate_notice\'\].*?(?=\';)/s';
		$matches_notice=array();
		preg_match($regex_notice,$config_new,$matches_notice);
		$notice_new=$matches_notice[0];
		$notice_new=substr(
			$notice_new,strpos(
				$notice_new,'\'',strpos($notice_new,'=') //查找'='之后的第一个'\''，然后substr其后面的notice
			)+1
		);
		echo('以下是迁移附注：');
		if(isset($System_Config['config_migrate_notice'])){
		    if($System_Config['config_migrate_notice']!=$notice_new){
			    echo($notice_new);
			}
		}
		else{
			echo($notice_new);
		}
		echo(PHP_EOL);

		file_put_contents(BASE_PATH."/config/.config.php",$config_new);
		echo(PHP_EOL.'迁移完成'.PHP_EOL);

		echo(PHP_EOL);

		echo('开始升级composer依赖...'.PHP_EOL);
		system('php '.BASE_PATH.'/composer.phar selfupdate');
		system('php '.BASE_PATH.'/composer.phar install -d '.BASE_PATH);
		echo('升级composer依赖结束，请自行根据上方输出确认是否升级成功'.PHP_EOL);
		system('rm -rf '.BASE_PATH.'/storage/framework/smarty/compile/*');
		system('chown -R www:www '.BASE_PATH.'/storage');
    }

	public static function old_to_new($version_old)
	{
		if($version_old<=0){
			echo('执行升级：0 -> 1');
			$conn=mysqli_connect(Config::get('db_host'),Config::get('db_username'),Config::get('db_password'),Config::get('db_database'));
			mysqli_query($conn,'ALTER TABLE user ADD discord BIGINT NULL AFTER telegram_id');
		}
	}
}
