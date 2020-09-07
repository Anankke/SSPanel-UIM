<?php

/**
 * 应用自定义配置
 *
 * PHP version 7.2+
 */

namespace App\Controllers;

use App\Utils\ConfRender;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * ConfController
 *
 * @category GeekQu
 * @package  App/Controllers/ConfController
 */
class ConfController extends BaseController
{
    /**
     * 获取匹配的节点
     *
     * @param array $Proxy 节点
     * @param array $Rule  匹配规则
     *
     * @return array|null
     */
    public static function getMatchProxy($Proxy, $Rule)
    {
        $return = null;
        switch (true) {
            case (isset($Rule['content']['class'])):
                if (in_array($Proxy['class'], $Rule['content']['class'])) {
                    if (isset($Rule['content']['regex'])) {
                        if (preg_match('/' . $Rule['content']['regex'] . '/i', $Proxy['remark'])) {
                            $return = $Proxy;
                        }
                    } else {
                        $return = $Proxy;
                    }
                }
                break;
            case (isset($Rule['content']['noclass'])):
                if (!in_array($Proxy['class'], $Rule['content']['noclass'])) {
                    if (isset($Rule['content']['regex'])) {
                        if (preg_match('/' . $Rule['content']['regex'] . '/i', $Proxy['remark'])) {
                            $return = $Proxy;
                        }
                    } else {
                        $return = $Proxy;
                    }
                }
                break;
            case (!isset($Rule['content']['class'])
                && !isset($Rule['content']['noclass'])
                && isset($Rule['content']['regex'])
                && preg_match('/' . $Rule['content']['regex'] . '/i', $Proxy['remark'])
            ):
                $return = $Proxy;
                break;
        }

        return $return;
    }

    /**
     * YAML 转数组
     *
     * @param string $Content YAML 字符串
     *
     * @return array|string
     */
    public static function YAML2Array($Content)
    {
        try {
            return Yaml::parse($Content);
        } catch (ParseException $exception) {
            return printf('无法解析 YAML 字符串: %s', $exception->getMessage());
        }
    }

    /**
     * 自定义配置文件生成 Surge 托管配置
     *
     * @param User   $User          用户
     * @param string $AllProxys     Surge 格式的全部节点
     * @param array  $Nodes         节点数组
     * @param string $SourceContent 配置内容
     *
     * @return string
     */
    public static function getSurgeConfs($User, $AllProxys, $Nodes, $Configs)
    {
        $General = (isset($Configs['General']) ? self::getSurgeConfGeneral($Configs['General']) : '');

        $Proxys = (isset($Configs['Proxy']) ? self::getSurgeConfProxy($Configs['Proxy']) : '');

        if (isset($Configs['Proxy Group'])) {
            //兼容
            $Configs['ProxyGroup'] = $Configs['Proxy Group'];
        }
        $ProxyGroups = self::getSurgeConfProxyGroup(
            $Nodes,
            $Configs['ProxyGroup']
        );
        $ProxyGroup = self::fixSurgeProxyGroup($ProxyGroups, $Configs['Checks']);
        $ProxyGroup = self::getSurgeProxyGroup2String($ProxyGroups);

        $Rule = self::getRule($Configs['Rule']);

        $Conf = [
            '#!MANAGED-CONFIG ' . $_ENV['baseUrl'] . $_SERVER['REQUEST_URI'],
            '',
            '#---------------------------------------------------#',
            '## 上次更新于：' . date("Y-m-d h:i:s"),
            '#---------------------------------------------------#',
            '',
            '[General]',
            $General,
            '',
            '[Proxy]',
            $Proxys,
            $AllProxys,
            '',
            '[Proxy Group]',
            $ProxyGroup,
            '',
            '[Rule]',
            $Rule
        ];

        return implode(PHP_EOL, $Conf);
    }

    /**
     * Surge 配置中的 General
     *
     * @param array $General Surge General 定义
     *
     * @return string
     */
    public static function getSurgeConfGeneral($General)
    {
        $return = '';
        if (count($General) != 0) {
            foreach ($General as $key => $value) {
                $return .= $key . ' = ' . $value . PHP_EOL;
            }
        }
        return $return;
    }

    /**
     * Surge 配置中的 Proxy
     *
     * @param array $Proxys 自定义配置中的额外 Proxy
     *
     * @return string
     */
    public static function getSurgeConfProxy($Proxys)
    {
        $return = '';
        if (count($Proxys) != 0) {
            foreach ($Proxys as $value) {
                if (!preg_match('/(\[General|Replica|Proxy|Proxy\sGroup|Rule|Host|URL\sRewrite|Header\sRewrite|MITM|Script\])/', $value)) {
                    $return .= $value . PHP_EOL;
                }
            }
        }
        return $return;
    }

