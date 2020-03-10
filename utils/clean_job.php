#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Services\Boot;

require __DIR__ . '/../app/predefine.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/.config.php';

Boot::setTime();
Boot::bootDb();

$processed = [];
$renew = [];
$renew_c = function ($ids) use ($processed) {
    echo 'Renew Process START.';
    foreach ($ids as $id) {
        $bought = \App\Models\Bought::find($id);
        if ($bought == null) {
            echo 'Bought not found:' . $id . PHP_EOL;
            unlink(__DIR__ . '/../storage/' . $id . '.renew');
            $processed['renew'] = $id;
        } else {
            $bought->is_notified = true;
            if ($bought->save() == true) {
                unlink(__DIR__ . '/../storage/' . $id . '.renew');
                echo 'Renew Process successed for bought' . $id . PHP_EOL;
                $processed['renew'] = $id;
            }
        }
    }
    echo 'Renew Process END.' . PHP_EOL . PHP_EOL;
};

$offline = [];
$offline_c = function ($ids) use ($processed) {
    echo 'Offline Process START.';
    foreach ($ids as $id) {
        $node = \App\Models\Node::find($id);
        if ($node == null) {
            echo 'Node not found:' . $id . PHP_EOL;
            unlink(__DIR__ . '/../storage/' . $id . '.offline');
            $processed['offline'] = $id;
        } else {
            $node->online = false;
            if ($node->save() == true) {
                unlink(__DIR__ . '/../storage/' . $id . '.offline');
                echo 'Offline Process successed for node' . $id . PHP_EOL;
                $processed['offline'] = $id;
            }
        }
    }
    echo 'Offline Process END.' . PHP_EOL . PHP_EOL;
};

$expire = [];
$expire_c = function ($ids) use ($processed) {
    echo 'Expire Process START.';
    foreach ($ids as $id) {
        $user = \App\Models\User::find($id);
        if ($user == null) {
            echo 'User not found:' . $id . PHP_EOL;
            unlink(__DIR__ . '/../storage/' . $id . '.expire_in');
            $processed['expire'] = $id;
        } else {
            $user->expire_notified = true;
            if ($user->save() == true) {
                unlink(__DIR__ . '/../storage/' . $id . '.expire_in');
                echo 'Expire Process successed for user' . $id . PHP_EOL;
                $processed['expire'] = $id;
            }
        }
    }
    echo 'Expire Process END.' . PHP_EOL . PHP_EOL;
};

$gfw = [];
$gfw_c = function ($ids) use ($processed) {
    echo 'GFW Process START.';
    foreach ($ids as $id) {
        $node = \App\Models\Node::find($id);
        if ($node == null) {
            echo 'Node not found:' . $id . PHP_EOL;
            unlink(__DIR__ . '/../storage/' . $id . '.gfw');
            $processed['gfw'] = $id;
        } else {
            $node->gfw_block = true;
            if ($node->save() == true) {
                unlink(__DIR__ . '/../storage/' . $id . '.gfw');
                echo 'GFW Process successed for node' . $id . PHP_EOL;
                $processed['gfw'] = $id;
            }
        }
    }
    echo 'GFW Process END.' . PHP_EOL . PHP_EOL;
};

$files = scandir(__DIR__ . '/../storage');
foreach ($files as $origin_file) {
    $file = explode('.', $origin_file);
    if (count($file) == 2 && is_numeric($file[0])) {
        switch ($file[1]) {
            case 'renew':
                $renew[] = $file[0];
                break;
            case 'offline':
                $offline[] = $file[0];
                break;
            case 'expire_in':
                $expire[] = $file[0];
                break;
            case 'gfw':
                $gfw[] = $file[0];
                break;
            default:
                echo 'Unrecognized file: ' . $origin_file . PHP_EOL;
        }
    } else {
        continue;
    }
}

$renew_c($renew);
$offline_c($offline);
$expire_c($expire);
$gfw_c($gfw);

if (file_exists(__DIR__ . '/../storage/traffic_notified') == true) {
    $files = scandir(__DIR__ . '/../storage/traffic_notified');
    if ($files != false) {
        foreach ($files as $origin_file) {
            $file = explode('.', $origin_file);
            if (count($file) == 2 && is_numeric($file[0] && $file[1] == 'userid')) {
                $notified[] = $file[0];
            } else {
                echo 'Unrecognized file: ' . $origin_file . PHP_EOL;
            }
        }
        file_put_contents(__DIR__ . '/notified.json', json_encode($file));
    }
    $notified_c = function ($ids) use ($processed) {
        echo 'Notified Process START.';
        foreach ($ids as $id) {
            $user = \App\Models\User::find($id);
            if ($user == null) {
                echo 'User not found:' . $id . PHP_EOL;
                unlink(__DIR__ . '/../storage/traffic_notified/' . $id . '.userid');
                $processed['notified'] = $id;
            } else {
                $user->traffic_notified = true;
                if ($user->save() == true) {
                    unlink(__DIR__ . '/../storage/traffic_notified/' . $id . '.userid');
                    echo 'Notified Process successed for node' . $id . PHP_EOL;
                    $processed['notified'] = $id;
                }
            }
        }
        echo 'Notified Process END.' . PHP_EOL . PHP_EOL;
    };
} else {
    echo 'Notified Process Nothing to do.' . PHP_EOL . PHP_EOL;
}
file_put_contents(__DIR__ . '/processed.json', json_encode($processed));
file_put_contents(__DIR__ . '/raw.json', json_encode([
    'renew' => $renew,
    'offline' => $offline,
    'expire' => $expire,
    'gfw' => $gfw,
]));
exit(0);
