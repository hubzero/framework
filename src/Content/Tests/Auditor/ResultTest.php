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

namespace Hubzero\Content\Tests\Auditor;

use Hubzero\Test\Basic;
use Hubzero\Content\Auditor\Result;

/**
 * Auditor Result tests
 */
class ResultTest extends Basic
{
	/**
	 * Hubzero\Content\Auditor\Result
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

		$this->instance = new Result();
	}

	/**
	 * Tests automaticProcessed() method.
	 *
	 * @covers  \Hubzero\Content\Auditor\Result::automaticProcessed
	 * @return  void
	 **/
	public function testAutomaticProcessed()
	{
		$dt = new \Hubzero\Utility\Date();

		$result = $this->instance->automaticProcessed(array());

		$this->assertEquals($dt->toSql(), $result);
	}

	/**
	 * Tests passed() method.
	 *
	 * @covers  \Hubzero\Content\Auditor\Result::passed
	 * @return  void
	 **/
	public function testPassed()
	{
		$this->instance->set('status', 1);

		$this->assertEquals(true, $this->instance->passed());

		$this->instance->set('status', 0);

		$this->assertEquals(false, $this->instance->passed());

		$this->instance->set('status', -1);

		$this->assertEquals(false, $this->instance->passed());
	}

	/**
	 * Tests skipped() method.
	 *
	 * @covers  \Hubzero\Content\Auditor\Result::skipped
	 * @return  void
	 **/
	public function testSkipped()
	{
		$this->instance->set('status', 1);

		$this->assertEquals(false, $this->instance->skipped());

		$this->instance->set('status', 0);

		$this->assertEquals(true, $this->instance->skipped());

		$this->instance->set('status', -1);

		$this->assertEquals(false, $this->instance->skipped());
	}

	/**
	 * Tests skipped() method.
	 *
	 * @covers  \Hubzero\Content\Auditor\Result::skipped
	 * @return  void
	 **/
	public function testFailed()
	{
		$this->instance->set('status', 1);

		$this->assertEquals(false, $this->instance->failed());

		$this->instance->set('status', 0);

		$this->assertEquals(false, $this->instance->failed());

		$this->instance->set('status', -1);

		$this->assertEquals(true, $this->instance->failed());
	}

	/**
	 * Tests transformStatus() method.
	 *
	 * @covers  \Hubzero\Content\Auditor\Result::transformStatus
	 * @return  void
	 **/
	public function testTransformStatus()
	{
		$this->instance->set('status', 1);

		$this->assertEquals('passed', $this->instance->status);
		$this->assertEquals('passed', $this->instance->transformStatus());

		$this->instance->set('status', 0);

		$this->assertEquals('skipped', $this->instance->status);
		$this->assertEquals('skipped', $this->instance->transformStatus());

		$this->instance->set('status', -1);

		$this->assertEquals('failed', $this->instance->status);
		$this->assertEquals('failed', $this->instance->transformStatus());

		$this->instance->set('status', 7);

		$this->assertEquals(7, $this->instance->status);
		$this->assertEquals(7, $this->instance->transformStatus());
	}
}
