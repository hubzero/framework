<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades\Tests\Mock;

use Hubzero\Facades\Facade;

/**
 * Mock Foo facade
 *
 * @codeCoverageIgnore
 */
class FooFacade extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return  string
	 */
	protected static function getAccessor()
	{
		return 'foo';
	}
}
