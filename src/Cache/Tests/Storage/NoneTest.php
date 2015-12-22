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
 * NoneTest
 */
class NoneTest extends Basic
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
	public function setUp()
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

		$this->cache->setDefaultDriver('none');
	}

	/**
	 * Test if an item exists in the cache
	 *
	 * @return  void
	 */
	public function testHas()
	{
		$this->cache->put('key', 'value', 15);
		$this->assertFalse($this->cache->has('key'));
	}

	/**
	 * Test adding item to cache, returning FALSE if it already exists
	 *
	 * @return  void
	 */
	public function testAdd()
	{
		$this->cache->put('key', 'value', 15);
		$this->assertFalse($this->cache->add('key', 'value', 15));
	}

	/**
	 * Test retrieving item from cache
	 *
	 * @return  void
	 */
	public function testGet()
	{
		$this->cache->put('key', 'value', 15);
		$this->assertNull($this->cache->get('key'));
	}

	/**
	 * Test puting something into the cache
	 *
	 * @return  void
	 */
	public function testPut()
	{
		$this->assertFalse($this->cache->put('key', 'value', 15));
	}

	/**
	 * Test removing item from cache
	 *
	 * @return  void
	 */
	public function testForget()
	{
		$this->assertTrue($this->cache->forget('key'));
	}
}
