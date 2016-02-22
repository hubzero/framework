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

namespace Hubzero\Content\Tests;

use Hubzero\Test\Database;
use Hubzero\Content\Auditor;
use Hubzero\Content\Tests\Mock\Checker;

/**
 * Auditor tests
 */
class AuditorTest extends Database
{
	/**
	 * Hubzero\Content\Auditor
	 *
	 * @var  object
	 */
	private $instance;

	/**
	 * Sample data
	 *
	 * @var  array
	 */
	private $data;

	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 **/
	public function setUp()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());

		$this->instance = new Auditor('tests');

		$this->data = array();

		for ($i = 1; $i <= 50; $i++)
		{
			$this->data[] = array(
				'id'    => $i,
				'title' => substr(md5(rand()), 0, 7)
			);
		}
	}

	/**
	 * Tests the registerTest() method.
	 *
	 * @covers  \Hubzero\Content\Auditor::registerTest
	 * @return  void
	 **/
	public function testRegisterTest()
	{
		$this->instance->registerTest(new Checker);

		$this->assertEquals(1, count($this->instance->getTests()));
		$this->assertInstanceOf('Hubzero\Content\Tests\Mock\Checker', $this->instance->getTest('Hubzero_Content_Tests_Mock_Checker'));
	}

	/**
	 * Test that an exception is thrown when registering
	 * a test that has already been registered
	 *
	 * @expectedException \RuntimeException
	 * @covers  \Hubzero\Content\Auditor::registerTest
	 * @return  void
	 */
	public function testRegisterTestThrowsRuntimeException()
	{
		$this->instance->registerTest(new Checker);
		$this->instance->registerTest(new Checker);
	}

	/**
	 * Tests the getTests() method.
	 *
	 * @covers  \Hubzero\Content\Auditor::getTests
	 * @return  void
	 **/
	public function testGetTests()
	{
		$results = $this->instance->getTests();

		$this->assertTrue(is_array($results), 'List of tests should be an array');
		$this->assertEquals(0, count($results));

		$this->instance->registerTest(new Checker);

		$results = $this->instance->getTests();

		$this->assertTrue(is_array($results), 'List of tests should be an array');
		$this->assertEquals(1, count($results));
	}

	/**
	 * Tests the getTest() method.
	 *
	 * @covers  \Hubzero\Content\Auditor::getTest
	 * @return  void
	 **/
	public function testGetTest()
	{
		$this->assertEquals(false, $this->instance->getTest('Hubzero_Content_Tests_Mock_Checker'));

		$this->instance->registerTest(new Checker);

		$this->assertInstanceOf('Hubzero\Content\Tests\Mock\Checker', $this->instance->getTest('Hubzero_Content_Tests_Mock_Checker'));
	}

	/**
	 * Tests the unregisterTest() method.
	 *
	 * @covers  \Hubzero\Content\Auditor::unregisterTest
	 * @return  void
	 **/
	public function testUnregisterTest()
	{
		$this->instance->registerTest(new Checker);

		$this->instance->unregisterTest('Hubzero_Content_Tests_Mock_Checker');

		$this->assertEquals(0, count($this->instance->getTests()));
		$this->assertEquals(false, $this->instance->getTest('Hubzero_Content_Tests_Mock_Checker'));
	}

	/**
	 * Tests the process() method.
	 *
	 * @covers  \Hubzero\Content\Auditor::process
	 * @return  void
	 **/
	public function testProcess()
	{
		$this->instance->registerTest(new Checker);

		$results = $this->instance->process($this->data[1]);

		$this->assertTrue(is_array($results), 'Results should be an array');

		foreach ($results as $result)
		{
			$this->assertInstanceOf('Hubzero\Content\Auditor\Result', $result);
		}
	}

	/**
	 * Tests the check() method.
	 *
	 * @covers  \Hubzero\Content\Auditor::check
	 * @return  void
	 **/
	public function testCheck()
	{
		$this->instance->registerTest(new Checker);

		$results = $this->instance->check($this->data);

		$this->assertTrue(is_array($results), 'Results should be an array');

		$this->assertEquals(count($this->data), count($results));

		foreach ($results as $result)
		{
			$this->assertEquals(true, is_array($result));
			$this->assertEquals(true, is_array($result['data']));
			$this->assertEquals(true, is_array($result['tests']));
			$this->assertEquals(1, count($result['tests']));
		}
	}
}
