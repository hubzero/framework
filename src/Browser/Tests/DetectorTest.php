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
use Hubzero\Browser\Tests\Fixtures\UserAgentStringMapper;

/**
 * Detector tests
 */
class DetectorTest extends Basic
{
	/**
	 * Tests the match() method.
	 *
	 * @covers  \Hubzero\Browser\Detector::match
	 * @return  void
	 **/
	public function testMatch()
	{
		$uas = UserAgentStringMapper::map();

		foreach ($uas as $userAgentString)
		{
			$browser = new Detector($userAgentString->getString());

			$this->assertEquals(strtolower($userAgentString->getBrowser()), $browser->name());
			$this->assertEquals($userAgentString->getBrowserVersion(), $browser->version());
			$this->assertEquals($userAgentString->getOs(), $browser->platform());
		}
	}
}
