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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model\Hook;

use Hubzero\Base\Obj;
use Hubzero\Content\Import\Model\Hook;

/**
 * Import Hook archive model
 */
class Archive extends Obj
{
	/**
	 * Type
	 *
	 * @var  string
	 */
	private $type = null;

	/**
	 * Constructor
	 *
	 * @param   string  $type
	 * @return  void
	 */
	public function __construct($type = null)
	{
		$this->type = $type;
	}

	/**
	 * Get Instance of Archive
	 *
	 * @param   string  $key  Instance Key
	 * @return  object
	 */
	static function &getInstance($key=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$key]))
		{
			$instances[$key] = new static($key);
		}

		return $instances[$key];
	}

	/**
	 * Get a count or list of import hooks
	 *
	 * @param   string   $rtrn     What data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   boolean  $boolean  Clear cached data?
	 * @return  mixed
	 */
	public function hooks($rtrn = 'list', $filters = array(), $clear = false)
	{
		$model = Hook::all();

		if (isset($filters['state']) && $filters['state'])
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$filters['state'] = array_map('intval', $filters['state']);

			$model->whereIn('state', $filters['state']);
		}

		if (!isset($filters['type']))
		{
			$filters['type'] = $this->type;
		}

		if (isset($filters['type']) && $filters['type'])
		{
			$model->whereEquals('type', $filters['type']);
		}

		if (isset($filters['event']) && $filters['event'])
		{
			$model->whereEquals('event', $filters['event']);
		}

		if (isset($filters['created_by']) && $filters['created_by'] >= 0)
		{
			$model->whereEquals('created_by', $filters['created_by']);
		}

		if (strtolower($rtrn) == 'count')
		{
			return $model->total();
		}

		return $model->ordered()
			->paginated()
			->rows();
	}
}
