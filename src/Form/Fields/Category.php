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

use Hubzero\Html\Builder\Category as Cat;
use Hubzero\Html\Builder\Select as Dropdown;
use App;

/**
 * Supports an HTML select list of categories
 */
class Category extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Category';

	/**
	 * Method to get the field options for category
	 * Use the extension attribute in a form to specify the.specific extension for
	 * which categories should be displayed.
	 * Use the show_root attribute to specify whether to show the global category root in the list.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options   = array();
		$extension = $this->element['extension'] ? (string) $this->element['extension'] : (string) $this->element['scope'];
		$published = (string) $this->element['published'];
		$name      = (string) $this->element['name'];

		// Load the category options for a given extension.
		if (!empty($extension))
		{
			// Filter over published state or not depending upon if it is present.
			if ($published)
			{
				$options = Cat::options($extension, array('filter.published' => explode(',', $published)));
			}
			else
			{
				$options = Cat::options($extension);
			}

			// Verify permissions.  If the action attribute is set, then we scan the options.
			if ((string) $this->element['action'])
			{

				// Get the current user object.
				$user = App::get('user')->getInstance();

				// For new items we want a list of categories you are allowed to create in.
				if (!$this->form->getValue($name))
				{
					foreach ($options as $i => $option)
					{
						// To take save or create in a category you need to have create rights for that category
						// unless the item is already in that category.
						// Unset the option if the user isn't authorised for it. In this field assets are always categories.
						if ($user->authorise('core.create', $extension . '.category.' . $option->value) != true)
						{
							unset($options[$i]);
						}
					}
				}
				// If you have an existing category id things are more complex.
				else
				{
					$categoryOld = $this->form->getValue($name);

					foreach ($options as $i => $option)
					{
						// If you are only allowed to edit in this category but not edit.state, you should not get any
						// option to change the category.
						if ($user->authorise('core.edit.state', $extension . '.category.' . $categoryOld) != true)
						{
							if ($option->value != $categoryOld)
							{
								unset($options[$i]);
							}
						}
						// However, if you can edit.state you can also move this to another category for which you have
						// create permission and you should also still be able to save in the current category.
						elseif (($user->authorise('core.create', $extension . '.category.' . $option->value) != true) && $option->value != $categoryOld)
						{
							unset($options[$i]);
						}
					}
				}
			}

			if (isset($this->element['show_root']))
			{
				array_unshift($options, Dropdown::option('0', App::get('language')->txt('JGLOBAL_ROOT')));
			}
		}
		else
		{
			App::abort(500, App::get('language')->txt('JLIB_FORM_ERROR_FIELDS_CATEGORY_ERROR_EXTENSION_EMPTY'));
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
