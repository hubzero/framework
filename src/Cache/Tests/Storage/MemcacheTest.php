<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * MemcacheTest
 */
class MemcacheTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		if (!extension_loaded('memcache') || !class_exists('\Memcache'))
		{
			$this->markTestSkipped(
				'The Memcache extension is not available.'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('memcache');
	}
}
