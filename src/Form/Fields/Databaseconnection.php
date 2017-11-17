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

use App;

/**
 * Provides a list of available database connections, optionally limiting to
 * a given list.
 */
class Databaseconnection extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Databaseconnection';

	/**
	 * Method to get the list of database options.
	 *
	 * This method produces a drop down list of available databases supported
	 * by Database drivers that are also supported by the application.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		// This gets the connectors available in the platform and supported by the server.
		$available = App::get('db')->getConnectors();
		$available = array_map('strtolower', $available);

		// This gets the list of database types supported by the application.
		// This should be entered in the form definition as a comma separated list.
		// If no supported databases are listed, it is assumed all available databases
		// are supported.
		$supported = $this->element['supported'];
		if (!empty($supported))
		{
			$supported = explode(',', $supported);
			foreach ($supported as $support)
			{
				if (in_array($support, $available))
				{
					$options[$support] = ucfirst($support);
				}
			}
		}
		else
		{
			foreach ($available as $support)
			{
				$options[$support] = ucfirst($support);
			}
		}

		// This will come into play if an application is installed that requires
		// a database that is not available on the server.
		if (empty($options))
		{
			$options[''] = App::get('language')->txt('JNONE');
		}
		return $options;
	}
}
