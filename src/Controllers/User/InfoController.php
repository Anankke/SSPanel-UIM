<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\Config;
use App\Models\User;
use App\Services\Auth;
use App\Services\Cache;
use App\Services\MFA;
use App\Utils\Hash;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use function in_array;
use function strlen;
use function strtolower;
use const BASE_PATH;

final class InfoController extends BaseController
{
    /**
     * @throws Exception
     */
    public function index(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $themes = Tools::getDir(BASE_PATH . '/resources/views');
        $methods = Tools::getSsMethod('method');
        $ga_url = MFA::getGaUrl($this->user);

        return $response->write($this->view()
            ->assign('user', $this->user)
            ->assign('themes', $themes)
            ->assign('methods', $methods)
            ->assign('ga_url', $ga_url)
            ->fetch('user/edit.tpl'));
    }

    /**
     * @throws RedisException
     */
    public function updateEmail(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $new_email = $this->antiXss->xss_clean($request->getParam('newemail'));
        $user = $this->user;
        $old_email = $user->email;

        if (! $_ENV['enable_change_email'] || $user->is_shadow_banned) {
            return ResponseHelper::error($response, '修改失败');
        }

        if ($new_email === '') {
            return ResponseHelper::error($response, '未填写邮箱');
        }

        $check_res = Tools::isEmailLegal($new_email);

        if ($check_res['ret'] !== 1) {
            return $response->withJson($check_res);
        }

        $exist_user = (new User())->where('email', $new_email)->first();

        if ($exist_user !== null) {
            return ResponseHelper::error($response, '邮箱已经被使用了');
        }

        if ($new_email === $old_email) {
            return ResponseHelper::error($response, '新邮箱不能和旧邮箱一样');
        }

        if (Config::obtain('reg_email_verify')) {
            $redis = (new Cache())->initRedis();
            $email_verify_code = $request->getParam('emailcode');
            $email_verify = $redis->get('email_verify:' . $email_verify_code);

            if (! $email_verify) {
                return ResponseHelper::error($response, '你的邮箱验证码不正确');
            }

            $redis->del('email_verify:' . $email_verify_code);
        }

        $user->email = $new_email;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
            'data' => [
                'email' => $user->email,
            ],
        ]);
    }

    public function updateUsername(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $newusername = $this->antiXss->xss_clean($request->getParam('newusername'));
        $user = $this->user;

        if ($user->is_shadow_banned) {
            return ResponseHelper::error($response, '修改失败');
        }

        $user->user_name = $newusername;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '修改成功',
            'data' => [
                'username' => $user->user_name,
            ],
        ]);
    }

    public function unbindIM(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;

        if (! $user->unbindIM()) {
            return ResponseHelper::error($response, '解绑失败');
        }

        return $response->withHeader('HX-Refresh', 'true')->withJson([
            'ret' => 1,
            'msg' => '解绑成功',
        ]);
    }

    public function updatePassword(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $oldpwd = $request->getParam('oldpwd');
        $pwd = $request->getParam('pwd');
        $repwd = $request->getParam('repwd');
        $user = $this->user;

        if ($oldpwd === '' || $pwd === '' || $repwd === '') {
            return ResponseHelper::error($response, '密码不能为空');
        }

        if (! Hash::checkPassword($user->pass, $oldpwd)) {
            return ResponseHelper::error($response, '旧密码错误');
        }

        if ($pwd !== $repwd) {
            return ResponseHelper::error($response, '两次输入不符合');
        }

        if (strlen($pwd) < 8) {
            return ResponseHelper::error($response, '密码太短啦');
        }

        if (! $user->updatePassword($pwd)) {
            return ResponseHelper::error($response, '修改失败');
        }

        if (Config::obtain('enable_forced_replacement')) {
            $user->cleanLink();
        }

        return ResponseHelper::success($response, '修改成功');
    }

    public function resetPasswd(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->passwd = Tools::genRandomChar(16);
        $user->uuid = Uuid::uuid4();

        if (! $user->save()) {
            return ResponseHelper::error($response, '重置失败');
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '重置成功',
            'data' => [
                'passwd' => $user->passwd,
                'uuid' => $user->uuid,
            ],
        ]);
    }

    public function resetApiToken(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->api_token = Uuid::uuid4();

        if (! $user->save()) {
            return ResponseHelper::error($response, '重置失败');
        }

        return ResponseHelper::success($response, '重置成功');
    }

    public function updateMethod(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $method = strtolower($this->antiXss->xss_clean($request->getParam('method')));

        if ($method === '') {
            ResponseHelper::error($response, '非法输入');
        }
        if (! Tools::isParamValidate('method', $method)) {
            ResponseHelper::error($response, '加密无效');
        }

        $user->method = $method;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::success($response, '修改成功');
    }

    public function resetURL(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->cleanLink();

        return ResponseHelper::success($response, '重置成功');
    }

    public function resetInviteURL(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $user->clearInviteCodes();
        $code = $this->user->addInviteCode();

        return $response->withJson([
            'ret' => 1,
            'msg' => '重置成功',
            'data' => [
                'invite-url' => $_ENV['baseUrl'] . '/auth/register?code=' . $code,
            ],
        ]);
    }

    public function updateDailyMail(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $value = (int) $request->getParam('mail');

        if (! in_array($value, [0, 1, 2])) {
            return ResponseHelper::error($response, '参数错误');
        }

        $user = $this->user;
        $user->daily_mail_enable = $value;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::success($response, '修改成功');
    }

    public function updateContactMethod(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $value = (int) $request->getParam('contact');

        if (! in_array($value, [1, 2])) {
            return ResponseHelper::error($response, '参数错误');
        }

        $user = $this->user;
        $user->contact_method = $value;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return ResponseHelper::success($response, '修改成功');
    }

    public function updateTheme(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $theme = $this->antiXss->xss_clean($request->getParam('theme'));
        $user = $this->user;

        if ($theme === '') {
            return ResponseHelper::error($response, '主题不能为空');
        }

        $user->theme = $theme;

        if (! $user->save()) {
            return ResponseHelper::error($response, '修改失败');
        }

        return $response->withHeader('HX-Refresh', 'true')->withJson([
            'ret' => 1,
            'msg' => '修改成功',
        ]);
    }

    public function sendToGulag(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $user = $this->user;
        $passwd = $request->getParam('passwd');

        if ($passwd === '') {
            return ResponseHelper::error($response, '密码不能为空');
        }

        if (! Hash::checkPassword($user->pass, $passwd)) {
            return ResponseHelper::error($response, '密码错误');
        }

        if ($_ENV['enable_kill']) {
            Auth::logout();
            $user->kill();

            return $response->withHeader('HX-Refresh', 'true')->withJson([
                'ret' => 1,
                'msg' => '你的帐号已被送去古拉格劳动改造，再见',
            ]);
        }

        return ResponseHelper::error($response, '自助账号删除未启用');
    }
}
