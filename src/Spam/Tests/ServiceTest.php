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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;

/**
 * Spam (abstract) Service tests
 */
class ServiceTest extends Basic
{
	/**
	 * Get the mock object
	 *
	 * @return  object
	 **/
	protected function getStub()
	{
		return $this->getMockForAbstractClass('Hubzero\Spam\Detector\Service');
	}

	/**
	 * Tests for setting and getting a value
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::setValue
	 * @covers  \Hubzero\Spam\Detector\Service::getValue
	 * @return  void
	 **/
	public function testValue()
	{
		$stub = $this->getStub();

		$stub->setValue('foo');

		$this->assertEquals($stub->getValue(), 'foo');
	}

	/**
	 * Tests detect() returns false
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::detect
	 * @return  void
	 **/
	public function testDetect()
	{
		$stub = $this->getStub();

		$this->assertFalse($stub->detect('foo'));
	}

	/**
	 * Tests learn()
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::learn
	 * @return  void
	 **/
	public function testLearn()
	{
		$stub = $this->getStub();

		$isSpam = true;

		$this->assertFalse($stub->learn('', $isSpam));
		$this->assertTrue($stub->learn('foo', $isSpam));
	}

	/**
	 * Tests forget()
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::forget
	 * @return  void
	 **/
	public function testForget()
	{
		$stub = $this->getStub();

		$isSpam = true;

		$this->assertTrue($stub->learn('foo', $isSpam));
	}

	/**
	 * Tests message() returns an empty string
	 *
	 * @covers  \Hubzero\Spam\Detector\Service::message
	 * @return  void
	 **/
	public function testMessage()
	{
		$stub = $this->getStub();

		$this->assertEquals($stub->message(), '');
	}
}
