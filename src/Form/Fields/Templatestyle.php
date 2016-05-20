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
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Base\ClientManager;
use App;

/**
 * Supports a select grouped list of template styles
 */
class TemplateStyle extends GroupedList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'TemplateStyle';

	/**
	 * Method to get the list of template style options
	 * grouped by template.
	 * Use the client attribute to specify a specific client.
	 * Use the template attribute to specify a specific template
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		// Initialize variables.
		$groups = array();
		$lang = App::get('language');

		// Get the client and client_id.
		$clientName = $this->element['client'] ? (string) $this->element['client'] : 'site';
		$client = ClientManager::client($clientName, true);

		// Get the template.
		$template = (string) $this->element['template'];

		// Get the database object and a new query object.
		$db = App::get('db');
		$query = $db->getQuery(true);

		// Build the query.
		$query->select('s.id, s.title, e.name as name, s.template');
		$query->from('#__template_styles as s');
		$query->where('s.client_id = ' . (int) $client->id);
		$query->order('template');
		$query->order('title');
		if ($template)
		{
			$query->where('s.template = ' . $db->quote($template));
		}
		$query->join('LEFT', '#__extensions as e on e.element=s.template');
		$query->where('e.enabled=1');
		$query->where($db->quoteName('e.type') . '=' . $db->quote('template'));

		// Set the query and load the styles.
		$db->setQuery($query);
		$styles = $db->loadObjectList();

		// Build the grouped list array.
		if ($styles)
		{
			foreach ($styles as $style)
			{
				$template = $style->template;
					$lang->load('tpl_' . $template . '.sys', PATH_APP . '/templates/' . $template, null, false, true)
				||	$lang->load('tpl_' . $template . '.sys', PATH_CORE . '/templates/' . $template, null, false, true);
				$name = $lang->txt($style->name);

				// Initialize the group if necessary.
				if (!isset($groups[$name]))
				{
					$groups[$name] = array();
				}

				$groups[$name][] = Dropdown::option($style->id, $style->title);
			}
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
