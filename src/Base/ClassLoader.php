<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

/**
 * Class loader for non PSR-4 classes
 *
 * This can autoload PSR-4 classes or cases where the class
 * name maps to a lowercase path:
 *
 *    Components\Example\Models\Entry
 *    -> /components/example/models/entry.php
 *
 * Inspired by Laravel 4's autoloader
 */
class ClassLoader
{
	/**
	 * The registered directories.
	 *
	 * @var  array
	 */
	protected static $directories = array();

	/**
	 * Indicates if a ClassLoader has been registered.
	 *
	 * @var  bool
	 */
	protected static $registered = false;

	/**
	 * Load the given class file.
	 *
	 * @param   string  $class
	 * @return  bool
	 */
	public static function load($class)
	{
		$class = static::normalizeClass($class);

		foreach (static::$directories as $directory)
		{
			if (file_exists($path = $directory . DIRECTORY_SEPARATOR . $class))
			{
				require_once $path;

				return true;
			}

			if (file_exists($path = $directory . DIRECTORY_SEPARATOR . strtolower($class)))
			{
				require_once $path;

				return true;
			}
		}

		return false;
	}

	/**
	 * Get the normal file name for a class.
	 *
	 * @param   string  $class
	 * @return  string
	 */
	public static function normalizeClass($class)
	{
		if ($class[0] == '\\')
		{
			$class = substr($class, 1);
		}

		return str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
	}

	/**
	 * Register the given class loader on the auto-loader stack.
	 *
	 * @return  void
	 */
	public static function register()
	{
		if (!static::$registered)
		{
			static::$registered = spl_autoload_register(array('\Hubzero\Base\ClassLoader', 'load'));
		}
	}

	/**
	 * Add directories to the class loader.
	 *
	 * @param   string|array  $directories
	 * @return  void
	 */
	public static function addDirectories($directories)
	{
		static::$directories = array_unique(array_merge(static::$directories, (array) $directories));
	}

	/**
	 * Remove directories from the class loader.
	 *
	 * @param   string|array  $directories
	 * @return  void
	 */
	public static function removeDirectories($directories = null)
	{
		if (is_null($directories))
		{
			static::$directories = array();
		}
		else
		{
			static::$directories = array_diff(static::$directories, (array) $directories);
		}
	}

	/**
	 * Gets all the directories registered with the loader.
	 *
	 * @return  array
	 */
	public static function getDirectories()
	{
		return static::$directories;
	}
}
