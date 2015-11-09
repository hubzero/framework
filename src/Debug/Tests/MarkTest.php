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

namespace Hubzero\Debug\Tests;

use Hubzero\Test\Basic;
use Hubzero\Debug\Profile\Mark;

/**
 * Profiler mark tests
 */
class MarkTest extends Basic
{
	/**
	 * Tests that data passed in constructor is set to correct properties
	 *
	 * @covers  \Hubzero\Debug\Profile\Mark::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$mark = new Mark('test1');

		$this->assertEquals($mark->label(), 'test1');
		$this->assertEquals($mark->started(), 0.0);
		$this->assertEquals($mark->ended(), 0.0);
		$this->assertEquals($mark->memory(), 0);

		$mark = new Mark('test2', 1.5, 3.5, 1048576);

		$this->assertEquals($mark->label(), 'test2');
		$this->assertEquals($mark->started(), 1.5);
		$this->assertEquals($mark->ended(), 3.5);
		$this->assertEquals($mark->memory(), 1048576);
	}

	/**
	 * Tests the label() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::label
	 */
	public function testLabel()
	{
		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->label(), 'test');
	}

	/**
	 * Tests the started() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::started
	 */
	public function testStarted()
	{
		$mark = new Mark('test', 0, 0, 0);
		$this->assertEquals($mark->started(), 0);

		$mark = new Mark('test', 1.5, 3.5, 0);
		$this->assertEquals($mark->started(), 1.5);
	}

	/**
	 * Tests the ended() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::ended
	 */
	public function testEnded()
	{
		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->ended(), 1.5);

		$mark = new Mark('test', 1.5, 3.5, 0);
		$this->assertEquals($mark->ended(), 3.5);
	}

	/**
	 * Tests the duration() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::duration
	 */
	public function testDuration()
	{
		$mark = new Mark('test', 0, 0, 0);
		$this->assertEquals($mark->duration(), 0);

		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->duration(), 1.5);
	}

	/**
	 * Tests the memory() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::memory
	 */
	public function testMemory()
	{
		$mark = new Mark('test', 0, 1.5, 0);
		$this->assertEquals($mark->memory(), 0);

		$mark = new Mark('test', 0, 1.5, 1048576);
		$this->assertEquals($mark->memory(), 1048576);
	}

	/**
	 * Tests the toString() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::toString
	 */
	public function testToString()
	{
		$mark = new Mark('test', 0, 1.5, 1048576);

		$result = sprintf('%s: %.2F MiB - %d ms', 'test', 1048576 / 1024 / 1024, 1.5);

		$this->assertEquals($mark->toString(), $result);
	}

	/**
	 * Tests the toArray() method.
	 *
	 * @return  void
	 * @covers  \Hubzero\Debug\Profile\Mark::toArray
	 */
	public function testToArray()
	{
		$mark = new Mark('test', 0, 1.5, 1048576);

		$result = array(
			'label'  => 'test',
			'start'  => 0.0,
			'end'    => 1.5,
			'memory' => 1048576
		);

		$this->assertEquals($mark->toArray(), $result);
	}
}
