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
use Hubzero\Config\Registry;
use stdClass;

/**
 * Registry tests
 */
class RegistryTest extends Basic
{
	/**
	 * Tests set() and get()
	 *
	 * @covers  \Hubzero\Config\Registry::set
	 * @covers  \Hubzero\Config\Registry::get
	 * @covers  \Hubzero\Config\Registry::offsetSet
	 * @covers  \Hubzero\Config\Registry::offsetGet
	 * @return  void
	 **/
	public function testSetAndGet()
	{
		$data = new Registry();

		$this->assertEquals($data->get('foo'), null);
		$this->assertEquals($data->get('foo', 'one'), 'one');

		$data->set('foo', 'bar');

		$this->assertEquals($data->get('foo'), 'bar');

		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$this->assertEquals($data->get('lorem.ipsum'), 'sham');

		$data['foo'] = 'lorem';

		$this->assertEquals($data->get('foo'), 'lorem');
		$this->assertEquals($data['foo'], 'lorem');

		$data['lorem.ipsum'] = 'ipsum';

		$this->assertEquals($data->get('lorem.ipsum'), 'ipsum');
		$this->assertEquals($data['lorem.ipsum'], 'ipsum');
	}

	/**
	 * Tests the has() method
	 *
	 * @covers  \Hubzero\Config\Registry::had
	 * @covers  \Hubzero\Config\Registry::offsetExists
	 * @return  void
	 **/
	public function testHas()
	{
		$data = new Registry();

		$data->set('foo', 'bar');

		$this->assertTrue($data->has('foo'));
		$this->assertFalse($data->has('bar'));

		$this->assertTrue(isset($data['foo']));
		$this->assertFalse(isset($data['bar']));
	}

	/**
	 * Tests the def() method
	 *
	 * @covers  \Hubzero\Config\Registry::def
	 * @return  void
	 **/
	public function testDef()
	{
		$data = new Registry();

		$data->def('foo', 'bar');

		$this->assertEquals($data->get('foo'), 'bar');

		$data->set('bar', 'foo');
		$data->def('bar', 'oop');

		$this->assertEquals($data->get('bar'), 'foo');
	}

	/**
	 * Tests the reset() method
	 *
	 * @covers  \Hubzero\Config\Registry::reset
	 * @return  void
	 **/
	public function testReset()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');

		$data->reset();

		$this->assertFalse($data->has('foo'));
		$this->assertFalse($data->has('bar'));
	}

	/**
	 * Tests the count() method
	 *
	 * @covers  \Hubzero\Config\Registry::count
	 * @return  void
	 **/
	public function testCount()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');

		$this->assertEquals($data->count(), 2);

		$data->set('lorem', 'ipsum');

		$this->assertEquals($data->count(), 3);

		$data->reset();

		$this->assertEquals($data->count(), 0);
	}

	/**
	 * Tests the toObject() method
	 *
	 * @covers  \Hubzero\Config\Registry::toObject
	 * @return  void
	 **/
	public function testToObject()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$obj = $data->toObject();

		$this->assertInstanceOf('stdClass', $obj);
		$this->assertTrue(isset($obj->bar));
		$this->assertEquals($obj->foo, 'bar');
		$this->assertTrue(isset($obj->lorem->ipsum));
	}

	/**
	 * Tests the toArray() method
	 *
	 * @covers  \Hubzero\Config\Registry::toArray
	 * @covers  \Hubzero\Config\Registry::asArray
	 * @return  void
	 **/
	public function testToArray()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$arr = $data->toArray();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(isset($arr['bar']));
		$this->assertTrue(isset($arr['lorem']['ipsum']));
		$this->assertEquals($arr['lorem']['ipsum'], 'sham');
	}

	/**
	 * Tests the flatten() method
	 *
	 * @covers  \Hubzero\Config\Registry::flatten
	 * @covers  \Hubzero\Config\Registry::toFlatten
	 * @return  void
	 **/
	public function testFlatten()
	{
		$data = new Registry();

		$data->set('foo', 'bar');
		$data->set('bar', 'foo');
		$data->set('lorem', new stdClass);
		$data->set('lorem.ipsum', 'sham');

		$arr = $data->flatten();

		$this->assertTrue(is_array($arr));
		$this->assertTrue(isset($arr['bar']));
		$this->assertTrue(isset($arr['lorem.ipsum']));
		$this->assertEquals($arr['lorem.ipsum'], 'sham');
	}
}
