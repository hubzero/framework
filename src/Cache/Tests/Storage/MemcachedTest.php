<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * MemcachedTest
 */
class MemcachedTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		if (!extension_loaded('memcached') || !class_exists('\Memcached'))
		{
			$this->markTestSkipped(
				'The Memcached extension is not available.'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('memcached');
	}
}
