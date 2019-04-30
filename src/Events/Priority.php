<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

/**
 * An enumeration of priorities for event listeners,
 * that you are encouraged to use when adding them in the Dispatcher.
 */
final class Priority
{
	const MIN          = -3;
	const LOW          = -2;
	const BELOW_NORMAL = -1;
	const NORMAL       = 0;
	const ABOVE_NORMAL = 1;
	const HIGH         = 2;
	const MAX          = 3;
}
