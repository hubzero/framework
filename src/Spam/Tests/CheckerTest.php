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

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;
use Hubzero\Spam\Checker;
use Hubzero\Spam\Tests\Mock\Detector;

/**
 * Spam checker tests
 */
class CheckerTest extends Basic
{
	/**
	 * Test to make sure a negative number fails validation
	 *
	 * @return  void
	 **/
	public function testRegisterDetector()
	{
		$service = new Checker();

		$this->assertInstanceOf('Hubzero\Spam\Checker', $service->registerDetector(new Detector()));

		$this->setExpectedException('RuntimeException');

		$service->registerDetector(new Detector());
	}

	/**
	 * Test to make sure a zero fails validation
	 *
	 * @return  void
	 **/
	public function testGetDetectors()
	{
		$d = new Detector();
		$k = get_class($d);

		$data = [];
		$data[$k] = $d;

		$service = new Checker();
		$service->registerDetector($data[$k]);

		$detectors = $service->getDetectors();

		$this->assertTrue(is_array($detectors), 'Getting all detectors should return an array');
		$this->assertCount(1, $detectors, 'Get detectors should have returned one detector');
		$this->assertEquals($detectors, $data);
	}
}
