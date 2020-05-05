<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Error\Exception;

/**
 * 'Not Found' Exception.
 * Defaults to 404 code.
 */
class NotFoundException extends \Exception
{
	/**
	 * Constructor
	 *
	 * @param   string   $message   The Exception message to throw.
	 * @param   integer  $code      The Exception code.
	 * @param   object   $previous  The previous exception used for the exception chaining.
	 * @return  void
	 */
	public function __construct($message = '', $code = 404, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
