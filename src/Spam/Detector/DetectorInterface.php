<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\Detector;

/**
 * Spam detector interface
 */
interface DetectorInterface
{
	/**
	 * Run content through spam detection
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data);

	/**
	 * Return any message the service may have
	 *
	 * @return  string
	 */
	public function message();
}
