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

namespace Hubzero\Html\Builder;

use Hubzero\Error\Exception\RuntimeException;
use Hubzero\Base\Object;
use Lang;
use App;

/**
 * Utility class working with content language select lists
 */
class ContentLanguage
{
	/**
	 * Cached array of the content language items.
	 *
	 * @var  array
	 */
	protected static $items = null;

	/**
	 * Get a list of the available content language items.
	 *
	 * @param   boolean  $all        True to include All (*)
	 * @param   boolean  $translate  True to translate All
	 * @return  string
	 */
	public static function existing($all = false, $translate = false)
	{
		if (empty(self::$items))
		{
			// Get the database object and a new query object.
			$db = App::get('db');

			// Build the query.
			$query = $db->getQuery()
				->select('a.lang_code', 'value')
				->select('a.title', 'text')
				->select('a.title_native')
				->from('#__languages', 'a')
				->where('a.published', '>=', '0')
				->order('a.title', 'asc');

			// Set the query and load the options.
			$db->setQuery($query->toString());
			self::$items = $db->loadObjectList();
			if ($all)
			{
				array_unshift(self::$items, new Object(array('value' => '*', 'text' => $translate ? Lang::alt('JALL', 'language') : 'JALL_LANGUAGE')));
			}

			// Detect errors
			if ($db->getErrorNum())
			{
				throw new RuntimeException($db->getErrorMsg(), 500, E_WARNING);
			}
		}
		return self::$items;
	}
}
