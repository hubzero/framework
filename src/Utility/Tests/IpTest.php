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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Tests;

use Hubzero\Test\Basic;

/**
 * Ip utility test
 */
class IpTest extends Basic
{
	/**
	 * Tests is valid ip check
	 *
	 * @return  void
	 **/
	public function testIsValid()
	{
		$ip = new \Hubzero\Utility\Ip('192.168.0.1');

		$this->assertTrue($ip->isValid(), 'Basic IPv4 address did not validate');

		$ip = new \Hubzero\Utility\Ip('256.256.256.256');

		$this->assertFalse($ip->isValid(), 'Invalid IPv4 validated as true');
	}

	/**
	 * Tests is private ip check
	 *
	 * @return  void
	 **/
	public function testIsPrivate()
	{
		$ip = new \Hubzero\Utility\Ip('192.168.255.255');
		$this->assertTrue($ip->isPrivate(), 'Basic IPv4 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('172.16.0.1');
		$this->assertTrue($ip->isPrivate(), 'Basic IPv4 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('10.5.20.135');
		$this->assertTrue($ip->isPrivate(), 'Basic IPv4 address did not identify as private');

		$ip = new \Hubzero\Utility\Ip('192.167.0.1');
		$this->assertFalse($ip->isPrivate(), 'Basic IPv4 address did not identify as public');

		$ip = new \Hubzero\Utility\Ip('172.15.255.255');
		$this->assertFalse($ip->isPrivate(), 'Basic IPv4 address did not identify as public');

		$ip = new \Hubzero\Utility\Ip('11.5.20.135');
		$this->assertFalse($ip->isPrivate(), 'Basic IPv4 address did not identify as public');
	}

	/**
	 * Tests to make sure bad arguments are caught
	 *
	 * @expectedException RuntimeException
	 * @return  void
	 **/
	public function testInvalidArgumentThrowsException()
	{
		$ip = new \Hubzero\Utility\Ip('172.16.0.1');
		$ip->isBetween('rock', 'hard place');
	}
}
