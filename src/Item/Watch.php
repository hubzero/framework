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
use User;

/**
 * Item Announcement
 */
class Watch extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'item';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__item_watch';

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
		'item_id'   => 'positive|nonzero',
		'item_type' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'email',
		'item_type'
	);

	/**
	 * Generates automatic email field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticEmail($data)
	{
		if (!isset($data['email']))
		{
			$data['email'] = User::get('email');
		}

		return $data['email'];
	}

	/**
	 * Generates automatic email field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticItemType($data)
	{
		if (isset($data['item_type']))
		{
			$data['item_type'] = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($data['item_type'])));
		}

		return $data['item_type'];
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
	 * Is user watching item?
	 *
	 * @param   integer  $item_id
	 * @param   string   $item_type
	 * @param   integer  $created_by
	 * @return  boolean
	 */
	public static function isWatching($item_id, $item_type, $created_by)
	{
		if ($item_id && $item_type && $created_by)
		{
			$total = self::all()
				->whereEquals('state', 1)
				->whereEquals('created_by', (int)$created_by)
				->whereEquals('item_id', (int)$item_id)
				->whereEquals('item_type', (string)$item_type)
				->total();

			if ($total)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Load a record by scope and scope ID
	 *
	 * @param   integer  $item_id
	 * @param   string   $item_type
	 * @param   integer  $created_by
	 * @param   string   $email
	 * @return  object
	 */
	public static function oneByScope($item_id, $item_type, $created_by = 0, $email = null)
	{
		$model = self::all()
			->whereEquals('item_id', (int)$item_id)
			->whereEquals('item_type', (string)$item_type);

		if ($created_by)
		{
			$model->whereEquals('created_by', (int)$created_by);
		}

		if ($email)
		{
			$model->whereEquals('email', (string)$email);
		}

		return $model->row();
	}
}
