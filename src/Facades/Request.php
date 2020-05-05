<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Request facade
 *
 * @codeCoverageIgnore
 */
class Request extends Facade
{
	/**
	 * Get the registered name.
	 *
	 * @return string
	 */
	protected static function getAccessor()
	{
		return 'request';
	}
}
