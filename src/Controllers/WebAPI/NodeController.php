<?php

declare(strict_types=1);

namespace App\Controllers\WebAPI;

use App\Controllers\BaseController;
use App\Models\Node;
use App\Utils\ResponseHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function json_decode;
use const JSON_UNESCAPED_SLASHES;
use const VERSION;

final class NodeController extends BaseController
{
    /**
     * GET /mod_mu/nodes/{id}/info
     */
    public function getInfo(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $node_id = $args['id'];
        $node = (new Node())->find($node_id);

        if ($node === null) {
            return ResponseHelper::error($response, 'Node not found.');
        }

        if ($node->type === 0) {
            return ResponseHelper::error($response, 'Node is not enabled.');
        }

        $data = [
            'node_speedlimit' => $node->node_speedlimit,
            'sort' => $node->sort,
            'server' => $node->server,
            'custom_config' => json_decode($node->custom_config, true, JSON_UNESCAPED_SLASHES),
            'type' => $_ENV['appName'],
            'version' => $this->convertVersionFormat(VERSION),
        ];

        return ResponseHelper::successWithDataEtag($request, $response, $data);
    }

    /**
     * Convert version format from YY.M.P to YYYY.M.P for backward compatibility
     * This ensures XrayR and other backends can correctly compare versions
     *
     * @param string $version Version string in YY.M.P format (e.g., "25.1.0")
     *
     * @return string Version string in YYYY.M.P format (e.g., "2025.1.0")
     */
    private function convertVersionFormat(string $version): string
    {
        // Match version pattern: YY.M.P
        if (preg_match('/^(\d{2})\.(\d+)\.(\d+)$/', $version, $matches)) {
            $year = (int) $matches[1];
            $month = $matches[2];
            $patch = $matches[3];

            // Convert 2-digit year to 4-digit year
            // Assume years 00-99 map to 2000-2099
            $fullYear = 2000 + $year;

            return "{$fullYear}.{$month}.{$patch}";
        }

        // If format doesn't match, return original version
        return $version;
    }
}
