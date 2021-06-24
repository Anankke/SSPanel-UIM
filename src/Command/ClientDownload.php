<?php

namespace App\Command;

use Exception;

/**
 * 世界这么大，何必要让它更艰难呢？
 *
 * By GeekQuerxy
 */
class ClientDownload extends Command
{
    public $description   = '├─=: php xcat ClientDownload - 定时更新客户端' . PHP_EOL;

    private $client;

    /**
     * 保存基本路径
     */
    private $basePath     = BASE_PATH . '/';

    /**
     * 下载配置
     */
    private $softs        = [
        // [
        //     'name'      => '示例名称备注',
        //     'tagMethod' => 'github_release | github_pre_release | apkpure',
        //     'gitRepo'   => 'Github 仓库或参照 Surfboard',
        //     'savePath'  => '基础路径下的分类路径，可不填写',
        //     'downloads' => [
        //         [
        //             'sourceName' => '来源文件名，以该文件名储存则 saveName 不填写',
        //             'saveName'   => '储存文件名，',
        //             'apkpureUrl' => '参照 Surfboard'
        //         ],
        //     ],
        // ],
        [
            'name'      => 'ShadowsocksR-Windows',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'HMBSbige/ShadowsocksR-Windows',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'ShadowsocksR-%tagName%.7z',
                    'saveName'   => 'ssr-win.7z',
                    'apkpureUrl' => ''
                ]
            ],
        ],
        [
            'name'      => 'Netch',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'netchx/Netch',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'Netch.7z',
                    'saveName'   => 'Netch.7z',
                    'apkpureUrl' => ''
                ]
            ],
        ],
        [
            'name'      => 'ClashforWindows',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'Fndroid/clash_for_windows_pkg',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'Clash.for.Windows.Setup.%tagName%.exe',
                    'saveName'   => 'Clash-Windows.exe',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'Clash.for.Windows-%tagName%.dmg',
                    'saveName'   => 'Clash-Windows.dmg',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'Clash.for.Windows-%tagName%-arm64.dmg',
                    'saveName'   => 'Clash-Windows-arm64.dmg',
                    'apkpureUrl' => ''
                ]
            ],
        ],
        [
            'name'      => 'ClashX',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'yichengchen/clashX',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'ClashX.dmg',
                    'saveName'   => 'ClashX.dmg',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'V2RayU',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'yanue/V2rayU',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'V2rayU.dmg',
                    'saveName'   => 'V2rayU.dmg',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'ShadowsocksXNG',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'shadowsocks/ShadowsocksX-NG',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'ShadowsocksX-NG.%tagName1%.zip',
                    'saveName'   => 'ss-mac.zip',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'ShadowsocksXNGR',
            'tagMethod' => 'github_pre_release',
            'gitRepo'   => 'qinyuhang/ShadowsocksX-NG-R',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'ShadowsocksX-NG-R8.dmg',
                    'saveName'   => 'ssr-mac.dmg',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'V2RayNG',
            'tagMethod' => 'github_release',
            'gitRepo'   => '2dust/v2rayNG',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'v2rayNG_%tagName%_arm64-v8a.apk',
                    'saveName'   => 'v2rayng.apk',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'v2rayNG_%tagName%_armeabi-v7a.apk',
                    'saveName'   => 'v2rayng_armeabi-v7a.apk',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'v2rayNG_%tagName%_x86.apk',
                    'saveName'   => 'v2rayng_x86.apk',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'v2rayNG_%tagName%_x86_64.apk',
                    'saveName'   => 'v2rayng_x86_64.apk',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'Qv2ray',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'Qv2ray/Qv2ray',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'Qv2ray.%tagName%.Windows-x64.7z',
                    'saveName'   => 'Qv2ray.7z',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'Qv2ray-%tagName%.macOS-x64.dmg',
                    'saveName'   => 'Qv2ray.dmg',
                    'apkpureUrl' => ''
                ],
                [
                    'sourceName' => 'Qv2ray.%tagName%.linux-x64.AppImage',
                    'saveName'   => 'Qv2ray.AppImage',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'ClashforAndroid',
            'tagMethod' => 'github_pre_release',
            'gitRepo'   => 'Kr328/ClashForAndroid',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'app-premium-universal-release.apk',
                    'saveName'   => 'Clash-Android.apk',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'ShadowsocksAndroid',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'shadowsocks/shadowsocks-android',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'shadowsocks--universal-%tagName1%.apk',
                    'saveName'   => 'ss-android.apk',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'ShadowsocksR-Android',
            'tagMethod' => 'github_release',
            'gitRepo'   => 'HMBSbige/ShadowsocksR-Android',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'shadowsocksr-android-%tagName%.apk',
                    'saveName'   => 'ssr-android.apk',
                    'apkpureUrl' => ''
                ],
            ],
        ],
        [
            'name'      => 'ElectronSSR',
            'tagMethod' => 'github_pre_release',
            'gitRepo'   => 'shadowsocksrr/electron-ssr',
            'savePath'  => 'public/clients/',
            'downloads' => [
                [
                    'sourceName' => 'electron-ssr-%tagName%.AppImage',
                    'saveName'   => 'Electron-SSR.AppImage',
                    'apkpureUrl' => ''
                ],
            ],
        ],
    ];

    private $version;

    public function boot()
    {
        $this->client  = new \GuzzleHttp\Client();
        $this->version = $this->getLocalVersions();
        foreach ($this->softs as $soft) {
            $this->getSoft($soft);
        }
    }

    /**
     * 下载远程文件
     *
     * @param string $fileName
     * @param string $savePath
     * @param string $url
     *
     * @return bool
     */
    private function getSourceFile(string $fileName, string $savePath, string $url): bool
    {
        try {
            if (!file_exists($savePath)) {
                echo '目标文件夹 ' . $savePath . ' 不存在，创建中...' . PHP_EOL;
                system('mkdir ' . $savePath);
            }
            echo '- 开始下载 ' . $fileName . '...' . PHP_EOL;
            $request  = $this->client->get($url);
            echo '- 下载 ' . $fileName . ' 成功，正在保存...' . PHP_EOL;
            $result = file_put_contents($savePath . $fileName, $request->getBody()->getContents());
            if ($result === false) {
                echo '- 保存 ' . $fileName . ' 至 ' . $savePath . ' 失败.' . PHP_EOL;
            } else {
                echo '- 保存 ' . $fileName . ' 至 ' . $savePath . ' 成功.' . PHP_EOL;
                system('chown ' . $_ENV['php_user_group'] . ' ' . $savePath . $fileName);
            }
            return true;
        } catch (Exception $e) {
            echo '- 下载 ' . $fileName . ' 失败...' . PHP_EOL;
            echo $e->getMessage() . PHP_EOL;
            return false;
        }
    }

    /**
     * 获取 GitHub 常规 Release
     *
     * @param string $repo
     *
     * @return string
     */
    private function getLatestReleaseTagName(string $repo): string
    {
        $url     = 'https://api.github.com/repos/' . $repo . '/releases/latest' . ($_ENV['github_access_token'] != '' ? '?access_token=' . $_ENV['github_access_token'] : '');
        $request = $this->client->get($url);
        return (string) json_decode(
            $request->getBody()->getContents(),
            true
        )['tag_name'];
    }

    /**
     * 获取 GitHub Pre-Release
     *
     * @param string $repo
     *
     * @return string
     */
    private function getLatestPreReleaseTagName(string $repo): string
    {
        $url     = 'https://api.github.com/repos/' . $repo . '/releases' . ($_ENV['github_access_token'] != '' ? '?access_token=' . $_ENV['github_access_token'] : '');
        $request = $this->client->get($url);
        $latest  = json_decode(
            $request->getBody()->getContents(),
            true
        )[0];
        return (string) $latest['tag_name'];
    }

    /**
     * 获取 Apkpure TagName
     *
     * @param string $url
     *
     * @return string
     */
    private function getApkpureTagName(string $url): string
    {
        $request = $this->client->get($url);
        preg_match('#(?<=\<span\sitemprop="version">)[^<]+#', $request->getBody()->getContents(), $tagName);
        preg_match('#[\d\.]+#', $tagName[0], $tagNum);
        return (string) $tagNum[0];
    }

    /**
     * 判断是否 JSON
     *
     * @param string $string
     *
     * @return bool
     */
    private function is_json(string $string): bool
    {
        return (json_decode($string, true) !== false);
    }

    /**
     * 获取本地软体版本库
     *
     * @return array
     */
    private function getLocalVersions(): array
    {
        $fileName = 'ClientDownloadVersion.json';
        $filePath = BASE_PATH . '/config/' . $fileName;
        if (!is_file($filePath)) {
            echo '本地软体版本库 ClientDownloadVersion.json 不存在，创建文件中...' . PHP_EOL;
            $result = file_put_contents(
                $filePath,
                json_encode(
                    [
                        'createTime' => time()
                    ]
                )
            );
            if ($result === false) {
                echo 'ClientDownloadVersion.json 创建失败，脚本中止.' . PHP_EOL;
                exit(0);
            }
        }
        $fileContent = file_get_contents($filePath);
        if (!$this->is_json($fileContent)) {
            echo 'ClientDownloadVersion.json 文件格式异常，脚本中止.' . PHP_EOL;
            exit(0);
        }
        return json_decode($fileContent, true);
    }

    /**
     * 储存本地软体版本库
     *
     * @return bool
     */
    private function setLocalVersions(array $versions): bool
    {
        $fileName = 'ClientDownloadVersion.json';
        $filePath = BASE_PATH . '/config/' . $fileName;
        return (bool) file_put_contents(
            $filePath,
            json_encode(
                $versions
            )
        );
    }

    private function getSoft(array $task)
    {
        $savePath = $this->basePath . $task['savePath'];
        echo '====== ' . $task['name'] . ' 开始 ======' . PHP_EOL;
        switch ($task['tagMethod']) {
            case 'github_pre_release':
                $tagMethod = 'getLatestPreReleaseTagName';
                break;
            case 'apkpure':
                $tagMethod = 'getApkpureTagName';
                break;
            default:
                $tagMethod = 'getLatestReleaseTagName';
                break;
        }
        $tagName = $this->$tagMethod($task['gitRepo']);
        if (!isset($this->version[$task['name']])) {
            echo '- 本地不存在 ' . $task['name'] . '，检测到当前最新版本为 ' . $tagName . PHP_EOL;
        } else {
            if ($tagName == $this->version[$task['name']]) {
                echo '- 检测到当前 ' . $task['name'] . ' 最新版本与本地版本一致，跳过此任务.' . PHP_EOL;
                echo '====== ' . $task['name'] . ' 结束 ======' . PHP_EOL;
                return;
            }
            echo '- 检测到当前 ' . $task['name'] . ' 最新版本为 ' . $tagName . '，本地最新版本为 ' . $this->version[$task['name']] . PHP_EOL;
        }
        $this->version[$task['name']] = $tagName;
        $nameFunction = function ($name) use ($task, $tagName) {
            return str_replace(
                [
                    '%taskName%',
                    '%tagName%',
                    '%tagName1%'
                ],
                [
                    $task['name'],
                    $tagName,
                    substr($tagName, 1)
                ],
                $name
            );
        };
        foreach ($task['downloads'] as $download) {
            $fileName   = $nameFunction(($download['saveName'] != '' ? $download['saveName'] : $download['sourceName']));
            $sourceName = $nameFunction($download['sourceName']);
            $filePath   = $savePath . $fileName;
            if (is_file($filePath)) {
                echo '- 正在删除旧版本文件...' . PHP_EOL;
                if (!unlink($filePath)) {
                    echo '- 删除旧版本文件失败，此任务跳过，请检查权限等...' . PHP_EOL;
                    continue;
                }
            }
            if ($task['tagMethod'] == 'apkpure') {
                $request = $this->client->get($download['apkpureUrl']);
                preg_match('#(?<=href=")https:\/\/download\.apkpure\.com\/b\/APK[^"]+#', $request->getBody()->getContents(), $downloadUrl);
                $downloadUrl = $downloadUrl[0];
            } else {
                $downloadUrl = 'https://github.com/' . $task['gitRepo'] . '/releases/download/' . $tagName . '/' . $sourceName;
            }
            if ($this->getSourceFile($fileName, $savePath, $downloadUrl)) {
                $this->setLocalVersions($this->version);
            }
        }
        echo '====== ' . $task['name'] . ' 结束 ======' . PHP_EOL;
    }
}
