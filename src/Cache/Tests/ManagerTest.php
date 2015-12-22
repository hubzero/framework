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

namespace Hubzero\Cache\Tests;

use Hubzero\Cache\Storage\None;
use Hubzero\Cache\Manager;
use Hubzero\Base\Application;
use Hubzero\Config\Registry;

/**
 * ManagerTest
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
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
		$configurationFile = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'config.json';

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
		$app['config']->set('foo', array(
			'hash'      => '',
			'cachebase' => ''
		));

		$this->cache = new Manager($app);
	}

	/**
	 * Test that an exception is thrown when selecting
	 * a nonexistent storage type.
	 *
	 * @expectedException \InvalidArgumentException
	 *
	 * @return  void
	 */
	public function testStorageThrowsException()
	{
		$this->cache->storage('foo');
	}

	/**
	 * Test setting the default storage type
	 *
	 * @return  void
	 */
	public function testSetDefaultDriver()
	{
		$this->cache->setDefaultDriver('memory');

		$this->assertEquals('memory', $this->cache->getDefaultDriver());
	}

	/**
	 * Test adding custom storage type
	 *
	 * @return  void
	 */
	public function testExtend()
	{
		$this->cache->extend('foo', function($config)
		{
			return new None;
		});

		$this->assertInstanceOf('Hubzero\Cache\Storage\None', $this->cache->storage('foo'));
	}
}
