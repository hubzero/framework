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

namespace Hubzero\Item;

use Hubzero\Database\Relational;
use Hubzero\User\Profile;
use Request;
use Lang;
use Date;
use User;

/**
 * Comment model
 */
class Comment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'item';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'content'
	);

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'content'   => 'notempty',
		'item_id'   => 'positive|nonzero',
		'item_type' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified',
		'modified_by',
		'item_type'
	);

	/**
	 * Return a formatted Created timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		if ($profile = Profile::getInstance($this->get('created_by')))
		{
			return $profile;
		}

		return new Profile;
	}

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticModified()
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @return  int
	 */
	public function automaticModifiedBy()
	{
		return User::get('id');
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticItemType($data)
	{
		return strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($data['item_type'])));
	}

	/**
	 * Determine if record was modified
	 * 
	 * @return  boolean  True if modified, false if not
	 */
	public function wasModified()
	{
		if ($this->get('modified') && $this->get('modified') != '0000-00-00 00:00:00')
		{
			return true;
		}

		return false;
	}

	/**
	 * Return a formatted Modified timestamp
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function modified($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Was the entry reported?
	 *
	 * @return  boolean  True if reported, False if not
	 */
	public function isReported()
	{
		return ($this->get('state') == 3);
	}

	/**
	 * Get either a count of or list of replies
	 *
	 * @param   array   $filters  Filters to apply to query
	 * @return  object
	 */
	public function replies($filters = array())
	{
		if (!isset($filters['item_id']))
		{
			$filters['item_id'] = $this->get('item_id');
		}

		if (!isset($filters['item_type']))
		{
			$filters['item_type'] = $this->get('item_type');
		}

		$entries = self::all()
			->whereEquals('parent', (int) $this->get('id'))
			->whereEquals('item_type', $filters['item_type'])
			->whereEquals('item_id', (int) $filters['item_id']);

		if (isset($filters['state']))
		{
			$entries->whereIn('state', (array) $filters['state']);
		}

		return $entries;
	}

	/**
	 * Get parent comment
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrFail($this->get('parent', 0));
	}

	/**
	 * Get a list of votes
	 *
	 * @return  object
	 */
	public function votes()
	{
		return $this->oneShiftsToMany('Hubzero\Item\Vote', 'item_id', 'item_type');
	}

	/**
	 * Get a list of files
	 *
	 * @return  object
	 */
	public function files()
	{
		return $this->oneToMany('Hubzero\Item\Comment\File', 'comment_id');
	}

	/**
	 * Check if a user has voted for this entry
	 *
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @param   string   $ip       IP Address
	 * @return  integer
	 */
	public function ballot($user_id = 0, $ip = null)
	{
		if (User::isGuest())
		{
			$vote = new Vote();
			$vote->set('item_type', 'comment');
			$vote->set('item_id', $this->get('id'));
			$vote->set('created_by', $user_id);
			$vote->set('ip', $ip);

			return $vote;
		}

		$user = $user_id ? User::getInstance($user_id) : User::getRoot();
		$ip   = $ip ?: Request::ip();

		// See if a person from this IP has already voted in the last week
		$votes = $this->votes();

		if ($user->get('id'))
		{
			$votes->whereEquals('created_by', $user->get('id'));
		}
		elseif ($ip)
		{
			$votes->whereEquals('ip', $ip);
		}

		$vote = $votes
			->ordered()
			->limit(1)
			->row();

		if (!$vote || !$vote->get('id'))
		{
			$vote = new Vote();
			$vote->set('item_type', 'comment');
			$vote->set('item_id', $this->get('id'));
			$vote->set('created_by', $user_id);
			$vote->set('ip', $ip);
		}

		return $vote;
	}

	/**
	 * Vote for the entry
	 *
	 * @param   integer  $vote     The vote [0, 1]
	 * @param   integer  $user_id  Optinal user ID to set as voter
	 * @param   string   $ip       Optional IP address
	 * @return  boolean  False if error, True on success
	 */
	public function vote($vote = 0, $user_id = 0, $ip = null)
	{
		if (!$this->get('id'))
		{
			$this->setError(Lang::txt('No record found'));
			return false;
		}

		if (!$vote)
		{
			$this->setError(Lang::txt('No vote provided'));
			return false;
		}

		$al = $this->ballot($user_id, $ip);
		$al->set('item_type', 'comment');
		$al->set('item_id', $this->get('id'));
		$al->set('created_by', $user_id);
		$al->set('ip', $ip);

		$vote = $al->automaticVote(['vote' => $vote]);

		if ($this->get('created_by') == $user_id)
		{
			$this->setError(Lang::txt('Cannot vote for your own entry'));
			return false;
		}

		if ($vote != $al->get('vote', 0))
		{
			if ($vote > 0)
			{
				$this->set('positive', (int) $this->get('positive') + 1);
				if ($al->get('id'))
				{
					$this->set('negative', (int) $this->get('negative') - 1);
				}
			}
			else
			{
				if ($al->get('id'))
				{
					$this->set('positive', (int) $this->get('positive') - 1);
				}
				$this->set('negative', (int) $this->get('negative') + 1);
			}

			if (!$this->save())
			{
				return false;
			}

			$al->set('vote', $vote);

			if (!$al->save())
			{
				$this->setError($al->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Saves the current model to the database
	 *
	 * @return  bool
	 */
	public function save()
	{
		// Make sure children inherit states
		if ($this->get('state') == self::STATE_DELETED
		 || $this->get('state') == self::STATE_UNPUBLISHED)
		{
			foreach ($this->replies() as $comment)
			{
				$comment->set('state', $this->get('state'));

				if (!$comment->save())
				{
					$this->setError($comment->getError());

					return false;
				}
			}
		}

		return parent::save();
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  bool  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove comments
		foreach ($this->replies() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->setError($comment->getError());
				return false;
			}
		}

		// Remove votes
		foreach ($this->votes() as $vote)
		{
			if (!$vote->destroy())
			{
				$this->setError($vote->getError());
				return false;
			}
		}

		// Remove files
		foreach ($this->files() as $file)
		{
			if (!$file->destroy())
			{
				$this->setError($file->getError());
				return false;
			}
		}

		return parent::destroy();
	}
}
