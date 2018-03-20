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

namespace Hubzero\Browser\Tests;

use Hubzero\Test\Basic;
use Hubzero\Browser\Detector;
use SimpleXmlElement;
use stdClass;

/**
 * Detector tests
 */
class DetectorTest extends Basic
{
	/**
	 * Tests the match() method.
	 *
	 * @covers  \Hubzero\Browser\Detector::match
	 * @covers  \Hubzero\Browser\Detector::agent
	 * @covers  \Hubzero\Browser\Detector::name
	 * @covers  \Hubzero\Browser\Detector::version
	 * @covers  \Hubzero\Browser\Detector::platform
	 * @covers  \Hubzero\Browser\Detector::_setPlatform
	 * @return  void
	 **/
	public function testMatch()
	{
		$uas = self::map();

		foreach ($uas as $userAgentString)
		{
			$browser = new Detector($userAgentString->string);

			$this->assertEquals($userAgentString->string, $browser->agent());
			$this->assertEquals(strtolower($userAgentString->browser), $browser->name());
			$this->assertEquals($userAgentString->browserVersion, $browser->version());
			$this->assertEquals($userAgentString->os, $browser->platform());
		}
	}

	/**
	 * Map test data
	 *
	 * @return  array
	 */
	private static function map()
	{
		$collection = array();

		$xml = new SimpleXmlElement(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'UserAgentStrings.xml'));

		if ($xml)
		{
			foreach ($xml->strings->string as $string)
			{
				$string = $string->field;

				$userAgentString = new stdClass();
				$userAgentString->browser        = (string)$string[0];
				$userAgentString->browserVersion = (string)$string[1];
				$userAgentString->os             = (string)$string[2];
				$userAgentString->osVersion      = (string)$string[3];
				$userAgentString->device         = (string)$string[4];
				$userAgentString->deviceVersion  = (string)$string[5];
				$userAgentString->string         = str_replace(array(PHP_EOL, '  '), ' ', (string)$string[6]);

				$collection[] = $userAgentString;
			}
		}

		return $collection;
	}
}
