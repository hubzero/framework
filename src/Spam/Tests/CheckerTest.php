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
use Hubzero\Spam\StringProcessor\NoneStringProcessor;
use Hubzero\Spam\StringProcessor\NativeStringProcessor;

/**
 * Spam checker tests
 */
class CheckerTest extends Basic
{
	/**
	 * Tests for setting and getting a StringProcessor
	 *
	 * @covers  \Hubzero\Spam\Checker::setStringProcessor
	 * @covers  \Hubzero\Spam\Checker::getStringProcessor
	 * @return  void
	 **/
	public function testStringProcessor()
	{
		$service = new Checker(new NoneStringProcessor());

		$this->assertInstanceOf('Hubzero\Spam\StringProcessor\NoneStringProcessor', $service->getStringProcessor());

		$service->setStringProcessor(new NativeStringProcessor());

		$this->assertInstanceOf('Hubzero\Spam\StringProcessor\NativeStringProcessor', $service->getStringProcessor());
	}

	/**
	 * Test to make sure a detector is registered properly
	 * and returns $this.
	 *
	 * @covers  \Hubzero\Spam\Checker::registerDetector
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
	 * Test to get a registered detector
	 *
	 * @covers  \Hubzero\Spam\Checker::getDetector
	 * @return  void
	 **/
	public function testGetDetector()
	{
		$service = new Checker();
		$service->registerDetector(new Detector());

		$this->assertInstanceOf('Hubzero\Spam\Tests\Mock\Detector', $service->getDetector('Hubzero\Spam\Tests\Mock\Detector'));
		$this->assertFalse($service->getDetector('Hubzero\Spam\Tests\Mock\Example'));
	}

	/**
	 * Test that getDetectors returns an array of detectors
	 *
	 * @covers  \Hubzero\Spam\Checker::getDetectors
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

	/**
	 * Test that getReport() returns an array
	 *
	 * @covers  \Hubzero\Spam\Checker::getReport
	 * @return  void
	 **/
	public function testGetReport()
	{
		$service = new Checker();
		$service->registerDetector(new Detector());

		$report = $service->getReport();

		$this->assertTrue(is_array($report));
	}

	/**
	 * Test the check() method
	 *
	 * @covers  \Hubzero\Spam\Checker::check
	 * @return  void
	 **/
	public function testCheck()
	{
		$service = new Checker();
		$service->registerDetector(new Detector());

		$result = $service->check('Maecenas sed diam eget risus varius blandit sit amet non magna.');

		$this->assertInstanceOf('Hubzero\Spam\Result', $result);
		$this->assertFalse($result->isSpam());

		$result = $service->check('Maecenas sed diam eget risus varius spam blandit sit amet non magna.');

		$this->assertInstanceOf('Hubzero\Spam\Result', $result);
		$this->assertTrue($result->isSpam());

		$messages = $result->getMessages();
		$this->assertTrue(is_array($messages));
		$this->assertTrue(in_array('Text contained the word "spam".', $messages));
	}
}
