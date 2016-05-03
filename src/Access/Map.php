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

namespace Hubzero\Access;

use Hubzero\Database\Relational;

/**
 * User/Group map
 */
class Map extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'user_usergroup';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__user_usergroup_map';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'user_id';

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
		'user_id'  => 'positive|nonzero',
		'group_id' => 'positive|nonzero'
	);

	/**
	 * Defines a relationship to the User/Group Map
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Defines a relationship to the User/Group Map
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne('Hubzero\Access\Group', 'group_id');
	}

	/**
	 * Delete this object and its dependencies
	 *
	 * @return  boolean
	 */
	public function destroy()
	{
		$query = $this->getQuery()
			->delete($this->getTableName())
			->whereEquals('group_id', $this->get('group_id'))
			->whereEquals('user_id', $this->get('user_id'));

		if (!$query->execute())
		{
			$this->addError($query->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete objects of this type by Access Group ID
	 *
	 * @param   mixed    $group_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function destroyByGroup($group_id)
	{
		$group_id = (is_array($group_id) ? $group_id : array($group_id));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereIn('group_id', $group_id);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete objects of this type by User ID
	 *
	 * @param   mixed    $user_id  Integer or array of integers
	 * @return  boolean
	 */
	public static function destroyByUser($user_id)
	{
		$user_id = (is_array($user_id) ? $user_id : array($user_id));

		$blank = self::blank();

		$query = $blank->getQuery()
			->delete($blank->getTableName())
			->whereIn('user_id', $user_id);

		if (!$query->execute())
		{
			return false;
		}

		return true;
	}
}
