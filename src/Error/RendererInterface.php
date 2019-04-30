<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error;

use Exception;

/**
 * Error renderer interface
 */
interface RendererInterface
{
	/**
	 * Display the given exception to the user.
	 *
	 * @param  object  $error
	 */
	public function render($error);
}
