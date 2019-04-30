<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\Processor;

/**
 * Processor tests
 */
class ProcessorTest extends Basic
{
	/**
	 * Tests all()
	 *
	 * @covers  \Hubzero\Config\Processor::all
	 * @return  void
	 **/
	public function testAll()
	{
		$instances = Processor::all();

		$this->assertCount(5, $instances);

		foreach ($instances as $instance)
		{
			$this->assertInstanceOf(Processor::class, $instance);
		}
	}

	/**
	 * Tests the instance() method
	 *
	 * @covers  \Hubzero\Config\Processor::instance
	 * @return  void
	 **/
	public function testInstance()
	{
		foreach (array('ini', 'yaml', 'json', 'php', 'xml') as $type)
		{
			$result = Processor::instance($type);

			$this->assertInstanceOf(Processor::class, $result);
		}

		$this->setExpectedException('Hubzero\\Error\\Exception\\InvalidArgumentException');

		$result = Processor::instance('py');
	}
}
