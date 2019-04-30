<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Cache\Tests\Storage;

/**
 * EacceleratorTest
 */
class EacceleratorTest extends AbstractCacheTest
{
	/**
	 * Test setup
	 *
	 * @return  void
	 */
	public function setUp()
	{
		@include_once 'Cache' . DS . 'Lite.php';

		if (!extension_loaded('eaccelerator') || !function_exists('eaccelerator_get'))
		{
			$this->markTestSkipped(
				'The eaccelerator extension is not available.'
			);
		}

		parent::setup();

		$this->cache->setDefaultDriver('eaccelerator');
	}
}
