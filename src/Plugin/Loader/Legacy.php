<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin\Loader;

/**
 * This class acts as a pseudo-JDispatcher to
 * allow legacy plugin instantiation.
 */
class Legacy
{
	/**
	 * Attach an observer object
	 *
	 * @param   object  $observer  An observer object to attach
	 * @return  void
	 */
	public function attach($observer)
	{
		return null;
	}

	/**
	 * Detach an observer object
	 *
	 * @param   object   $observer  An observer object to detach.
	 * @return  boolean  True if the observer object was detached.
	 */
	public function detach($observer)
	{
		return true;
	}
}
