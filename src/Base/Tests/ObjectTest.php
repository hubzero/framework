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
use Hubzero\Base\Object;

/**
 * Object test
 */
class ObjectTest extends Basic
{
	/**
	 * Sample data
	 *
	 * @var  array
	 */
	protected $data = array(
		'one'   => 'for the money',
		'two'   => 'for the show',
		'three' => 'to get ready',
		'four'  => 'to go'
	);

	/**
	 * Test __construct
	 *
	 * @covers  \Hubzero\Base\Object::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$obj = new Object($this->data);

		foreach ($this->data as $key => $datum)
		{
			$this->assertTrue(isset($obj->$key));
			$this->assertEquals($obj->$key, $datum);
		}

		$obj2 = new Object($obj);

		foreach ($this->data as $key => $datum)
		{
			$this->assertTrue(isset($obj2->$key));
			$this->assertEquals($obj2->$key, $datum);
		}
	}

	/**
	 * Test __toString
	 *
	 * @covers  \Hubzero\Base\Object::__toString
	 * @return  void
	 **/
	public function testToString()
	{
		$obj = new Object($this->data);

		$result = (string)$obj;

		$this->assertEquals($result, 'Hubzero\Base\Object');
	}

	/**
	 * Test setProperties
	 *
	 * @covers  \Hubzero\Base\Object::setProperties
	 * @return  void
	 **/
	public function testSetProperties()
	{
		$obj = new Object();

		$this->assertFalse($obj->setProperties('foo'));
		$this->assertTrue($obj->setProperties($this->data));

		foreach ($this->data as $key => $datum)
		{
			$this->assertTrue(isset($obj->$key));
			$this->assertEquals($obj->$key, $datum);
		}

		$obj = new Object();

		$data = new \stdClass;
		$data->one   = 'for the money';
		$data->two   = 'for the show';
		$data->three = 'to get ready';
		$data->four  = 'to go';

		$this->assertTrue($obj->setProperties($data));

		foreach (get_object_vars($data) as $key => $datum)
		{
			$this->assertTrue(isset($obj->$key));
			$this->assertEquals($obj->$key, $datum);
		}
	}

	/**
	 * Test getProperties
	 *
	 * @covers  \Hubzero\Base\Object::getProperties
	 * @return  void
	 **/
	public function testGetProperties()
	{
		$data = $this->data;
		$data['_private'] = 'Private property';

		$obj = new Object($data);

		$prop = $obj->getProperties();

		$this->assertTrue(is_array($prop));
		$this->assertCount(4, $prop);

		foreach ($prop as $key => $val)
		{
			$this->assertEquals($this->data[$key], $val);
		}
	}

	/**
	 * Test setting a property
	 *
	 * @covers  \Hubzero\Base\Object::set
	 * @return  void
	 **/
	public function testSet()
	{
		$obj = new Object();

		$this->assertInstanceOf('Hubzero\Base\Object', $obj->set('foo', 'bar'));
		$this->assertTrue(isset($obj->foo));
		$this->assertEquals($obj->foo, 'bar');
	}

	/**
	 * Test retrieving a set property and
	 * retriving a default value if a property isn't set
	 *
	 * @covers  \Hubzero\Base\Object::get
	 * @return  void
	 **/
	public function testGet()
	{
		$obj = new Object();
		$obj->set('foo', 'bar');

		$this->assertEquals($obj->get('foo'), 'bar');
		$this->assertEquals($obj->get('bar', 'default'), 'default');
	}

	/**
	 * Test setting a default value if not alreay assigned
	 *
	 * @covers  \Hubzero\Base\Object::def
	 * @return  void
	 **/
	public function testDef()
	{
		$obj = new Object();

		$obj->def('bar', 'ipsum');

		$this->assertEquals($obj->get('bar'), 'ipsum');

		$obj->set('foo', 'bar');
		$obj->def('foo', 'lorem');

		$this->assertEquals($obj->get('foo'), 'bar');
	}
}
