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

namespace Hubzero\Pagination\Tests;

use Hubzero\Test\Basic;
use Hubzero\Pagination\Paginator;
use Hubzero\Pagination\View;

/**
 * Pagination Paginator test
 */
class PaginatorTest extends Basic
{
	/**
	 * Paginator instance
	 *
	 * @var  object
	 */
	protected $instance = null;

	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 */
	public function setUp()
	{
		$this->instance = new Paginator(200, 0, 25);
	}

	/**
	 * Tests that data passed in constructor is set to correct properties
	 *
	 * @covers  \Hubzero\Pagination\Paginator::__construct
	 * @return  void
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf('Hubzero\Pagination\Paginator', $this->instance);

		$this->assertEquals($this->instance->total, 200, 'Total is wrong');
		$this->assertEquals($this->instance->limitstart, 0, 'Limit start is wrong');
		$this->assertEquals($this->instance->limit, 25, 'Limit is wrong');
	}

	/**
	 * Tests the getLimits() method
	 *
	 * @covers  \Hubzero\Pagination\Paginator::getLimits
	 * @return  void
	 */
	public function testGetLimits()
	{
		$limits = array();
		for ($i = 5; $i <= 30; $i += 5)
		{
			$limits[] = $i;
		}
		$limits[] = 50;
		$limits[] = 100;
		$limits[] = 500;
		$limits[] = 1000;

		$items = $this->instance->getLimits();

		$this->assertTrue(is_array($items), 'getLimits() should return an array');
		$this->assertCount(count($limits), $items, 'getLimits() should have returned more items');
		$this->assertEquals($items, $limits);
	}

	/**
	 * Tests the setLimits() method
	 *
	 * @covers  \Hubzero\Pagination\Paginator::setLimits
	 * @return  void
	 */
	public function testSetLimits()
	{
		$limits = array(
			10,
			20,
			40,
			80,
			160
		);

		$this->assertInstanceOf('Hubzero\Pagination\Paginator', $this->instance->setLimits($limits));

		$items = $this->instance->getLimits();

		$this->assertTrue(is_array($items), 'getLimits() should return an array');
		$this->assertCount(count($limits), $items, 'getLimits() should have returned more items');
		$this->assertEquals($items, $limits);
	}

	/**
	 * Tests the setAdditionalUrlParam() method
	 *
	 * @covers  \Hubzero\Pagination\Paginator::setAdditionalUrlParam
	 * @covers  \Hubzero\Pagination\Paginator::getAdditionalUrlParam
	 * @return  void
	 */
	public function testAdditionalUrlParam()
	{
		$this->assertInstanceOf('Hubzero\Pagination\Paginator', $this->instance->setAdditionalUrlParam('foo', 'bar'));

		$this->assertEquals($this->instance->getAdditionalUrlParam('foo'), 'bar');

		$this->instance->setAdditionalUrlParam('foo', null);

		$this->assertEquals($this->instance->getAdditionalUrlParam('foo'), null);
	}

	/**
	 * Tests the getRowOffset() method
	 *
	 * @covers  \Hubzero\Pagination\Paginator::getRowOffset
	 * @return  void
	 */
	public function testGetRowOffset()
	{
		$this->assertEquals($this->instance->getRowOffset(0), 1);
		$this->assertEquals($this->instance->getRowOffset(1), 2);
		$this->assertEquals($this->instance->getRowOffset(5), 6);

		$instance = new Paginator(200, 50, 25);

		$this->assertEquals($instance->getRowOffset(0), 51);
		$this->assertEquals($instance->getRowOffset(1), 52);
		$this->assertEquals($instance->getRowOffset(5), 56);
	}

	/**
	 * Tests the getData() method
	 *
	 * @covers  \Hubzero\Pagination\Paginator::getData
	 * @return  void
	 */
	public function testGetData()
	{
		$data = $this->instance->getData();

		$this->assertInstanceOf('stdClass', $data);
		$this->assertTrue(is_array($data->pages));
		$this->assertInstanceOf('Hubzero\Pagination\Item', $data->start);
		$this->assertInstanceOf('Hubzero\Pagination\Item', $data->previous);
		$this->assertInstanceOf('Hubzero\Pagination\Item', $data->next);
		$this->assertInstanceOf('Hubzero\Pagination\Item', $data->end);
	}

	/**
	 * Tests the render() method
	 *
	 * @covers  \Hubzero\Pagination\Paginator::render
	 * @return  void
	 */
	/*public function testRender()
	{
		$view = new View(array(
			'base_path' => __DIR__
		));

		$data = $this->instance->render($view);
		$data = trim($data);

		$this->assertEquals($data, '<p>Pagination test view</p>');
	}*/
}
