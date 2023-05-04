<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\GiftCard;
use App\Models\UserMoneyLog;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function time;

/**
 *  User MoneyController
 */
final class MoneyController extends BaseController
{
    /**
     * @throws Exception
     */
    public function money(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $moneylogs = UserMoneyLog::where('user_id', $user->id)->orderBy('id', 'desc')->get();

        foreach ($moneylogs as $moneylog) {
            $moneylog->create_time = Tools::toDateTime($moneylog->create_time);
        }

        return $response->write(
            $this->view()
                ->assign('moneylogs', $moneylogs)
                ->fetch('user/money.tpl')
        );
    }

    public function applyGiftCard(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $antiXss = new AntiXSS();
        $giftcard_raw = $antiXss->xss_clean($request->getParam('giftcard'));

        $giftcard = GiftCard::where('card', $giftcard_raw)->first();

        if ($giftcard === null || $giftcard->status !== 0) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '礼品卡无效',
            ]);
        }

        $user = $this->user;

        $giftcard->status = 1;
        $giftcard->use_time = time();
        $giftcard->use_user = $user->id;
        $giftcard->save();

        $money_before = $user->money;
        $user->money += $giftcard->balance;
        $user->save();

        (new UserMoneyLog())->addMoneyLog(
            $user->id,
            (float) $money_before,
            (float) $user->money,
            $giftcard->balance,
            '礼品卡充值 ' . $giftcard->card
        );

        return $response->withJson([
            'ret' => 1,
            'msg' => '充值成功',
        ]);
    }
}
