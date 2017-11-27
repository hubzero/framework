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

namespace Hubzero\Api\Component;

use Hubzero\Component\Loader as Base;

/**
 * Component helper class
 */
class Loader extends Base
{
	/**
	 * Render the component.
	 *
	 * @param   string  $option  The component option.
	 * @param   array   $params  The component parameters
	 * @return  bool
	 */
	public function render($option, $params = array())
	{
		$lang = $this->app['language'];

		if (empty($option))
		{
			// Throw 404 if no component
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		$option = $this->canonical($option);

		// Record the scope
		$scope = $this->app->has('scope') ? $this->app->get('scope') : null;

		// Set scope to component name
		$this->app->set('scope', $option);

		// Build the component path.
		$client = (isset($this->app['client']->alias) ? $this->app['client']->alias : $this->app['client']->name);

		// Set path and constants
		define('PATH_COMPONENT', $this->path($option) . DIRECTORY_SEPARATOR . $client);
		define('PATH_COMPONENT_SITE', $this->path($option) . DIRECTORY_SEPARATOR . 'site');
		define('PATH_COMPONENT_ADMINISTRATOR', $this->path($option) . DIRECTORY_SEPARATOR . 'admin');

		// Legacy compatibility
		// @TODO: Deprecate this!
		define('JPATH_COMPONENT', PATH_COMPONENT);
		define('JPATH_COMPONENT_SITE', PATH_COMPONENT_SITE);
		define('JPATH_COMPONENT_ADMINISTRATOR', PATH_COMPONENT_ADMINISTRATOR);

		$version    = $this->app['request']->getVar('version');
		$controller = $this->app['request']->getCmd('controller', 'api');

		// If no version is specified, try to determine the most
		// recent version from the available controllers
		if (!$version)
		{
			$files = glob(PATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . 'v*.php');

			if (!empty($files))
			{
				natsort($files);

				$file = end($files);
				$controller = basename($file, '.php');
			}
		}
		else
		{
			$controller .= 'v' . str_replace('.', '_', $version);
		}

		$path       = PATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
		$controller = '\\Components\\' . ucfirst(substr($option, 4)) . '\\Api\\Controllers\\' . ucfirst($controller);
		$found      = false;

		// Make sure the component is enabled
		if ($this->isEnabled($option))
		{
			// Include the file
			if (file_exists($path))
			{
				require_once $path;
			}

			// Check to see if the class exists
			if (class_exists($controller))
			{
				$found = true;

				$lang->load($option, PATH_COMPONENT, null, false, true);
			}
		}

		if (!$found)
		{
			$this->app->abort(404, $lang->translate('JLIB_APPLICATION_ERROR_COMPONENT_NOT_FOUND'));
		}

		// Handle template preview outlining.
		$action = new $controller($this->app->get('response'));
		$action->execute();

		// Revert the scope
		$this->app->forget('scope');
		$this->app->set('scope', $scope);

		return true;
	}

	/**
	 * Execute the component.
	 *
	 * @param   string  $path  The component path.
	 * @return  string  The component output
	 */
	protected function execute($path)
	{
		return '';
	}
}
