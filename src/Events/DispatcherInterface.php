<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

/**
 * Interface for event dispatchers.
 */
interface DispatcherInterface
{
	/**
	 * Trigger an event.
	 *
	 * @param   mixed   $event  The event object or name.
	 * @return  object  The event after being passed through all listeners.
	 */
	public function trigger($event, $args = array());
}
