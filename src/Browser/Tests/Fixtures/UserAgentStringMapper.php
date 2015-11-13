<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Browser\Tests\Fixtures;

use SimpleXmlElement;

/**
 * User Agent String mapper
 */
class UserAgentStringMapper
{
	/**
	 * Map test data
	 *
	 * @return  array
	 */
	public static function map()
	{
		$collection = array();

		$xml = new SimpleXmlElement(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'UserAgentStrings.xml'));

		foreach ($xml->strings->string as $string)
		{
			$string = $string->field;

			$userAgentString = new UserAgentString();
			$userAgentString->setBrowser((string)$string[0]);
			$userAgentString->setBrowserVersion((string)$string[1]);
			$userAgentString->setOs((string)$string[2]);
			$userAgentString->setOsVersion((string)$string[3]);
			$userAgentString->setDevice((string)$string[4]);
			$userAgentString->setDeviceVersion((string)$string[5]);
			$userAgentString->setString(str_replace(array(PHP_EOL, '  '), ' ', (string)$string[6]));

			$collection[] = $userAgentString;
		}

		return $collection;
	}
}
