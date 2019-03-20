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

namespace Hubzero\Events;

use Hubzero\Base\Traits\ErrorBag;
use InvalidArgumentException;
use Serializable;
use ArrayAccess;
use Countable;

/**
 * Default Event class.
 */
class Event implements ArrayAccess, Serializable, Countable
{
	use ErrorBag;

	/**
	 * The event name.
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * The event group name.
	 *
	 * @var  string
	 */
	protected $group;

	/**
	 * The event arguments.
	 *
	 * @var  array
	 */
	protected $arguments;

	/**
	 * A flag to see if the event propagation is stopped.
	 *
	 * @var  boolean
	 */
	protected $stopped = false;

	/**
	 * An array of error messages or Exception objects.
	 *
	 * @var    array
	 */
	protected $response = array();

	/**
	 * Constructor.
	 *
	 * @param  string  $name       The event name.
	 * @param  array   $arguments  The event arguments.
	 */
	public function __construct($name, array $arguments = array())
	{
		if (strstr($name, '.'))
		{
			$this->group = strstr($name, '.', true);
			$name = ltrim(strstr($name, '.'), '.');
		}
		$this->name      = $name;
		$this->arguments = $arguments;
	}

	/**
	 * Get the event group name.
	 *
	 * @return  string  The event group name.
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * Get the event name.
	 *
	 * @return  string  The event name.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Add an event argument, only if it is not existing.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 * @return  object  This method is chainable.
	 */
	public function addArgument($name, $value)
	{
		if (!isset($this->arguments[$name]))
		{
			$this->arguments[$name] = $value;
		}

		return $this;
	}

	/**
	 * Set the value of an event argument.
	 * If the argument already exists, it will be overridden.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 * @return  object  This method is chainable.
	 */
	public function setArgument($name, $value)
	{
		$this->arguments[$name] = $value;

		return $this;
	}

	/**
	 * Remove an event argument.
	 *
	 * @param   string  $name  The argument name.
	 * @return  object  This method is chainable.
	 */
	public function removeArgument($name)
	{
		if (isset($this->arguments[$name]))
		{
			unset($this->arguments[$name]);
		}

		return $this;
	}

	/**
	 * Clear all event arguments.
	 *
	 * @return  array  The old arguments.
	 */
	public function clearArguments()
	{
		$this->arguments = array();

		return $this;
	}

	/**
	 * Get an event argument value.
	 *
	 * @param   string  $name     The argument name.
	 * @param   mixed   $default  The default value if not found.
	 * @return  mixed  The argument value or the default value.
	 */
	public function getArgument($name, $default = null)
	{
		if (isset($this->arguments[$name]))
		{
			return $this->arguments[$name];
		}

		return $default;
	}

	/**
	 * Tell if the given event argument exists.
	 *
	 * @param   string  $name  The argument name.
	 * @return  boolean  True if it exists, false otherwise.
	 */
	public function hasArgument($name)
	{
		return isset($this->arguments[$name]);
	}

	/**
	 * Get all event arguments.
	 *
	 * @return  array  An associative array of argument names as keys
	 *                 and their values as values.
	 */
	public function getArguments()
	{
		return $this->arguments;
	}

	/**
	 * Count the number of arguments.
	 *
	 * @return  integer  The number of arguments.
	 */
	public function count()
	{
		return count($this->arguments);
	}

	/**
	 * Serialize the event.
	 *
	 * @return  string  The serialized event.
	 */
	public function serialize()
	{
		return serialize(array($this->name, $this->arguments, $this->stopped));
	}

	/**
	 * Unserialize the event.
	 *
	 * @param   string  $serialized  The serialized event.
	 * @return  void
	 */
	public function unserialize($serialized)
	{
		list($this->name, $this->arguments, $this->stopped) = unserialize($serialized);
	}

	/**
	 * Add an error message.
	 *
	 * @param   string  $error  Error message.
	 * @param   string  $key    Specific key to set the value to
	 * @return  object  This method is chainable.
	 */
	public function addResponse($data)
	{
		array_push($this->response, $data);

		return $this;
	}

	/**
	 * Get the list of responses from triggered listeners.
	 *
	 * @return  array
	 */
	public function getResponse()
	{
		return $this->response;
	}

	/**
	 * Stop the event propagation.
	 *
	 * @return  void
	 */
	public function stop()
	{
		$this->stopped = true;
	}

	/**
	 * Resume the event propagation.
	 *
	 * @return  void
	 */
	public function resume()
	{
		$this->stopped = false;
	}

	/**
	 * Tell if the event propagation is stopped.
	 *
	 * @return  boolean  True if stopped, false otherwise.
	 */
	public function isStopped()
	{
		return true === $this->stopped;
	}

	/**
	 * Set the value of an event argument.
	 *
	 * @param   string  $name   The argument name.
	 * @param   mixed   $value  The argument value.
	 * @return  void
	 * @throws  InvalidArgumentException  If the argument name is null.
	 */
	public function offsetSet($name, $value)
	{
		if (is_null($name))
		{
			throw new InvalidArgumentException('The argument name cannot be null.');
		}

		$this->setArgument($name, $value);
	}

	/**
	 * Remove an event argument.
	 *
	 * @param   string  $name  The argument name.
	 * @return  void
	 */
	public function offsetUnset($name)
	{
		$this->removeArgument($name);
	}

		/**
   * Tell if the given event argument exists.
   *
   * @param   string  $name  The argument name.
   * @return  boolean  True if it exists, false otherwise.
   */
	public function offsetExists($name)
	{
		return $this->hasArgument($name);
	}

	/**
	 * Get an event argument value.
	 *
	 * @param   string  $name  The argument name.
	 * @return  mixed  The argument value or null if not existing.
	 */
	public function offsetGet($name)
	{
		return $this->getArgument($name);
	}
}
