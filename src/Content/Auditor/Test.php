<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Auditor;

/**
 * Auditor test interface
 */
interface Test
{
	/**
	 * Run content through test
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function examine(array $data, array $options = []);
}
