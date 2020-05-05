<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Html\Builder\Asset;

/**
 * Helper for outputting icons.
 */
class Icon extends AbstractHelper
{
	/**
	 * Generate asset path
	 *
	 * @param   string  $symbol
	 * @param   bool    $ariahidden
	 * @return  string
	 */
	public function __invoke($symbol, $ariahidden = true)
	{
		return Asset::icon($symbol, $ariahidden);
	}
}
