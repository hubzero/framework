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

namespace Hubzero\Menu\Type;

/**
 * Site Menu class
 */
class Site extends Base
{
	/**
	 * Loads the entire menu table into memory.
	 *
	 * @return  array
	 */
	public function load()
	{
		if (!($this->get('db') instanceof \Hubzero\Database\Driver))
		{
			return;
		}

		// Initialise variables.
		$db    = $this->get('db');
		$query = $db->getQuery(true);

		$query->select('m.id, m.menutype, m.title, m.alias, m.note, m.path AS route, m.link, m.type, m.level, m.language');
		$query->select('m.browserNav, m.access, m.params, m.home, m.img, m.template_style_id, m.component_id, m.parent_id');
		$query->select('e.element as component');
		$query->from('#__menu AS m');
		$query->leftJoin('#__extensions AS e ON m.component_id = e.extension_id');
		$query->where('m.published = 1');
		$query->where('m.parent_id > 0');
		$query->where('m.client_id = 0');
		$query->order('m.lft');

		// Set the query
		$db->setQuery($query);

		$this->_items = $db->loadObjectList('id');

		foreach ($this->_items as &$item)
		{
			// Get parent information.
			$parent_tree = array();
			if (isset($this->_items[$item->parent_id]))
			{
				$parent_tree  = $this->_items[$item->parent_id]->tree;
			}

			// Create tree.
			$parent_tree[] = $item->id;
			$item->tree = $parent_tree;

			// Create the query array.
			$url = str_replace('index.php?', '', $item->link);
			$url = str_replace('&amp;', '&', $url);

			parse_str($url, $item->query);
		}
	}

	/**
	 * Gets menu items by attribute
	 *
	 * @param   string   $attributes  The field name
	 * @param   string   $values      The value of the field
	 * @param   boolean  $firstonly   If true, only returns the first item found
	 * @return  array
	 */
	public function getItems($attributes, $values, $firstonly = false)
	{
		$attributes = (array) $attributes;
		$values     = (array) $values;

		// Filter by language if not set
		if (($key = array_search('language', $attributes)) === false)
		{
			if ($this->get('language_filter'))
			{
				$attributes[] = 'language';
				$values[]     = array($this->get('language'), '*');
			}
		}
		elseif ($values[$key] === null)
		{
			unset($attributes[$key]);
			unset($values[$key]);
		}

		// Filter by access level if not set
		if (($key = array_search('access', $attributes)) === false)
		{
			$attributes[] = 'access';
			$values[]     = $this->get('access');
		}
		elseif ($values[$key] === null)
		{
			unset($attributes[$key]);
			unset($values[$key]);
		}

		return parent::getItems($attributes, $values, $firstonly);
	}

	/**
	 * Get menu item by id
	 *
	 * @param   string  $language  The language code.
	 * @return  mixed   The item object
	 */
	public function getDefault($language = '*')
	{
		if (array_key_exists($language, $this->_default) && $this->get('language_filter'))
		{
			return $this->_items[$this->_default[$language]];
		}

		if (array_key_exists('*', $this->_default))
		{
			return $this->_items[$this->_default['*']];
		}

		return 0;
	}
}
