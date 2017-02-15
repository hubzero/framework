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
use Hubzero\Filesystem\Util;
use App;

/**
 * Parameter to display a list of the layouts for a component view from the extension or default template overrides.
 */
class ComponentLayouts extends Select
{
	/**
	 * @var  string
	 */
	protected $_name = 'ComponentLayouts';

	/**
	 * Get the options for the list.
	 *
	 * @param   object  &$node  XMLElement node object containing the settings for the element
	 * @return  array
	 */
	protected function _getOptions(&$node)
	{
		$options = array();
		$path1 = null;
		$path2 = null;

		// Load template entries for each menuid
		$db = App::get('db');

		$query = $db->getQuery()
			->select('template')
			->from('#__template_styles')
			->whereEquals('client_id', 0)
			->whereEquals('home', 1)
			->limit(1);
		$db->setQuery($query->toString());
		$template = $db->loadResult();

		$view = (string) $node['view'];
		$extn = (string) $node['extension'];
		if ($view && $extn)
		{
			$view = preg_replace('#\W#', '', $view);
			$extn = preg_replace('#\W#', '', $extn);
			$path1 = PATH_CORE . '/components/' . $extn . '/site/views/' . $view . '/tmpl';
			$path2 = PATH_ROOT . '/templates/' . $template . '/html/' . $extn . '/' . $view;
			$options[] = Builder\Select::option('', App::get('language')->txt('JOPTION_USE_MENU_REQUEST_SETTING'));
		}

		if ($path1 && $path2)
		{
			$path1 = Util::normalizePath($path1);
			$path2 = Util::normalizePath($path2);

			$files = App::get('filesystem')->files($path1, '^[^_]*\.php$');
			foreach ($files as $file)
			{
				$options[] = Builder\Select::option(App::get('filesystem')->extension($file));
			}

			if (is_dir($path2) && $files = App::get('filesystem')->files($path2, '^[^_]*\.php$'))
			{
				$options[] = Builder\Select::optgroup(App::get('language')->txt('JOPTION_FROM_DEFAULT_TEMPLATE'));
				foreach ($files as $file)
				{
					$options[] = Builder\Select::option(App::get('filesystem')->extension($file));
				}
				$options[] = Builder\Select::optgroup(App::get('language')->txt('JOPTION_FROM_DEFAULT_TEMPLATE'));
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::_getOptions($node), $options);

		return $options;
	}
}
