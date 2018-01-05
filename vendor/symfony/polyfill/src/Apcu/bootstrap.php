<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!extension_loaded('apc')) {
    return;
}

if (!function_exists('apcu_add')) {
    function apcu_add($key, $var = null, $ttl = 0) { return apc_add($key, $var, $ttl); }
    function apcu_cache_info($limited = false) { return apc_cache_info('user', $limited); }
    function apcu_cas($key, $old, $new) { return apc_cas($key, $old, $new); }
    function apcu_clear_cache() { return apc_clear_cache('user'); }
    function apcu_dec($key, $step = 1, &$success = false) { return apc_dec($key, $step, $success); }
    function apcu_delete($key) { return apc_delete($key); }
    function apcu_exists($keys) { return apc_exists($keys); }
    function apcu_fetch($key, &$success = false) { return apc_fetch($key, $success); }
    function apcu_inc($key, $step = 1, &$success = false) { return apc_inc($key, $step, $success); }
    function apcu_sma_info($limited = false) { return apc_sma_info($limited); }
    function apcu_store($key, $var = null, $ttl = 0) { return apc_store($key, $var, $ttl); }
}

if (!class_exists('APCUIterator', false) && class_exists('APCIterator', false)) {
    class APCUIterator extends APCIterator
    {
        public function __construct($search = null, $format = APC_ITER_ALL, $chunk_size = 100, $list = APC_LIST_ACTIVE)
        {
            parent::__construct('user', $search, $format, $chunk_size, $list);
        }
    }
}
