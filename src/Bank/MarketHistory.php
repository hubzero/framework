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

namespace Hubzero\Bank;

use Hubzero\Database\Relational;

/**
 * Market History class:
 * Logs batch transactions, royalty distributions and other big transactions
 */
class MarketHistory extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'market';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__market_history';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

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
		'itemid'   => 'positive|nonzero',
		'category' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'date'
	);

	/**
	 * Generates automatic date value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticDate($data)
	{
		if (!isset($data['date']))
		{
			$dt = new \Hubzero\Utility\Date('now');

			$data['date'] = $dt->toSql();
		}

		return $data['date'];
	}

	/**
	 * Get the ID of a record matching the data passed
	 *
	 * @param   mixed    $itemid    Integer
	 * @param   string   $action    Transaction type
	 * @param   string   $category  Transaction category
	 * @param   string   $created   Transaction date
	 * @param   string   $log       Transaction log
	 * @return  integer
	 */
	public static function getRecord($itemid=0, $action='', $category='', $created='', $log = '')
	{
		$model = self::all()
			->select('id');

		if ($category)
		{
			$model->whereEquals('category', $category);
		}

		if ($itemid)
		{
			$model->whereEquals('itemid', $itemid);
		}

		if ($action)
		{
			$model->whereEquals('action', $action);
		}

		if ($created)
		{
			$model->whereLike('date', $created . '%');
		}

		if ($log)
		{
			$model->whereEquals('log', $log);
		}

		$row = $model->row();

		return $row->get('id');
	}
}
