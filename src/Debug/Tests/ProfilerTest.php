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
use Hubzero\Debug\Profiler;

/**
 * Profiler tests
 */
class ProfilerTest extends Basic
{
	/**
	 * Hubzero\Debug\Profiler
	 *
	 * @var  object
	 */
	private $instance;

	/**
	 * Setup the tests.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->instance = new Profiler('test');
	}

	/**
	 * Tests the marks() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::marks
	 * @return  void
	 **/
	public function testMarks()
	{
		$this->instance->mark('one');
		$this->instance->mark('two');
		$this->instance->mark('three');

		// Assert the first point has a time and memory = 0
		$marks = $this->instance->marks();

		$this->assertTrue(is_array($marks), 'marks() should return an array');
		$this->assertEquals(count($marks), 3);
	}

	/**
	 * Tests the mark() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::mark
	 * @return  void
	 **/
	public function testMark()
	{
		$started = $this->instance->started();

		$this->instance->mark('one');
		$this->instance->mark('two');
		$this->instance->mark('three');

		// Assert the first point has a time and memory = 0
		$marks = $this->instance->marks();

		$first = $marks[0];

		$this->assertEquals($first->label(), 'one');
		$this->assertEquals($first->started(), $started);

		// Assert the other points have a time and memory
		$second = $marks[1];

		$this->assertEquals($second->label(), 'two');
		$this->assertGreaterThan(0, $second->duration());
		$this->assertGreaterThan(0, $second->memory());

		$third = $marks[2];

		$this->assertEquals($third->label(), 'three');
		$this->assertGreaterThan(0, $third->duration());
		$this->assertGreaterThan(0, $third->memory());

		// Assert the third point has greater values than the other points
		$this->assertGreaterThan($second->ended(), $third->ended());
		$this->assertGreaterThanOrEqual($second->memory(), $third->memory());
	}

	/**
	 * Tests the duration() method.
	 *
	 * @covers  \Hubzero\Debug\Profiler::duration
	 * @return  void
	 **/
	public function testDuration()
	{
		$this->instance->mark('one');
		$this->instance->mark('two');
		$this->instance->mark('three');

		$this->assertGreaterThan(0, $this->instance->duration());
	}
}
