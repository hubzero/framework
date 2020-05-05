<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * MemoryTest
 */
class MemoryTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		parent::setup();

		$this->cache->setDefaultDriver('memory');
	}
}
