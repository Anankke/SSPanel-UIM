<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\GiftCard;
use App\Models\UserMoneyLog;
use App\Services\DB;
use App\Services\Wallet;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function time;

final class MoneyController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $moneylogs = (new UserMoneyLog())->where('user_id', $user->id)->orderBy('id', 'desc')->get();

        foreach ($moneylogs as $moneylog) {
            $moneylog->create_time = Tools::toDateTime($moneylog->create_time);
        }

        $moneylog_count = $moneylogs->count();

        return $response->write(
            $this->view()
                ->assign('moneylogs', $moneylogs)
                ->assign('moneylog_count', $moneylog_count)
                ->fetch('user/money.tpl')
        );
    }

    public function applyGiftCard(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $giftcard_raw = $this->antiXss->xss_clean($request->getParam('giftcard'));
        $giftcard = (new GiftCard())->where('card', $giftcard_raw)->first();

        if ($giftcard === null || $giftcard->status !== 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '礼品卡无效',
            ]);
        }

        $user = $this->user;

        if ($user->is_shadow_banned) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '礼品卡无效',
            ]);
        }

        $redeemed = DB::connection()->transaction(static function () use ($giftcard, $user): bool {
            $giftcard = (new GiftCard())->lockForUpdate()->find($giftcard->id);

            if ($giftcard->status !== 0) {
                return false;
            }

            $giftcard->status = 1;
            $giftcard->use_time = time();
            $giftcard->use_user = $user->id;
            $giftcard->save();

            Wallet::credit($user->id, (string) $giftcard->balance, '礼品卡充值 ' . $giftcard->card);

            return true;
        }, 3);

        if (! $redeemed) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '礼品卡无效',
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '充值成功',
        ]);
    }
}
