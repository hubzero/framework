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
use Hubzero\Spam\Result;

/**
 * Spam result test
 */
class ResultTest extends Basic
{
	/**
	 * Tests isSpam() returns correct value
	 *
	 * @covers  \Hubzero\Spam\Result::isSpam
	 * @return  void
	 */
	public function testIsSpam()
	{
		$result = new Result(true);

		$this->assertTrue($result->isSpam());

		$result = new Result(false);

		$this->assertFalse($result->isSpam());
	}

	/**
	 * Tests passed() returns correct value depending on if spam or not
	 *
	 * @covers  \Hubzero\Spam\Result::passed
	 * @return  void
	 */
	public function testPassed()
	{
		$result = new Result(false);

		$this->assertTrue($result->passed());

		$result = new Result(true);

		$this->assertFalse($result->passed());
	}

	/**
	 * Tests failed() returns correct value depending on if spam or not
	 *
	 * @covers  \Hubzero\Spam\Result::failed
	 * @return  void
	 */
	public function testFailed()
	{
		$result = new Result(true);

		$this->assertTrue($result->failed());

		$result = new Result(false);

		$this->assertFalse($result->failed());
	}

	/**
	 * Tests getMessages() returns the list of messages passed in the constructor
	 *
	 * @covers  \Hubzero\Spam\Result::getMessages
	 * @return  void
	 */
	public function testGetMessages()
	{
		$messages = [
			'Message one',
			'Message two'
		];

		$result = new Result(true, $messages);

		$this->assertEquals($messages, $result->getMessages());
	}
}
