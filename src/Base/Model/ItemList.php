<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Model;

/**
 * Extended Iterator class
 */
class ItemList extends \Hubzero\Base\ItemList
{
	/**
	 * Fetch Item with key equal to value
	 *
	 * @param   $key    Object Key
	 * @param   $value  Object Value
	 * @return  mixed
	 */
	public function fetch($key, $value)
	{
		foreach ($this->_data as $data)
		{
			if ($data->get($key) == $value)
			{
				return $data;
			}
		}
		return null;
	}

	/**
	 * Lists a specific key from the item list
	 *
	 * @param   string  $key      Key to grab from item
	 * @param   string  $default  Default value if key is empty
	 * @return  array   Array of keys
	 */
	public function lists($key, $default = null)
	{
		$results = array();
		foreach ($this->_data as $data)
		{
			array_push($results, $data->get($key));
		}
		return $results;
	}
}
