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
	 * @covers  \Hubzero\Config\Registry::instance
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
