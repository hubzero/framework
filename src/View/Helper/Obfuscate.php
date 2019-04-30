<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Utility\Str;

/**
 * Helper for obfuscating text
 */
class Obfuscate extends AbstractHelper
{
	/**
	 * Obfuscate some text
	 *
	 * @param   string  $text  Text to obfuscate
	 * @return  string
	 * @throws  \InvalidArgumentException If no text passed
	 */
	public function __invoke($text = null)
	{
		if (null === $text)
		{
			throw new \InvalidArgumentException(__METHOD__ . '(); No text passed.');
		}

		return Str::obfuscate($text);
	}
}
