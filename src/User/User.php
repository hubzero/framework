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
 * @since     Class available since release 2.0.0
 */

namespace Hubzero\User;

use Hubzero\Config\Registry;

/**
 * Users database model
 *
 * @uses \Hubzero\Database\Relational
 */
class User extends \Hubzero\Database\Relational
{
	/**
	 * Default order by for model
	 *
	 * @var    string
	 * @since  2.1.0
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var    string
	 * @since  2.1.0
	 */
	public $orderDir = 'asc';

	/**
	 * Guest status
	 *
	 * @var    bool
	 * @since  2.1.0
	 */
	public $guest = null;

	/**
	 * Fields and their validation criteria
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $rules = array(
		'name'     => 'notempty',
		'username' => 'notempty'
	);

	/**
	 * User params
	 *
	 * @var    object
	 * @since  2.1.0
	 */
	protected $userParams = null;

	/**
	 * Authorised access groups
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $authGroups = null;

	/**
	 * Authorised access levels
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $authLevels = null;

	/**
	 * Authorised access actions
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $authActions = null;

	/**
	 * Defines a one to many relationship between users and reset tokens
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 */
	public function tokens()
	{
		return $this->oneToMany('\Components\Members\Models\Token');
	}

	/**
	 * Defines a one to one relationship between a user and their reputation
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToOne
	 * @since   2.0.0
	 */
	public function reputation()
	{
		return $this->oneToOne('Reputation');
	}

	/**
	 * Defines a relationship with a generic user logging class (not a relational model itself)
	 *
	 * @return  object  \Hubzero\User\Logger
	 * @since   2.0.0
	 */
	public function logger()
	{
		return new Logger($this);
	}

	/**
	 * Transform parameters into object
	 *
	 * @return  object  \Hubzero\Config\Registry
	 * @since   2.1.0
	 */
	public function transformParams()
	{
		if (!isset($this->userParams))
		{
			$this->userParams = new Registry($this->get('params'));
		}

		return $this->userParams;
	}

	/**
	 * Checks to see if the current user has exceeded the site
	 * password reset request limit for a given time period
	 *
	 * @return  bool
	 */
	public function hasExceededResetLimit()
	{
		$params     = \Component::params('com_users');
		$resetCount = (int)$params->get('reset_count', 10);
		$resetHours = (int)$params->get('reset_time', 1);
		$result     = true;

		// Get the user's tokens
		$threshold = date("Y-m-d H:i:s", strtotime(\Date::toSql() . " {$resetHours} hours ago"));
		$tokens    = $this->tokens()->where('created', '>=', $threshold)->rows();

		if ($tokens->count() < $resetCount)
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Checks to see if the current user has exceeded the site
	 * login attempt limit for a given time period
	 *
	 * @return  bool
	 */
	public function hasExceededLoginLimit()
	{
		$params    = \Component::params('com_users');
		$limit     = (int)$params->get('login_attempts_limit', 10);
		$timeframe = (int)$params->get('login_attempts_timeframe', 1);
		$result    = true;

		// Get the user's tokens
		$threshold = date("Y-m-d H:i:s", strtotime(\Date::toSql() . " {$timeframe} hours ago"));
		$auths     = new \Hubzero\User\Log\Auth;

		$auths->whereEquals('username', $this->username)
		      ->whereEquals('status', 'failure')
		      ->where('logged', '>=', $threshold);

		if ($auths->count() < $limit)
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Get a user's picture
	 *
	 * @param   integer  $anonymous  Is user anonymous?
	 * @param   boolean  $thumbnail  Show thumbnail or full picture?
	 * @param   boolean  $serveFile  Serve file?
	 * @return  string
	 * @since   2.1.0
	 */
	public function picture($anonymous=0, $thumbnail=true, $serveFile=true)
	{
		static $picture;

		if (!isset($picture))
		{
			$image = "<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64' style='stroke-width: 0px; background-color: #ffffff;'>" .
					"<path fill='#d9d9d9' d='M63.9 64v-3c-.6-.9-1-1.8-1.4-2.8l-1.2-3c-.4-1-.9-1.9-1.4-2.8S58.8 50.9 58 " .
					"50c-.8-.8-1.5-1.3-2.4-1.5-.6-.2-1.1-.3-1.7-.4-.6 0-2.1-.3-4.4-.6l-8.4-1.3c-.2-.8-.4-1.5-.5-2.4-.1-" .
					".8-.3-1.5-.6-2.4.3-.6.7-1 1.1-1.5.4-.6.8-1 1.1-1.5.4-.6.7-1.3 1-2.2.3-.8.8-3.5 1.3-7.8l.4-3c.1-.9." .
					"1-1.4.1-1.5 0-2.9-1-5.6-3.1-8-1-1.3-2.4-2.4-4.1-3.2-1.8-.9-3.7-1.4-6-1.4-2.2 0-4.3.4-6 1.3-1.8.9-3" .
					".1 2-4.2 3.2-1.1 1.3-1.8 2.6-2.3 4.1-.6 1.4-.7 2.5-.7 3.2 0 .7 0 1.5.1 2.3l.4 2.9.4 3.1.4 3.3c.2 1" .
					".1.7 2.4 1.5 3.7.3.6.7 1.1 1.1 1.5l1.1 1.5c-.2.8-.4 1.5-.6 2.4-.1.8-.3 1.5-.6 2.4l-5.6.8-4.6.8c-1." .
					"2.2-2.1.3-2.6.4-.6.1-1.1.2-1.7.4-2.1.8-4 3.1-5.7 6.8L.9 58.5c-.4 1-.8 1.9-1.3 2.8V64h64.3z'/>" .
					"</svg>";

			$picture = sprintf('data:image/svg+xml;base64,%s', base64_encode($image));
		}

		return $picture;
	}

	/**
	 * Finds a user by username
	 *
	 * @param   string  $username
	 * @return  object
	 */
	public static function oneByUsername($username)
	{
		return self::all()
			->whereEquals('username', $username)
			->row();
	}

	/**
	 * Finds a user by email
	 *
	 * @param   string  $email
	 * @return  object
	 */
	public static function oneByEmail($email)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			return self::oneByUsername($email);
		}

		return self::all()
			->whereEquals('email', $email)
			->row();
	}

	/**
	 * Finds a user by activation token
	 *
	 * @param   string  $token
	 * @return  object
	 */
	public static function oneByActivationToken($token)
	{
		return self::all()
			->whereEquals('activation', $token)
			->row();
	}
}
