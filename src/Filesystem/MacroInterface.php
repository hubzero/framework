<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Filesystem;

/**
 * Filesystem macro interface.
 */
interface MacroInterface
{
	/**
	 * Get the method name.
	 *
	 * @return  string
	 */
	public function getMethod();

	/**
	 * Set the Filesystem object.
	 *
	 * @param  $filesystem  Filesystem
	 */
	public function setFilesystem(Filesystem $filesystem);
}
