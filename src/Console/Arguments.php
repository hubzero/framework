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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console;

use Hubzero\Console\Config;
use Hubzero\Console\Exception\UnsupportedCommandException;
use Hubzero\Console\Exception\UnsupportedTaskException;
use Hubzero\Console\Exception\InvalidPropertyException;

/**
 * Console arguments class
 **/
class Arguments
{
	/**
	 * Raw command line arguments (PHP $argv)
	 *
	 * @var  array
	 **/
	private $raw = null;

	/**
	 * Class name - command to execute
	 *
	 * @var  string
	 **/
	private $class = null;

	/**
	 * Task name - class method to execute
	 *
	 * @var  string
	 **/
	private $task = null;

	/**
	 * Array of additional options being passed to the command
	 *
	 * @var  array
	 **/
	private $opts = null;

	/**
	 * Registered list of namespaces in which to search for commands
	 *
	 * @var  array
	 **/
	private $commandNamespaces = [];

	/**
	 * Constructor
	 *
	 * Set raw arguments
	 *
	 * @param   object  $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct($arguments)
	{
		$this->raw = $arguments;
		$this->registerNamespace(__NAMESPACE__ . '\\Command');
	}

	/**
	 * Simple getter for class properties
	 *
	 * Throws invalid property exception if property isn't found
	 *
	 * @param   string  $var  The property to retrieve
	 * @return  void
	 **/
	public function get($var)
	{
		if (isset($this->{$var}))
		{
			return $this->{$var};
		}
		else
		{
			throw new InvalidPropertyException("Property {$var} does not exists.");
		}
	}

	/**
	 * Getter for those additional options that a given command may use
	 *
	 * @param   string  $key      Option name to retieve value for
	 * @param   mixed   $default  Default value for option
	 * @return  void
	 **/
	public function getOpt($key, $default = false)
	{
		return (isset($this->opts[$key])) ? $this->opts[$key] : $default;
	}

	/**
	 * Get all opts
	 *
	 * @return  array
	 **/
	public function getOpts()
	{
		return $this->opts;
	}

	/**
	 * Setter for additional options for a given command
	 *
	 * @param   string  $key    The argument to set
	 * @param   mixed   $value  The argument value to give it
	 * @return  void
	 **/
	public function setOpt($key, $value)
	{
		$this->opts[$key] = $value;
	}

	/**
	 * Delete option
	 *
	 * @param   string  $key  The argument to remove
	 * @return  void
	 **/
	public function deleteOpt($key)
	{
		unset($this->opts[$key]);
	}

	/**
	 * Parse the raw arguments into command, task, and additional options
	 *
	 * @return  void
	 **/
	public function parse()
	{
		if (isset($this->raw) && count($this->raw) > 0)
		{
			$class = isset($this->raw[1]) ? $this->raw[1] : 'help';
			$task  = (isset($this->raw[2]) && substr($this->raw[2], 0, 1) != "-") ? $this->raw[2] : 'execute';

			$this->class = self::routeCommand($class, $this->commandNamespaces);
			$this->task  = self::routeTask($class, $this->class, $task);

			// Parse the remaining args for command options/arguments
			for ($i = 2; $i < count($this->raw); $i++)
			{
				// Ignore the second arg if we used it above as task
				if ($i == 2 && substr($this->raw[$i], 0, 1) != "-")
				{
					continue;
				}

				// Args with an "=" will use the value before as key and the value after as value
				if (strpos($this->raw[$i], "=") !== false)
				{
					$parts = explode("=", $this->raw[$i], 2);
					$key   = preg_replace("/^([-]{1,2})/", "", $parts[0]);
					$value = ($parts[1]);

					if (isset($this->opts[$key]))
					{
						$this->opts[$key] = (array)$this->opts[$key];
						array_push($this->opts[$key], $value);
					}
					else
					{
						$this->opts[$key] = $value;
					}

					continue;
				}
				// Args with a dash but no equals sign will be considered TRUE if present
				elseif (substr($this->raw[$i], 0, 1) == '-')
				{
					// Try to catch clumped arguments (ex: -if as shorthand for -i -f)
					if (preg_match("/^-([[:alpha:]]{2,})/", $this->raw[$i], $matches))
					{
						if (isset($matches[1]))
						{
							foreach (str_split($matches[1], 1) as $k)
							{
								$this->opts[$k] = true;
							}
						}

						continue;
					}
					else
					{
						$key   = preg_replace("/^([-]{1,2})/", "", $this->raw[$i]);
						$value = true;
					}
				}
				// Otherwise, we'll just save the arg as a single word and individual commands may use them
				else
				{
					$key   = $i;
					$value = $this->raw[$i];
				}

				$this->opts[$key] = $value;
			}
		}
	}

	/**
	 * Registers a location to look for commands
	 *
	 * @param   string  $namespace  The namespace location to use
	 * @return  $this
	 **/
	public function registerNamespace($namespace)
	{
		$this->commandNamespaces[] = $namespace;
	}

	/**
	 * Routes command to the proper file based on the input given
	 *
	 * @param   string  $command     The command to route
	 * @param   array   $namespaces  The potential command namespace locations
	 * @return  void
	 **/
	public static function routeCommand($command = 'help', $namespaces = [])
	{
		// Aliases take precedence, so parse for them first
		if ($aliases = Config::get('aliases'))
		{
			if (array_key_exists($command, $aliases))
			{
				if (strpos($aliases->$command, '::') !== false)
				{
					$bits      = explode('::', $aliases->$command);
					$command   = $bits[0];
					$aliasTask = $bits[1];
				}
				else
				{
					$command = $aliases->$command;
				}
			}
		}

		foreach ($namespaces as $namespace)
		{
			// Check if we're targeting a namespaced command
			$bits = [];
			if (strpos($command, ':'))
			{
				$bits = explode(':', $command);
			}
			else
			{
				$bits[] = $command;
			}

			$bits = array_map('ucfirst', $bits);

			// Replace any inset placeholders
			for ($i = 0; $i < count($bits); $i++)
			{
				$loc = $i + 1;
				if (strpos($namespace, "{\$$loc}"))
				{
					$namespace = str_replace("{\$$loc}", $bits[$i], $namespace);
					unset($bits[$i]);
				}
			}

			// Add any remaining bits to the end of the command namespace
			if (count($bits) > 0)
			{
				$namespace .= '\\' . implode('\\', $bits);
			}

			// Check for existence
			if (class_exists($namespace))
			{
				$class = $namespace;
				break;
			}
		}

		if (!isset($class))
		{
			throw new UnsupportedCommandException("Unknown command: {$command}.");
		}

		return $class;
	}

	/**
	 * Routes task to the proper method based on the input given
	 *
	 * @param   string  $command  The command to route
	 * @param   string  $class    The class deduced from routeCommand
	 * @param   string  $task     The task to route
	 * @return  void
	 **/
	public static function routeTask($command, $class, $task = 'execute')
	{
		// Aliases take precedence, so parse for them first
		if ($aliases = Config::get('aliases'))
		{
			if (array_key_exists($command, $aliases))
			{
				if (strpos($aliases->$command, '::') !== false)
				{
					$bits = explode('::', $aliases->$command);
					$task = $bits[1];
				}
			}
		}

		// Make sure task exists
		if (!method_exists($class, $task))
		{
			throw new UnsupportedTaskException("{$class} does not support the {$task} method.");
		}

		return $task;
	}
}
