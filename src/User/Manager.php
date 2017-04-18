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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

/**
 * User manager
 */
class Manager
{
	/**
	 * The application instance.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * The array of created "drivers".
	 *
	 * @var  array
	 */
	protected $users = array();

	/**
	 * Create a new manager instance.
	 *
	 * @param   object  $app
	 * @return  void
	 */
	public function __construct($app)
	{
		$this->app = $app;
	}

	/**
	 * Get the default user
	 *
	 * @return  object
	 */
	public function getCurrentUser()
	{
		$instance = $this->app['session']->get('user');

		if (!($instance instanceof User))
		{
			$instance = new User;
		}

		return $instance;
	}

	/**
	 * Get a user instance
	 *
	 * @param   mixed  $id  Integer or string
	 * @return  object
	 */
	public function getInstance($id = null)
	{
		$current = $this->getCurrentUser();

		// Does the ID match to the current user?
		// If so, we already have that info and can just return it
		if (is_null($id))
		{
			return $current;
		}

		if (is_numeric($id))
		{
			// Cast as an integer so we can do an exact (===) comparison below
			$id = (int)$id;
		}

		if ($id == (int)$current->get('id')
		 || $id == (string)$current->get('username'))
		{
			return $current;
		}

		// Is the ID numeric?
		// If not, let's try to resolve by username or email address.
		if (!is_numeric($id))
		{
			$user = $this->resolveUser($id);
			$id   = $user->get('id', 0);

			if ($id)
			{
				$this->users[$id] = $user;
			}
		}

		// If the $id is zero, just return an empty User.
		// Note: don't cache this user because it'll have a new ID on save!
		if ($id === 0)
		{
			return new User;
		}

		// If the given user has not been created before, we will create the instance
		// here and cache it so we can return it next time very quickly. If there is
		// already a user created for this ID, we'll just return that instance.
		if (!isset($this->users[$id]))
		{
			$this->users[$id] = $this->resolveUser($id);
		}

		return $this->users[$id];
	}

	/**
	 * Create a new user instance.
	 *
	 * @param   mixed  $id
	 * @return  object
	 */
	protected function resolveUser($id)
	{
		if (!is_numeric($id))
		{
			if (strstr($id, '@'))
			{
				$user = User::oneByEmail($id);
			}
			else
			{
				$user = User::oneByUsername($id);
			}
		}
		else
		{
			$user = User::oneOrNew($id);
		}

		return $user;
	}

	/**
	 * Get all of the created "users".
	 *
	 * @return  array
	 */
	public function getUsers()
	{
		return $this->users;
	}

	/**
	 * Dynamically call the router instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->getInstance(), $method), $parameters);
	}
}
