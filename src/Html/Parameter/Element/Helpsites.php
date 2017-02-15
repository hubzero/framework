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

/**
 * Renders a helpsites element
 */
class Helpsites extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Helpsites';

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
		$helpsites = self::createSiteList(PATH_CORE . '/help/helpsites.xml', $value);
		array_unshift($helpsites, Builder\Select::option('', \App::get('language')->txt('local')));

		return Builder\Select::genericlist(
			$helpsites,
			$control_name . '[' . $name . ']',
			array(
				'id' => $control_name . $name,
				'list.attr' => 'class="inputbox"',
				'list.select' => $value
			)
		);
	}

	/**
	 * Builds a list of the help sites which can be used in a select option.
	 *
	 * @param   string  $pathToXml  Path to an XML file.
	 * @param   string  $selected   Language tag to select (if exists).
	 * @return  array   An array of arrays (text, value, selected).
	 */
	public static function createSiteList($pathToXml, $selected = null)
	{
		$list = array();
		$xml = false;

		if (!empty($pathToXml))
		{
			// Disable libxml errors and allow to fetch error information as needed
			libxml_use_internal_errors(true);

			// Try to load the XML file
			$xml = simplexml_load_file($pathToXml);
		}

		if (!$xml)
		{
			$option['text']  = 'English (US) hubzero.org';
			$option['value'] = 'http://hubzero.org/documentation';
			$list[] = $option;
		}
		else
		{
			$option = array();

			foreach ($xml->sites->site as $site)
			{
				$option['text']  = (string) $site;
				$option['value'] = (string) $site->attributes()->url;

				$list[] = $option;
			}
		}

		return $list;
	}
}
