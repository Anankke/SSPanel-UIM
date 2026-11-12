<?php

declare(strict_types=1);

use App\Models\Config;
use App\Models\User;
use App\Services\DB;
use App\Services\IM;
use App\Services\IM\Discord;
use App\Services\IM\Slack;
use App\Services\IM\Telegram;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . '/../../../app/predefine.php';

beforeEach(function () {
    DB::init();
    DB::connection()->beginTransaction();

    imSetting('discord_bot_token', 'test-discord-token');
    imSetting('slack_token', 'test-slack-token');
    imSetting('telegram_token', '123456:test-telegram-token');
});

afterEach(function () {
    DB::connection()->rollBack();
});

function imSetting(string $item, string $value): void
{
    (new Config())->where('item', $item)->delete();
    (new Config())->insert(['item' => $item, 'value' => $value, 'type' => 'string']);
}

function imUser(int $type, string $value): User
{
    $user = new User();
    $user->email = 'im-' . bin2hex(random_bytes(6)) . '@example.com';
    $user->pass = '';
    $user->passwd = '';
    $user->uuid = Uuid::uuid4()->toString();
    $user->port = random_int(10000, 60000);
    $user->im_type = $type;
    $user->im_value = $value;
    $user->save();

    return $user;
}

function imMockClient(array $responses, array &$history): Client
{
    $stack = HandlerStack::create(new MockHandler($responses));
    $stack->push(Middleware::history($history));

    return new Client(['handler' => $stack]);
}

it('routes the stored im_type to the matching client', function (int $type, string $client) {
    expect(IM::getClient($type))->toBeInstanceOf($client);
})->with([
    'slack' => [1, Slack::class],
    'discord' => [2, Discord::class],
    'telegram' => [4, Telegram::class],
    'unbound' => [0, Telegram::class],
    'unknown' => [3, Telegram::class],
]);

it('sends a Discord-bound user\'s notification to Discord', function () {
    $user = imUser(2, '123456789012345678');
    $history = [];
    $http = imMockClient([
        new Response(404, [], '{"message":"Unknown Channel","code":10003}'),
        new Response(200, [], '{"id":"987654321"}'),
        new Response(200, [], '{}'),
    ], $history);

    expect(IM::getClient($user->im_type))->toBeInstanceOf(Discord::class);

    (new Discord($http))->send($user->im_value, 'hello');

    expect($history)->toHaveCount(3);

    foreach ($history as $transaction) {
        expect($transaction['request']->getUri()->getHost())->toBe('discord.com');
    }

    $dm = $history[1]['request'];
    $message = $history[2]['request'];

    expect($history[0]['request']->getMethod())->toBe('GET')
        ->and(json_decode((string) $dm->getBody(), true)['recipient_id'])->toBe('123456789012345678')
        ->and($message->getUri()->getPath())->toBe('/api/v10/channels/987654321/messages')
        ->and(json_decode((string) $message->getBody(), true)['content'])->toBe('hello');
});

it('sends a Slack-bound user\'s notification to Slack', function () {
    $user = imUser(1, 'U0123ABCD');
    $history = [];
    $http = imMockClient([new Response(200, [], '{"ok":true}')], $history);

    expect(IM::getClient($user->im_type))->toBeInstanceOf(Slack::class);

    (new Slack($http))->send($user->im_value, 'hello');

    $request = $history[0]['request'];
    $body = json_decode((string) $request->getBody(), true);

    expect($history)->toHaveCount(1)
        ->and($request->getMethod())->toBe('POST')
        ->and($request->getUri()->getHost())->toBe('slack.com')
        ->and($request->getUri()->getPath())->toBe('/api/chat.postMessage')
        ->and($body['channel'])->toBe('U0123ABCD')
        ->and($body['text'])->toBe('hello');
});
