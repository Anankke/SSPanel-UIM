<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Services\Cloudflare;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function in_array;

final class ClientController extends BaseController
{
    public function getClients(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $clientName = $antiXss->xss_clean($args['name']);

        if (! $_ENV['enable_r2_client_download'] || $clientName === '' || $clientName === null) {
            return $response->withStatus(404);
        }

        $clients = [
            'Clash.Verge.exe',
            'Clash.Verge_aarch64.dmg',
            'Clash.Verge_x64.dmg',
            'Clash.Verge.AppImage.tar.gz',
            'Clash-Android.apk',
            'v2rayN-Core.zip',
            'v2rayNG.apk',
        ];

        if (! in_array($clientName, $clients)) {
            return $response->withStatus(404);
        }

        $presignedUrl = Cloudflare::genR2PresignedUrl($clientName);

        return $response->withHeader('Location', $presignedUrl)->withStatus(302);
    }
}
