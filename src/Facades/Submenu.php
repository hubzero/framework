<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Submenu facade
 */
class Submenu extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return  string
	 */
	public static function getAccessor()
	{
		return 'submenu';
	}

	/**
	 * Method to add a menu item to submenu.
	 *
	 * @param  string  $name  Name of the menu item.
	 * @param  string  $link  URL of the menu item.
	 * @param  bool    True if the item is active, false otherwise.
	 */
	public static function addEntry($name, $link = '', $active = false)
	{
		static::getRoot()->appendButton($name, $link, $active);
	}
}