    /**
     * Surge 配置中的 ProxyGroup
     *
     * @param array $Nodes       全部节点数组
     * @param array $ProxyGroups Surge 策略组定义
     *
     * @return array
     */
    public static function getSurgeConfProxyGroup($Nodes, $ProxyGroups)
    {
        $return = [];
        foreach ($ProxyGroups as $ProxyGroup) {
            if (in_array($ProxyGroup['type'], ['select', 'url-test', 'fallback', 'load-balance'])) {
                $proxies = [];
                if (
                    isset($ProxyGroup['content']['left-proxies'])
                    && count($ProxyGroup['content']['left-proxies']) != 0
                ) {
                    $proxies = $ProxyGroup['content']['left-proxies'];
                }
                foreach ($Nodes as $item) {
                    $item = self::getMatchProxy($item, $ProxyGroup);
                    if ($item !== null && !in_array($item['remark'], $proxies)) {
                        $proxies[] = $item['remark'];
                    }
                }
                if (isset($ProxyGroup['content']['right-proxies'])) {
                    $proxies = array_merge($proxies, $ProxyGroup['content']['right-proxies']);
                }
                $ProxyGroup['proxies'] = $proxies;
            }
            $return[] = $ProxyGroup;
        }

        return $return;
    }

    /**
     * Surge ProxyGroup 去除无用策略组
     *
     * @param array $ProxyGroups 策略组
     * @param array $checks      要检查的策略组名
     *
     * @return array
     */
    public static function fixSurgeProxyGroup($ProxyGroups, $checks)
    {
        if (count($checks) == 0) {
            return $ProxyGroups;
        }
        $clean_names = [];
        $newProxyGroups = [];
        foreach ($ProxyGroups as $ProxyGroup) {
            if (in_array($ProxyGroup['name'], $checks) && count($ProxyGroup['proxies']) == 0) {
                $clean_names[] = $ProxyGroup['name'];
                continue;
            }
            $newProxyGroups[] = $ProxyGroup;
        }
        if (count($clean_names) >= 1) {
            $ProxyGroups = $newProxyGroups;
            $newProxyGroups = [];
            foreach ($ProxyGroups as $ProxyGroup) {
                if (!in_array($ProxyGroup['name'], $checks) && $ProxyGroup['type'] != 'ssid') {
                    $newProxies = [];
                    foreach ($ProxyGroup['proxies'] as $proxie) {
                        if (!in_array($proxie, $clean_names)) {
                            $newProxies[] = $proxie;
                        }
                    }
                    $ProxyGroup['proxies'] = $newProxies;
                }
                $newProxyGroups[] = $ProxyGroup;
            }
        }

        return $newProxyGroups;
    }

    /**
     * Surge ProxyGroup 转字符串
     *
     * @param array $ProxyGroups Surge 策略组定义
     *
     * @return string
     */
    public static function getSurgeProxyGroup2String($ProxyGroups)
    {
        $return = '';
        foreach ($ProxyGroups as $ProxyGroup) {
            $str = '';
            if (in_array($ProxyGroup['type'], ['select', 'url-test', 'fallback', 'load-balance'])) {
                $proxies = implode(', ', $ProxyGroup['proxies']);
                if (in_array($ProxyGroup['type'], ['url-test', 'fallback', 'load-balance'])) {
                    $str .= ($ProxyGroup['name']
                        . ' = '
                        . $ProxyGroup['type']
                        . ', '
                        . $proxies
                        . ', url = ' . $ProxyGroup['url']
                        . ', interval = ' . $ProxyGroup['interval']);
                } else {
                    $str .= ($ProxyGroup['name']
                        . ' = '
                        . $ProxyGroup['type']
                        . ', '
                        . $proxies);
                }
            } elseif ($ProxyGroup['type'] == 'ssid') {
                $wifi = '';
                foreach ($ProxyGroup['content'] as $key => $value) {
                    $wifi .= ', "' . $key . '" = ' . $value;
                }
                $cellular = (isset($ProxyGroup['cellular'])
                    ? ', cellular = ' . $ProxyGroup['cellular']
                    : '');
                $str .= ($ProxyGroup['name']
                    . ' = '
                    . $ProxyGroup['type']
                    . ', default = '
                    . $ProxyGroup['default']
                    . $cellular
                    . $wifi);
            } else {
                $str .= '';
            }
            $return .= $str . PHP_EOL;
        }
        return $return;
    }

