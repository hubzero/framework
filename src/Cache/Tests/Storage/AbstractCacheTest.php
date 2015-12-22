<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

use Hubzero\Test\Basic;
use Hubzero\Base\Application;
use Hubzero\Config\Registry;
use Hubzero\Cache\Manager;

/**
 * AbstractCacheTest
 */
abstract class AbstractCacheTest extends Basic
{
	/**
	 * Cache manager
	 *
	 * @var  object
	 */
	protected $cache;

	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setup()
	{
		$configurationFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'config.json';

		if (!is_file($configurationFile))
		{
			throw new \Exception('Configuration file not found in "' . $configurationFile . '"');
		}

		$config = json_decode(file_get_contents($configurationFile), true);

		$app = new Application;
		$app['config'] = new Registry();
		foreach ($config as $key => $value)
		{
			$app['config']->set($key, $value);
		}

		$this->cache = new Manager($app);
	}

	/**
	 * @return  array
	 */
	public function dataProvider()
	{
		return [
			['key1', 'value1', 1],
			['key2', 'value2', 100],
			['key3', 'value3', null],
			['key4', true, null],
			['key5', false, null],
			['key6', array(), null],
			['key7', new \DateTime('now', new \DateTimeZone('UTC')), null],
		];
	}

	/**
	 * Test if an item exists int he cache
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testHas($key, $value, $ttl)
	{
		$this->assertTrue($this->cache->forget($key));
		$this->assertFalse($this->cache->has($key));
		$this->assertTrue($this->cache->put($key, $value, $ttl));
		$this->assertTrue($this->cache->has($key));
	}

	/**
	 * Test adding item to cache, returning FALSE if it already exists
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testAdd($key, $value, $ttl)
	{
		$this->cache->put($key, $value, $ttl);
		$this->assertFalse($this->cache->add($key, $value, $ttl));
	}

	/**
	 * Test retrieving item from cache
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testGet($key, $value, $ttl)
	{
		$this->cache->put($key, $value, $ttl);
		$this->assertEquals($value, $this->cache->get($key));
	}

	/**
	 * Test removing item from cache
	 *
	 * @dataProvider dataProvider
	 *
	 * @param   string    $key
	 * @param   mixed     $value
	 * @param   int|null  $ttl
	 * @return  void
	 */
	public function testForget($key, $value, $ttl)
	{
		$this->cache->put($key, $value, $ttl);
		$this->assertTrue($this->cache->forget($key));
		$this->assertFalse($this->cache->has($key));
	}

	/**
	 * Test has() with expired data 
	 *
	 * @return  void
	 */
	public function testHasWithTtlExpired()
	{
		$this->cache->put('key1', 'value1', (1 / 60));
		sleep(2);
		$this->assertFalse($this->cache->has('key1'));
	}
}
