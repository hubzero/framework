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

namespace Hubzero\Html\Toolbar\Button;

use Hubzero\Html\Toolbar\Button;

/**
 * Renders a help popup window button
 *
 * Inspired by Joomla's JButtonHelp class
 */
class Help extends Button
{
	/**
	 * Button type
	 *
	 * @var  string
	 */
	protected $_name = 'Help';

	/**
	 * Fetches the button HTML code.
	 *
	 * @param   string   $type       Unused string.
	 * @param   string   $ref        The name of the help screen (its key reference).
	 * @param   boolean  $com        Use the help file in the component directory.
	 * @param   string   $override   Use this URL instead of any other.
	 * @param   string   $component  Name of component to get Help (null for current component)
	 * @return  string
	 */
	public function fetchButton($type = 'Help', $url = null, $width = 700, $height = 500)
	{
		$text   = \Lang::txt('JTOOLBAR_HELP');
		$class  = $this->fetchIconClass('help');
		if (!strstr('?', $url) && !strstr('&', $url) && substr($url, 0, 4) != 'http')
		{
			$url = \Route::url('index.php?option=com_help&component=' . \Request::getCmd('option') . '&page=' . $url);
			$doTask = "Joomla.popupWindow('$url', '" . \Lang::txt('JHELP', true) . "', {$width}, {$height}, 1)";
		}
		else
		{
			$doTask = $this->_getCommand($ref, $com, $override, $component);
		}

		$html  = '<a data-title="' . $text . '" href="#" onclick="' . $doTask . '" rel="help" class="toolbar">' . "\n";
		$html .= '<span class="' . $class . '">' . "\n" . $text . "\n" . '</span>' . "\n";
		$html .= '</a>' . "\n";

		return $html;
	}

	/**
	 * Get the button id
	 *
	 * @return  string	Button CSS Id
	 */
	public function fetchId()
	{
		return $this->_parent->getName() . '-' . 'help';
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string   $ref        The name of the help screen (its key reference).
	 * @param   boolean  $com        Use the help file in the component directory.
	 * @param   string   $override   Use this URL instead of any other.
	 * @param   string   $component  Name of component to get Help (null for current component)
	 * @return  string   JavaScript command string
	 */
	protected function _getCommand($ref, $com, $override, $component)
	{
		// Get Help URL
		$url = self::createURL($ref, $com, $override, $component);
		$url = htmlspecialchars($url, ENT_QUOTES);
		$cmd = "Joomla.popupWindow('$url', '" . \Lang::txt('JHELP', true) . "', 700, 500, 1)";

		return $cmd;
	}

	/**
	 * Create a URL for a given help key reference
	 *
	 * @param   string   $ref           The name of the help screen (its key reference)
	 * @param   boolean  $useComponent  Use the help file in the component directory
	 * @param   string   $override      Use this URL instead of any other
	 * @param   string   $component     Name of component (or null for current component)
	 * @return  string
	 */
	public static function createURL($ref, $useComponent = false, $override = null, $component = null)
	{
		$local = false;

		//  Determine the location of the help file.  At this stage the URL
		//  can contain substitution codes that will be replaced later.

		if ($override)
		{
			$url = $override;
		}
		else
		{
			// Get the user help URL.
			$user = \User::getInstance();
			$url = $user->getParam('helpsite');

			// If user hasn't specified a help URL, then get the global one.
			if ($url == '')
			{
				$url = $app->getCfg('helpurl');
			}

			// Component help URL overrides user and global.
			if ($useComponent)
			{
				// Look for help URL in component parameters.
				$params = \Component::params($component);
				$url = $params->get('helpURL');

				if ($url == '')
				{
					$local = true;
					$url = 'components/{component}/help/{language}/{keyref}';
				}
			}

			// Set up a local help URL.
			if (!$url)
			{
				$local = true;
				$url = 'help/{language}/{keyref}';
			}
		}

		// If the URL is local then make sure we have a valid file extension on the URL.
		if ($local)
		{
			if (!preg_match('#\.html$|\.xml$#i', $ref))
			{
				$url .= '.html';
			}
		}

		//  Replace substitution codes in the URL.
		$lang    = \App::get('language');
		$version = HVERSION;
		$hver    = explode('.', $version);
		$hlang   = explode('-', $lang->getTag());

		$debug  = $lang->setDebug(false);
		$keyref = $lang->txt($ref);
		$lang->setDebug($debug);

		// Replace substitution codes in help URL.
		$search = array(
			'{app}', // Application name (eg. 'Administrator')
			'{component}', // Component name (eg. 'com_content')
			'{keyref}', // Help screen key reference
			'{language}', // Full language code (eg. 'en-GB')
			'{langcode}', // Short language code (eg. 'en')
			'{langregion}', // Region code (eg. 'GB')
			'{major}', // Joomla major version number
			'{minor}', // Joomla minor version number
			'{maintenance}'// Joomla maintenance version number
		);

		$replace = array(
			\App::get('client')->name, // {app}
			$component, // {component}
			$keyref, // {keyref}
			$lang->getTag(), // {language}
			$hlang[0], // {langcode}
			$hlang[1], // {langregion}
			$hver[0], // {major}
			$hver[1], // {minor}
			$hver[2]// {maintenance}
		);

		// If the help file is local then check it exists.
		// If it doesn't then fallback to English.
		if ($local)
		{
			$try = str_replace($search, $replace, $url);

			if (!\Filesystem::exists(PATH_ROOT . '/' . $try))
			{
				$replace[3] = 'en-GB';
				$replace[4] = 'en';
				$replace[5] = 'GB';
			}
		}

		$url = str_replace($search, $replace, $url);

		return $url;
	}
}
