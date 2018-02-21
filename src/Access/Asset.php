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

use Hubzero\Database\Nested;

/**
 * Access asset
 */
class Asset extends Nested
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'lft';

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
		'title' => 'notempty',
		'name'  => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'parent_id'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('parent_id', function($data)
		{
			if (!isset($data['parent_id']) || $data['parent_id'] == 0)
			{
				return 'Entries must have a parent ID.';
			}

			$parent = self::oneOrNew($data['parent_id']);

			return $parent->get('id') ? false : 'The set parent does not exist.';
		});
	}

	/**
	 * Generates automatic parent_id field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParentId($data)
	{
		if (!isset($data['parent_id']) || $data['parent_id'] == 0)
		{
			$data['parent_id'] = self::getRootId();
		}

		return $data['parent_id'];
	}

	/**
	 * Method to load an asset by it's name.
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function oneByName($name)
	{
		return self::all()
			->whereEquals('name', $name)
			->row();
	}

	/**
	 * Method to load root node ID
	 *
	 * @return  integer
	 */
	public static function getRootId()
	{
		$result = self::all()
			->whereEquals('parent_id', 0)
			->row();

		if (!$result->get('id'))
		{
			$result = self::all()
				->whereEquals('lft', 0)
				->row();

			if (!$result->get('id'))
			{
				$result = self::all()
					->whereEquals('alias', 'root')
					->row();
			}
		}

		return $result->get('id');
	}
}
