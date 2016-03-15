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

namespace Hubzero\Activity;

use Hubzero\Database\Relational;
use Hubzero\User\Profile;
use Exception;
use Event;

/**
 * Activity log
 */
class Log extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'activity';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'details' => 'notempty',
		'action'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'//,
		//'uuid'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'scope'
	);

	/**
	 * Generate a UUID
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticUuid($data)
	{
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	/**
	 * Generates automatic scope field value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticScope($data)
	{
		if (!isset($data['scope']))
		{
			$data['scope'] = '';
		}
		return strtolower(preg_replace("/[^a-zA-Z0-9\-_\.]/", '', trim($data['scope'])));
	}

	/**
	 * Defines a belongs to one relationship between exposure and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Get recipients
	 *
	 * @return  object
	 */
	public function recipients()
	{
		return $this->oneToMany('Recipient', 'log_id');
	}

	/**
	 * Defines a belongs to one relationship between article and user
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
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		foreach ($this->recipients() as $recipient)
		{
			if (!$recipient->destroy())
			{
				$this->setError($recipient->getError());
				return false;
			}
		}

		$result = parent::destroy();

		if ($result)
		{
			Event::trigger('activity.onLogDelete', [$this]);
		}

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		$result = parent::save();

		if ($result)
		{
			Event::trigger('activity.onLogSave', [$this]);
		}

		return $result;
	}

	/**
	 * Create an activity log entry.
	 *
	 * @param   mixed    $data
	 * @param   array    $recipients
	 * @return  boolean
	 */
	public static function log($data = array(), $recipients = array())
	{
		if (is_object($data))
		{
			$data = (array) $data;
		}

		if (is_string($data))
		{
			$data = array('description' => $data);

			$data['action'] = 'create';

			if (substr(strtolower($data['description']), 0, 6) == 'update')
			{
				$data['action'] = 'update';
			}

			if (substr(strtolower($data['description']), 0, 6) == 'delete')
			{
				$data['action'] = 'delete';
			}
		}

		try
		{
			$activity = self::blank()->set($data);

			if (!$activity->save())
			{
				return false;
			}

			// Get everyone subscribed
			$subscriptions = Subscription::all()
				->whereEquals('scope', $activity->get('scope'))
				->whereEquals('scope_id', $activity->get('scope_id'))
				->rows();

			foreach ($subscriptions as $subscription)
			{
				$recipients[] = array(
					'scope'    => 'user',
					'scope_id' => $subscription->user_id
				);
			}

			$sent = array();

			// Do we have any recipients?
			foreach ($recipients as $receiver)
			{
				// Default to type 'user'
				if (!is_array($receiver))
				{
					$receiver = array(
						'scope'    => 'user',
						'scope_id' => $receiver
					);
				}

				// Make sure we have expected data
				if (!isset($receiver['scope'])
				 || !isset($receiver['scope_id']))
				{
					continue;
				}

				$key = $receiver['scope'] . '.' . $receiver['scope_id'];

				// No duplicate sendings
				if (in_array($key, $sent))
				{
					continue;
				}

				// Create a recipient object that ties a user to an activity
				$recipient = Recipient::blank()->set([
					'scope'    => $receiver['scope'],
					'scope_id' => $receiver['scope_id'],
					'log_id'   => $activity->get('id'),
					'state'    => 1
				]);

				$recipient->save();

				$sent[] = $key;
			}
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}
}
