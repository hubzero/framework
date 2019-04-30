<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * FileTest
 */
class FileTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setup()
	{
		parent::setup();

		$this->cache->setDefaultDriver('file');
	}

	/**
	 * Clear out any leftover test data
	 *
	 * @return  void
	 */
	public function tearDown()
	{
		$this->cache->clean();
	}
}
