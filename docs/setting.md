# 结构
```
CREATE TABLE `config` (
  `id` int(11) NOT NULL COMMENT '主键',
  `item` text NOT NULL COMMENT '项',
  `value` text NOT NULL COMMENT '值',
  `class` varchar(16) NOT NULL DEFAULT 'default' COMMENT '配置分类',
  `is_public` int(11) NOT NULL DEFAULT 0 COMMENT '是否为公共参数',
  `type` text NOT NULL COMMENT '值类型',
  `default` text NOT NULL COMMENT '默认值',
  `mark` text NOT NULL COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

# 说明

`id` 主键

`item` 配置项目。使用下划线命名法

`value` 配置项目值。可将内容留空（非NULL）

`class` 配置项目所属的分类名，默认值是 `default` 

`is_public` 是否是公共参数

`type` 值类型，可选：`string`、`int`、`bool`、`array`

`default` 配置项目的默认值

`mark` 备注

# 方法
使用 `use` 操作符导入
```
use App\Models\Setting;
```
## obtain
获取单个项目的配置。例如：
```
Setting::obtain('f2f_pay_app_id');
```
## getClass
获取某一分类下的所有值，返回关联数组
```
Setting::getClass('f2f');
```
调用这些值
```
$configs = Setting::getClass('f2f');

$f2f_pay_app_id = $configs['f2f_pay_app_id'];
$f2f_pay_pid = $configs['f2f_pay_pid'];
```
## getPublicConfig
为 `/src/Services/Config.php` 提供的方法，将所有被标记为是公共参数的配置项目，以关联数组的形式返回
