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

namespace Hubzero\User\Group;

use Hubzero\Database\Relational;

/**
 * Group email invite table class
 */
class InviteEmail extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xgroups';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__xgroups_inviteemails';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'email';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'gidNumber' => 'positive|nonzero',
		'email'     => 'notempty',
		'token'     => 'notempty'
	);

	/**
	 * Get parent group
	 *
	 * @return  object
	 */
	public function group()
	{
		return \Hubzero\User\Group::getInstance($this->get('gidNumber'));
	}

	/**
	 * Get a list of email invites for a group
	 *
	 * @param   integer  $gid         Group ID
	 * @param   boolean  $email_only  Resturn only email addresses?
	 * @return  array
	 */
	public function getInviteEmails($gid, $email_only = false)
	{
		$final = array();

		$invitees = self::all()
			->whereEquals('gidNumber', $gid)
			->ordered()
			->rows();

		if ($email_only)
		{
			foreach ($invitees as $invitee)
			{
				$final[] = $invitee->get('email');
			}
		}
		else
		{
			$final = $invitees;
		}

		return $final;
	}

	/**
	 * Add a list of emails to a group as invitees
	 *
	 * @param   integer  $gid     Group ID
	 * @param   array    $emails  Array of email addresses
	 * @return  array
	 */
	public function addInvites($gid, $emails)
	{
		$exists = array();
		$added  = array();

		$current = $this->getInviteEmails($gid, true);

		foreach ($emails as $e)
		{
			if (in_array($e, $current))
			{
				$exists[] = $e;
			}
			else
			{
				$added[] = $e;
			}
		}

		if (count($added) > 0)
		{
			foreach ($added as $a)
			{
				$model = self::blank();
				$model->set([
					'email'     => $a,
					'gidNumber' => $gid,
					'token'     => md5($a)
				]);
				$model->save();
			}
		}

		$return['exists'] = $exists;
		$return['added']  = $added;

		return $return;
	}

	/**
	 * Remove Invite Emails
	 *
	 * @param   integer  $gid     Group ID
	 * @param   array    $emails  Array of email addresses
	 * @return  void
	 */
	public function removeInvites($gid, $emails)
	{
		foreach ($emails as $email)
		{
			$model = self::all()
				->whereEquals('gidNumber', $gid)
				->whereEquals('email', $email)
				->row();

			if ($model->get('id'))
			{
				$model->destroy();
			}
		}
	}
}
