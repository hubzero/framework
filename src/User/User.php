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
use Hubzero\Utility\Date;

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
	public $guest = true;

	/**
	 * Fields and their validation criteria
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $rules = array(
		'name'     => 'notempty',
		'email'    => 'notempty|email',
		'username' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	public $initiate = array(
		'registerDate',
		'registerIP'
	);

	/**
	 * A cached switch for if this user has root access rights.
	 *
	 * @var    boolean
	 * @since  2.1.0
	 */
	protected $isRoot = null;

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
	 * Link pattern
	 *
	 * @var    string
	 * @since  2.1.0
	 */
	public static $linkBase = null;

	/**
	 * List of picture resolvers
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	public static $pictureResolvers = array();

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		// Check that username conforms to rules
		$this->addRule('username', function($data)
		{
			$username = $data['username'];

			if (preg_match('#[<>"\'%;()&\\\\]|\\.\\./#', $username)
			 || strlen(utf8_decode($username)) < 2
			 || trim($username) != $username)
			{
				return \Lang::txt('JLIB_DATABASE_ERROR_VALID_AZ09', 2);
			}

			return false;
		});

		// Check for existing username
		$this->addRule('username', function($data)
		{
			$user = self::oneByUsername($data['username']);

			if ($user->get('id') && $user->get('id') != $data['id'])
			{
				return \Lang::txt('JLIB_DATABASE_ERROR_USERNAME_INUSE');
			}

			return false;
		});
	}

	/**
	 * Generates automatic registerDate field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRegisterDate($data)
	{
		$dt = new Date('now');

		return $dt->toSql();
	}

	/**
	 * Generates automatic registerIP field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticRegisterIP($data)
	{
		if (!isset($data['registerIP']))
		{
			$data['registerIP'] = \Request::ip();
		}
		return $data['registerIP'];
	}

	/**
	 * Defines a one to many relationship between users and reset tokens
	 *
	 * @return  object  \Hubzero\Database\Relationship\OneToMany
	 * @since   2.0.0
	 */
	public function tokens()
	{
		return $this->oneToMany('Token');
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
	 * Get access groups
	 *
	 * @return  object
	 */
	public function accessgroups()
	{
		return $this->oneToMany('Hubzero\Access\Map', 'user_id');
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
	 * Gets an attribute by key
	 *
	 * This will not retrieve properties directly attached to the model,
	 * even if they are public - those should be accessed directly!
	 *
	 * Also, make sure to access properties in transformers using the get method.
	 * Otherwise you'll just get stuck in a loop!
	 *
	 * @param   string  $key      The attribute key to get
	 * @param   mixed   $default  The value to provide, should the key be non-existent
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		if ($key == 'guest')
		{
			return $this->isGuest();
		}

		return parent::get($key, $default);
	}

	/**
	 * Is the current user a guest (logged out) or not?
	 *
	 * @return  boolean
	 */
	public function isGuest()
	{
		return $this->guest;
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
	 * Method to get a parameter value
	 *
	 * @param   string  $key      Parameter key
	 * @param   mixed   $default  Parameter default value
	 * @return  mixed   The value or the default if it did not exist
	 * @since   2.1.0
	 */
	public function getParam($key, $default = null)
	{
		return $this->params->get($key, $default);
	}

	/**
	 * Method to set a parameter
	 *
	 * @param   string  $key    Parameter key
	 * @param   mixed   $value  Parameter value
	 * @return  mixed   Set parameter value
	 * @since   2.1.0
	 */
	public function setParam($key, $value)
	{
		return $this->params->set($key, $value);
	}

	/**
	 * Method to set a default parameter if it does not exist
	 *
	 * @param   string  $key    Parameter key
	 * @param   mixed   $value  Parameter value
	 * @return  mixed   Set parameter value
	 * @since   2.1.0
	 */
	public function defParam($key, $value)
	{
		return $this->params->def($key, $value);
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
		static $fallback;

		if (!isset($fallback))
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

			$fallback = sprintf('data:image/svg+xml;base64,%s', base64_encode($image));
		}

		if (!$this->get('id') || $anonymous)
		{
			return $fallback;
		}

		$picture = null;

		foreach (self::$pictureResolvers as $resolver)
		{
			$picture = $resolver->picture($this->get('id'), $this->get('name'), $this->get('email'), $thumbnail);

			if ($picture)
			{
				break;
			}
		}

		if (!$picture)
		{
			$picture = $fallback;
		}

		return $picture;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired such as edit, delete, etc.
	 *
	 * @param   string  $type  The type of link to return
	 * @return  string
	 * @since   2.1.0
	 */
	public function link($type='')
	{
		if (!$this->get('id') || !self::$linkBase)
		{
			return '';
		}

		$link = str_replace(
			array(
				'{ID}',
				'{USERNAME}',
				'{EMAIL}',
				'{NAME}'
			),
			array(
				$this->get('id'),
				$this->get('username'),
				$this->get('email'),
				str_replace(' ', '+', $this->get('name'))
			),
			self::$linkBase
		);

		return $link;
	}

	/**
	 * Finds a user by username
	 *
	 * @param   string  $username
	 * @return  object
	 * @since   2.1.0
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
	 * @since   2.1.0
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
	 * @since   2.1.0
	 */
	public static function oneByActivationToken($token)
	{
		return self::all()
			->whereEquals('activation', $token)
			->row();
	}

	/**
	 * Pass through method to the table for setting the last visit date
	 *
	 * @param   integer  $timestamp  The timestamp, defaults to 'now'.
	 * @return  boolean  True on success.
	 * @since   2.1.0
	 */
	public function setLastVisit($timestamp = 'now')
	{
		$timestamp = new Date($timestamp);

		$this->set('lastvisitDate', $timestamp->toSql());

		return self::save();
	}

	/**
	 * Method to check User object authorisation against an access control
	 * object and optionally an access extension object
	 *
	 * @param   string   $action     The name of the action to check for permission.
	 * @param   string   $assetname  The name of the asset on which to perform the action.
	 * @return  boolean  True if authorised
	 * @since   2.1.0
	 */
	public function authorise($action, $assetname = null)
	{
		// Make sure we only check for core.admin once during the run.
		if ($this->isRoot === null)
		{
			$this->isRoot = false;

			// Check for the configuration file failsafe.
			$rootUser = \App::get('config')->get('root_user');

			// The root_user variable can be a numeric user ID or a username.
			if (is_numeric($rootUser) && $this->get('id') > 0 && $this->get('id') == $rootUser)
			{
				$this->isRoot = true;
			}
			elseif ($this->username && $this->username == $rootUser)
			{
				$this->isRoot = true;
			}
			else
			{
				// Get all groups against which the user is mapped.
				$identities = $this->getAuthorisedGroups();

				array_unshift($identities, $this->get('id') * -1);

				if (\JAccess::getAssetRules(1)->allow('core.admin', $identities))
				{
					$this->isRoot = true;
					return true;
				}
			}
		}

		return $this->isRoot ? true : \JAccess::check($this->get('id'), $action, $assetname);
	}

	/**
	 * Method to return a list of all categories that a user has permission for a given action
	 *
	 * @param   string  $component  The component from which to retrieve the categories
	 * @param   string  $action     The name of the section within the component from which to retrieve the actions.
	 * @return  array   List of categories that this group can do this action to (empty array if none). Categories must be published.
	 * @since   2.1.0
	 */
	public function getAuthorisedCategories($component, $action)
	{
		// Brute force method: get all published category rows for the component and check each one
		// TODO: Move to ORM-based models
		$db = \App::get('db');
		$query = $db->getQuery(true)
			->select('c.id AS id, a.name AS asset_name')
			->from('#__categories AS c')
			->innerJoin('#__assets AS a ON c.asset_id = a.id')
			->where('c.extension = ' . $db->quote($component))
			->where('c.published = 1');
		$db->setQuery($query);

		$allCategories = $db->loadObjectList('id');

		$allowedCategories = array();

		foreach ($allCategories as $category)
		{
			if ($this->authorise($action, $category->asset_name))
			{
				$allowedCategories[] = (int) $category->id;
			}
		}

		return $allowedCategories;
	}

	/**
	 * Gets an array of the authorised access levels for the user
	 *
	 * @return  array
	 * @since   2.1.0
	 */
	public function getAuthorisedViewLevels()
	{
		if (is_null($this->authLevels))
		{
			$this->authLevels = array();
		}

		if (empty($this->_authLevels))
		{
			$this->authLevels = \JAccess::getAuthorisedViewLevels($this->id);
		}

		return $this->authLevels;
	}

	/**
	 * Gets an array of the authorised user groups
	 *
	 * @return  array
	 * @since   2.1.0
	 */
	public function getAuthorisedGroups()
	{
		if (is_null($this->authGroups))
		{
			$this->authGroups = array();
		}

		if (empty($this->authGroups))
		{
			$this->authGroups = \JAccess::getGroupsByUser($this->id);
		}

		return $this->authGroups;
	}

	/**
	 * Save data
	 *
	 * @return  boolean
	 */
	public function save()
	{
		// Trigger the onUserBeforeSave event.
		$data  = $this->toArray();
		$isNew = $this->isNew();

		// Allow an exception to be thrown.
		try
		{
			$oldUser = self::oneOrNew($this->get('id'));

			// Trigger the onUserBeforeSave event.
			$result = \Event::trigger('user.onUserBeforeSave', array($oldUser->toArray(), $isNew, $data));

			if (in_array(false, $result, true))
			{
				// Plugin will have to raise its own error or throw an exception.
				return false;
			}

			// Save record
			$result = parent::save();

			if (!$result)
			{
				throw new \Exception($this->getError());
			}

			// Fire the onUserAfterSave event
			\Event::trigger('user.onUserAfterSave', array($data, $isNew, $result, $this->getError()));
		}
		catch (\Exception $e)
		{
			$this->addError($e->getMessage());

			$result = false;
		}

		return $result;
	}

	/**
	 * Delete the record and associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		$data = $this->toArray();

		// Trigger the onUserBeforeDelete event
		\Event::trigger('user.onUserBeforeDelete', array($data));

		// Remove associated data
		if (!$this->reputation->destroy())
		{
			$this->addError($this->reputation->getError());
			return false;
		}

		foreach ($this->tokens()->rows() as $token)
		{
			if (!$token->destroy())
			{
				$this->addError($token->getError());
				return false;
			}
		}

		// Attempt to delete the record
		$result = parent::destroy();

		if ($result)
		{
			// Trigger the onUserAfterDelete event
			\Event::trigger('user.onUserAfterDelete', array($data, true, $this->getError()));
		}

		return $result;
	}
}
