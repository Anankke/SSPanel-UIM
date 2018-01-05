<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Tests\Apcu;

/**
 * @requires extension apc
 */
class ApcuTest extends \PHPUnit_Framework_TestCase
{
    public function testApcu()
    {
        $key = __CLASS__;
        apcu_delete($key);

        $this->assertFalse(apcu_exists($key));
        $this->assertTrue(apcu_add($key, 123));
        $this->assertTrue(apcu_exists($key));
        $this->assertSame(array($key => -1), apcu_add(array($key => 123)));
        $this->assertSame(123, apcu_fetch($key));
        $this->assertTrue(apcu_store($key, 124));
        $this->assertSame(124, apcu_fetch($key));
        $this->assertSame(125, apcu_inc($key));
        $this->assertSame(124, apcu_dec($key));
        $this->assertTrue(apcu_cas($key, 124, 123));
        $this->assertFalse(apcu_cas($key, 124, 123));
        $this->assertTrue(apcu_delete($key));
        $this->assertFalse(apcu_delete($key));
        $this->assertArrayHasKey('cache_list', apcu_cache_info());
    }

    public function testAPCUIterator()
    {
        $key = __CLASS__;
        $this->assertTrue(apcu_store($key, 456));

        $entries = iterator_to_array(new \APCUIterator('/^'.preg_quote($key, '/').'$/', APC_ITER_KEY | APC_ITER_VALUE));

        $this->assertSame(array($key), array_keys($entries));
        $this->assertSame($key, $entries[$key]['key']);
        $this->assertSame(456, $entries[$key]['value']);
    }
}