    /**
     * 自定义配置文件生成 Clash 配置
     *
     * @param object $User          用户
     * @param array  $AllProxys     全部节点数组
     * @param string $SourceContent 远程配置内容
     *
     * @return string
     */
    public static function getClashConfs($User, $AllProxys, $Configs)
    {
        if (isset($Configs['Proxy']) && count($Configs['Proxy']) != 0) {
            $tmpProxys = array_merge($AllProxys, $Configs['Proxy']);
        } else {
            $tmpProxys = $AllProxys;
        }
        $Proxys = [];
        foreach ($tmpProxys as $Proxy) {
            unset($Proxy['class']);
            $Proxys[] = $Proxy;
        }

        $tmp = $Configs['General'];
        $tmp['dns'] = $Configs['DNS'];
        $tmp['proxies'] = $Proxys;
        if (isset($Configs['Proxy Group'])) {
            $Configs['ProxyGroup'] = $Configs['Proxy Group'];
        }
        $tmp['proxy-groups'] = self::getClashConfProxyGroup(
            $AllProxys,
            $Configs['ProxyGroup']
        );

        $Conf = [
            '#!MANAGED-CONFIG ' . $_ENV['baseUrl'] . $_SERVER['REQUEST_URI'],
            '',
            '#---------------------------------------------------#',
            '## 上次更新于：' . date("Y-m-d h:i:s"),
            '#---------------------------------------------------#',
            '',
            Yaml::dump($tmp, 4, 2),
            '',
            'rules:',
            self::getRule($Configs['Rule'])
        ];

        return implode(PHP_EOL, $Conf);
    }

    /**
     * Clash 配置中的 ProxyGroup
     *
     * @param array $Nodes       全部节点数组
     * @param array $ProxyGroups Clash 策略组定义
     *
     * @return array
     */
    public static function getClashConfProxyGroup($Nodes, $ProxyGroups)
    {
        $return = [];
        foreach ($ProxyGroups as $ProxyGroup) {
            $tmp = [];
            if (in_array($ProxyGroup['type'], ['select', 'url-test', 'fallback', 'load-balance'])) {
                $proxies = [];
                if (
                    isset($ProxyGroup['content']['left-proxies'])
                    && count($ProxyGroup['content']['left-proxies']) != 0
                ) {
                    $proxies = $ProxyGroup['content']['left-proxies'];
                }
                foreach ($Nodes as $item) {
                    $item['remark'] = $item['name'];
                    $item = self::getMatchProxy($item, $ProxyGroup);
                    if ($item !== null && !in_array($item['name'], $proxies)) {
                        $proxies[] = $item['name'];
                    }
                }
                if (isset($ProxyGroup['content']['right-proxies'])) {
                    $proxies = array_merge($proxies, $ProxyGroup['content']['right-proxies']);
                }
                $tmp = [
                    'name' => $ProxyGroup['name'],
                    'type' => $ProxyGroup['type'],
                    'proxies' => $proxies
                ];
                if ($ProxyGroup['type'] != 'select') {
                    $tmp['url'] = $ProxyGroup['url'];
                    $tmp['interval'] = $ProxyGroup['interval'];
                }
                $return[] = $tmp;
            }
        }
        return $return;
    }

    /**
     * Clash ProxyGroup 去除无用策略组
     *
     * @param array $ProxyGroups 策略组
     * @param array $checks      要检查的策略组名
     *
     * @return array
     */
    public static function fixClashProxyGroup($ProxyGroups, $checks)
    {
        if (count($checks) == 0) {
            return $ProxyGroups;
        }
        $clean_names = [];
        $newProxyGroups = [];
        foreach ($ProxyGroups as $ProxyGroup) {
            if (in_array($ProxyGroup['name'], $checks) && count($ProxyGroup['proxies']) == 0) {
                $clean_names[] = $ProxyGroup['name'];
                continue;
            }
            $newProxyGroups[] = $ProxyGroup;
        }
        if (count($clean_names) >= 1) {
            $ProxyGroups = $newProxyGroups;
            $newProxyGroups = [];
            foreach ($ProxyGroups as $ProxyGroup) {
                if (!in_array($ProxyGroup['name'], $checks)) {
                    $newProxies = [];
                    foreach ($ProxyGroup['proxies'] as $proxie) {
                        if (!in_array($proxie, $clean_names)) {
                            $newProxies[] = $proxie;
                        }
                    }
                    $ProxyGroup['proxies'] = $newProxies;
                }
                $newProxyGroups[] = $ProxyGroup;
            }
        }

        return $newProxyGroups;
    }

    /**
     * 规则加载
     *
     * @param array $Rules 规则加载地址
     *
     * @return string
     */
    public static function getRule($Rules)
    {
        $render = ConfRender::getTemplateRender();
        return $render->fetch($Rules['source']);
    }
}
