<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Language helper facade
 */
class Lang extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return  string
	 */
	protected static function getAccessor()
	{
		return 'language';
	}
}
