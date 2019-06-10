<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Date facade
 *
 * @codeCoverageIgnore
 */
class Date extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return  string
	 */
	protected static function getAccessor()
	{
		return 'date';
	}

	/**
	 * Get the root object behind the facade.
	 *
	 * @return  object
	 */
	public static function getRoot()
	{
		return self::of('now');
	}

	/**
	 * Get the root object behind the facade.
	 *
	 * @return  object
	 */
	public static function of($date = 'now', $tz = null)
	{
		return new \Hubzero\Utility\Date($date, $tz);
	}
}
