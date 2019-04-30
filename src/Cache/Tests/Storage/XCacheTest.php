<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * XcacheTest
 */
class XcacheTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		if (!extension_loaded('xcache'))
		{
			$this->markTestSkipped(
				'The xcache library is not available.'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('xcache');
	}
}
