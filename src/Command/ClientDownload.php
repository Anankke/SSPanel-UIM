<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\Cloudflare;
use Exception;
use GuzzleHttp\Client;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function get_current_user;
use function is_file;
use function json_decode;
use function json_encode;
use function posix_geteuid;
use function posix_getpwuid;
use function str_replace;
use function substr;
use function time;
use function unlink;
use const BASE_PATH;
use const PHP_EOL;

final class ClientDownload extends Command
{
    public string $description = '├─=: php xcat ClientDownload - 定时更新客户端' . PHP_EOL;

    private $client;

    /**
     * 保存基本路径
     */
    private string $basePath = BASE_PATH . '/';

    /**
     * 下载配置
     */
    private $version;

    public function boot(): void
    {
        $this->client = new Client();
        $this->version = $this->getLocalVersions();
        $clientsPath = BASE_PATH . '/config/clients.json';

        if (! is_file($clientsPath)) {
            echo 'clients.json 不存在，脚本中止。' . PHP_EOL;
            exit(0);
        }

        $runningUser = posix_getpwuid(posix_geteuid())['name'];
        $fileOwner = get_current_user();

        if ($runningUser !== $fileOwner) {
            echo '当前用户为 ' . $runningUser . '，与文件所有者 ' . $fileOwner . ' 不符，脚本中止。' . PHP_EOL;
            exit(0);
        }

        $clients = json_decode(file_get_contents($clientsPath), true);

        foreach ($clients['clients'] as $client) {
            $this->getSoft($client);
        }
    }

    /**
     * 下载远程文件
     */
    private function getSourceFile(string $fileName, string $savePath, string $url): bool
    {
        try {
            if (! file_exists($savePath)) {
                echo '目标文件夹 ' . $savePath . ' 不存在，下載失败。' . PHP_EOL;
                return false;
            }

            echo '- 开始下载 ' . $fileName . '...' . PHP_EOL;
            $request = $this->client->get($url);
            echo '- 下载 ' . $fileName . ' 成功，正在保存...' . PHP_EOL;
            $result = file_put_contents($savePath . $fileName, $request->getBody()->getContents());

            if ($result === false) {
                echo '- 保存 ' . $fileName . ' 至 ' . $savePath . ' 失败。' . PHP_EOL;
            } else {
                echo '- 保存 ' . $fileName . ' 至 ' . $savePath . ' 成功。' . PHP_EOL;
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
     */
    private function getLatestReleaseTagName(string $repo): string
    {
        $url = 'https://api.github.com/repos/' . $repo . '/releases/latest' .
            ($_ENV['github_access_token'] !== '' ? '?access_token=' . $_ENV['github_access_token'] : '');
        $request = $this->client->get($url);

        return (string) json_decode(
            $request->getBody()->getContents(),
            true
        )['tag_name'];
    }

    /**
     * 获取 GitHub Pre-Release
     */
    private function getLatestPreReleaseTagName(string $repo): string
    {
        $url = 'https://api.github.com/repos/' . $repo . '/releases' .
            ($_ENV['github_access_token'] !== '' ? '?access_token=' . $_ENV['github_access_token'] : '');
        $request = $this->client->get($url);
        $latest = json_decode(
            $request->getBody()->getContents(),
            true
        )[0];

        return (string) $latest['tag_name'];
    }

    /**
     * 判断是否 JSON
     */
    private function isJson(string $string): bool
    {
        return json_decode($string, true) !== false;
    }

    /**
     * 获取本地软体版本库
     *
     * @return array
     */
    private function getLocalVersions(): array
    {
        $fileName = 'LocalClientVersion.json';
        $filePath = BASE_PATH . '/storage/' . $fileName;

        if (! is_file($filePath)) {
            echo '本地软体版本库 LocalClientVersion.json 不存在，创建文件中...' . PHP_EOL;
            $result = file_put_contents(
                $filePath,
                json_encode(
                    [
                        'createTime' => time(),
                    ]
                )
            );
            if ($result === false) {
                echo 'LocalClientVersion.json 创建失败，脚本中止。' . PHP_EOL;
                exit(0);
            }
        }

        $fileContent = file_get_contents($filePath);

        if (! $this->isJson($fileContent)) {
            echo 'LocalClientVersion.json 文件格式异常，脚本中止。' . PHP_EOL;
            exit(0);
        }

        return json_decode($fileContent, true);
    }

    /**
     * 储存本地软体版本库
     */
    private function setLocalVersions(array $versions): void
    {
        $fileName = 'LocalClientVersion.json';
        $filePath = BASE_PATH . '/storage/' . $fileName;
        (bool) file_put_contents(
            $filePath,
            json_encode(
                $versions
            )
        );
    }

    private function getSoft(array $task): void
    {
        $savePath = $this->basePath . $task['savePath'];
        echo '====== ' . $task['name'] . ' 开始 ======' . PHP_EOL;

        $tagMethod = match ($task['tagMethod']) {
            'github_pre_release' => 'getLatestPreReleaseTagName',
            default => 'getLatestReleaseTagName',
        };

        $tagName = $this->$tagMethod($task['gitRepo']);

        if (! isset($this->version[$task['name']])) {
            echo '- 本地不存在 ' . $task['name'] . '，检测到当前最新版本为 ' . $tagName . PHP_EOL;
        } else {
            if ($tagName === $this->version[$task['name']]) {
                echo '- 检测到当前 ' . $task['name'] . ' 最新版本与本地版本一致，跳过此任务。' . PHP_EOL;
                echo '====== ' . $task['name'] . ' 结束 ======' . PHP_EOL;
                return;
            }
            echo '- 检测到当前 ' . $task['name'] . ' 最新版本为 ' .
                $tagName . '，本地最新版本为 ' . $this->version[$task['name']] . PHP_EOL;
        }

        $this->version[$task['name']] = $tagName;
        $nameFunction = static function ($name) use ($task, $tagName) {
            return str_replace(
                [
                    '%taskName%',
                    '%tagName%',
                    '%tagName1%',
                ],
                [
                    $task['name'],
                    $tagName,
                    substr($tagName, 1),
                ],
                $name
            );
        };

        foreach ($task['downloads'] as $download) {
            $fileName = $nameFunction(($download['saveName'] !== '' ? $download['saveName'] : $download['sourceName']));
            $sourceName = $nameFunction($download['sourceName']);
            $filePath = $savePath . $fileName;

            if (is_file($filePath)) {
                echo '- 正在删除旧版本文件...' . PHP_EOL;
                if (! unlink($filePath)) {
                    echo '- 删除旧版本文件失败，此任务跳过，请检查权限' . PHP_EOL;
                    continue;
                }
            }

            $downloadUrl = 'https://github.com/' . $task['gitRepo'] .
                '/releases/download/' . $tagName . '/' . $sourceName;

            if ($this->getSourceFile($fileName, $savePath, $downloadUrl)) {
                $this->setLocalVersions($this->version);
            }

            if ($_ENV['enable_r2_client_download']) {
                Cloudflare::uploadR2($fileName, file_get_contents($filePath));
                unlink($filePath);
            }
        }

        echo '====== ' . $task['name'] . ' 结束 ======' . PHP_EOL;
    }
}
