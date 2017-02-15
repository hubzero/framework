<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder;
use App;

/**
 * Renders a editors element
 */
class Editors extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Editors';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$db = App::get('db');

		$query = $db->getQuery()
			->select('element', 'value')
			->select('name', 'text')
			->from('#__extensions')
			->whereEquals('folder', 'editors')
			->whereEquals('type', 'plugin')
			->whereEquals('enabled', 1)
			->order('ordering', 'asc')
			->order('name', 'asc');

		$db->setQuery($query->toString());
		$editors = $db->loadObjectList();

		array_unshift($editors, Builder\Select::option('', App::get('language')->txt('JOPTION_SELECT_EDITOR')));

		return Builder\Select::genericlist(
			$editors,
			$control_name . '[' . $name . ']',
			array(
				'id'          => $control_name . $name,
				'list.attr'   => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}
}
