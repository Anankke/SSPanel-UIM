<?php

declare(strict_types=1);

use App\Models\DetectBanLog;
use App\Models\DetectLog;
use App\Models\User;
use App\Services\DB;
use Psr\Http\Message\ResponseInterface;

const DATATABLE_ORDER_DETECT_LIST_ID = 990001;
const DATATABLE_ORDER_BAN_USER_ID = 990002;

/**
 * Endpoint => [path, JSON key of the paginator, column keys in display order, search value].
 */
function dataTableOrderEndpoints(): array
{
    return [
        'detect log' => [
            '/admin/detect/log/ajax',
            'logs',
            ['id', 'user_id', 'node_id', 'node_name', 'list_id', 'rule_name', 'datetime'],
            (string) DATATABLE_ORDER_DETECT_LIST_ID,
        ],
        'detect ban log' => [
            '/admin/detect/ban/ajax',
            'bans',
            ['id', 'user_id', 'detect_number', 'ban_time', 'start_time', 'end_time', 'ban_end_time', 'all_detect_number'],
            (string) DATATABLE_ORDER_BAN_USER_ID,
        ],
    ];
}

function dataTableOrderSeed(): array
{
    $rows = [];

    foreach ([[5, 30, 1700000300], [3, 10, 1700000100], [5, 20, 1700000200], [1, 40, 1700000050]] as [$user, $node, $time]) {
        $log = new DetectLog();
        $log->user_id = $user;
        $log->node_id = $node;
        $log->list_id = DATATABLE_ORDER_DETECT_LIST_ID;
        $log->datetime = $time;
        $log->save();
        $rows['detect log'][] = ['id' => $log->id, 'user_id' => $user, 'node_id' => $node, 'datetime' => $time];
    }

    foreach ([
        [7, 60, 1700000300, 1700000900, 12],
        [2, 30, 1700000100, 1700000700, 4],
        [7, 10, 1700000200, 1700000400, 30],
        [1, 90, 1700000050, 1700000800, 4],
    ] as [$number, $banTime, $start, $end, $all]) {
        $ban = new DetectBanLog();
        $ban->user_id = DATATABLE_ORDER_BAN_USER_ID;
        $ban->detect_number = $number;
        $ban->ban_time = $banTime;
        $ban->start_time = $start;
        $ban->end_time = $end;
        $ban->all_detect_number = $all;
        $ban->save();
        $rows['detect ban log'][] = [
            'id' => $ban->id,
            'detect_number' => $number,
            'ban_time' => $banTime,
            'start_time' => $start,
            'end_time' => $end,
            'all_detect_number' => $all,
        ];
    }

    return $rows;
}

function dataTableOrderPayload(string $endpoint, string $column, string $dir): array
{
    [, , $columns, $search] = dataTableOrderEndpoints()[$endpoint];

    return [
        'draw' => '1',
        'start' => '0',
        'length' => '100',
        'search' => ['value' => $search, 'regex' => 'false'],
        'order' => [['column' => $column, 'dir' => $dir]],
        'columns' => array_map(static fn (string $data): array => ['data' => $data], $columns),
    ];
}

function dataTableOrderIds(string $endpoint, ResponseInterface $response): array
{
    $body = json_decode((string) $response->getBody(), true);
    $rows = is_array($body) ? $body[dataTableOrderEndpoints()[$endpoint][1]]['data'] ?? [] : [];

    return array_map('intval', array_column($rows, 'id'));
}

function dataTableOrderExpected(array $rows, string $field, string $dir): array
{
    usort($rows, static function (array $a, array $b) use ($field, $dir): int {
        $cmp = $dir === 'asc' ? $a[$field] <=> $b[$field] : $b[$field] <=> $a[$field];

        return $cmp !== 0 ? $cmp : $b['id'] <=> $a['id'];
    });

    return array_map('intval', array_column($rows, 'id'));
}

beforeEach(function () {
    DB::connection()->beginTransaction();

    $this->previousUser = $GLOBALS['user'] ?? null;
    $admin = new User();
    $admin->isLogin = true;
    $admin->is_admin = 1;
    $GLOBALS['user'] = $admin;

    $this->rows = dataTableOrderSeed();
});

afterEach(function () {
    $GLOBALS['user'] = $this->previousUser;
    DB::connection()->rollBack();
});

describe('admin DataTables server-side ordering', function () {
    it('orders rows by the requested column', function (string $endpoint, string $field, string $dir) {
        $index = (string) array_search($field, dataTableOrderEndpoints()[$endpoint][2], true);

        $path = dataTableOrderEndpoints()[$endpoint][0];
        $response = $this->post($path, dataTableOrderPayload($endpoint, $index, $dir));

        expect($response->getStatusCode())->toBe(200)
            ->and(dataTableOrderIds($endpoint, $response))
            ->toBe(dataTableOrderExpected($this->rows[$endpoint], $field, $dir));
    })->with([
        ['detect log', 'user_id'],
        ['detect log', 'node_id'],
        ['detect log', 'datetime'],
        ['detect ban log', 'detect_number'],
        ['detect ban log', 'ban_time'],
        ['detect ban log', 'start_time'],
        ['detect ban log', 'end_time'],
        ['detect ban log', 'all_detect_number'],
    ])->with(['asc', 'desc']);

    it('falls back to ordering by id for columns it cannot sort by', function (
        string $endpoint,
        string $column,
        string $dir,
        string $expectedDir
    ) {
        $path = dataTableOrderEndpoints()[$endpoint][0];
        $response = $this->post($path, dataTableOrderPayload($endpoint, $column, $dir));

        expect($response->getStatusCode())->toBe(200)
            ->and(dataTableOrderIds($endpoint, $response))
            ->toBe(dataTableOrderExpected($this->rows[$endpoint], 'id', $expectedDir));
    })->with([
        'detect log default' => ['detect log', '0', 'desc', 'desc'],
        'detect log node_name' => ['detect log', '3', 'asc', 'asc'],
        'detect log rule_name' => ['detect log', '5', 'desc', 'desc'],
        'detect log out of range' => ['detect log', '99', 'asc', 'asc'],
        'detect log non-numeric' => ['detect log', 'name', 'asc', 'asc'],
        'detect log bad dir' => ['detect log', '0', 'sideways', 'desc'],
        'detect ban log default' => ['detect ban log', '0', 'desc', 'desc'],
        'detect ban log ban_end_time' => ['detect ban log', '6', 'asc', 'asc'],
        'detect ban log out of range' => ['detect ban log', '99', 'desc', 'desc'],
        'detect ban log non-numeric' => ['detect ban log', 'name', 'asc', 'asc'],
        'detect ban log bad dir' => ['detect ban log', '0', 'sideways', 'desc'],
    ]);
});
