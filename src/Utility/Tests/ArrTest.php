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
use Hubzero\Utility\Arr;
use stdClass;

/**
 * Arr utility test
 */
class ArrTest extends Basic
{
	/**
	 * Tests converting values to integers
	 *
	 * @covers  \Hubzero\Utility\Arr::toInteger
	 * @return  void
	 **/
	public function testToInteger()
	{
		$data = array(
			'1',
			'322',
			55,
			false,
			'foo'
		);

		Arr::toInteger($data);

		$this->assertTrue(is_array($data), 'Value returned was not an array');

		foreach ($data as $val)
		{
			$this->assertTrue(is_int($val), 'Value returned was not an integer');
		}

		$data = new stdClass;
		$data->one = '1';
		$data->two = false;
		$data->three = 55;

		Arr::toInteger($data);

		$this->assertTrue(is_array($data), 'Value returned was not an array');
		$this->assertTrue(empty($data), 'Value returned was not an empty array');

		$dflt = array(
			'1',
			'322',
			55,
			false,
			'foo'
		);

		$data = new stdClass;
		$data->one = '1';
		$data->two = false;
		$data->three = 55;

		Arr::toInteger($data, $dflt);

		$this->assertTrue(is_array($data), 'Value returned was not an array');
		$this->assertFalse(empty($data), 'Value returned was an empty array');

		foreach ($data as $key => $val)
		{
			$this->assertTrue(is_int($val), 'Value returned was not an integer');
			$this->assertEquals($val, (int)$dflt[$key]);
		}
	}

	/**
	 * Tests converting values to objects
	 *
	 * @covers  \Hubzero\Utility\Arr::toObject
	 * @return  void
	 **/
	public function testToObject()
	{
		$data1 = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			)
		);

		$data2 = new stdClass;
		$data2->foo = 'one';
		$data2->bar = 'two';
		$data2->lor = array(
			'ipsum',
			'lorem'
		);

		$datas = array();
		$datas[] = $data1;
		$datas[] = $data2;

		foreach ($datas as $data)
		{
			$result = Arr::toObject($data);

			$this->assertTrue(is_object($result), 'Value returned was not an object');

			foreach ((array)$data as $key => $val)
			{
				$this->assertTrue(isset($result->$key), 'Property "' . $key . '" not set on returned object');
				if (!is_array($val))
				{
					$this->assertEquals($result->$key, $val);
				}
				else
				{
					$this->assertTrue(is_object($result->$key), 'Value returned was not an object');

					foreach ($val as $k => $v)
					{
						$this->assertTrue(isset($result->$key->$k), 'Property not set on returned object');
						$this->assertEquals($result->$key->$k, $v);
					}
				}
			}
		}
	}

	/**
	 * Tests converting values from objects
	 *
	 * @covers  \Hubzero\Utility\Arr::fromObject
	 * @return  void
	 **/
	public function testFromObject()
	{
		$data1 = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			)
		);

		$data2 = new stdClass;
		$data2->foo = 'one';
		$data2->bar = 'two';
		$data2->lor = array(
			'ipsum',
			'lorem'
		);
		$data2->ipsum = new stdClass;
		$data2->ipsum->dolor = 'mit';
		$data2->ipsum->carac = 'kol';

		$datas = array();
		$datas[] = $data1;
		$datas[] = $data2;

		foreach ($datas as $data)
		{
			$result = Arr::fromObject($data);

			$this->assertTrue(is_array($result), 'Value returned was not an array');

			foreach ((array)$data as $key => $val)
			{
				$this->assertTrue(isset($result[$key]), 'Array value not set from object property');
				if (!is_array($val) && !is_object($val))
				{
					$this->assertEquals($result[$key], $val);
				}
				else
				{
					$this->assertTrue(isset($result[$key]), 'Array value not set from object property');

					foreach ((array)$val as $k => $v)
					{
						$this->assertTrue(isset($result[$key][$k]), 'Property not set on returned object');
						$this->assertEquals($result[$key][$k], $v);
					}
				}
			}
		}

		$result = Arr::fromObject($data2, false);

		$this->assertTrue(is_array($result), 'Value returned was not an array');
		foreach ((array)$data2 as $key => $val)
		{
			$this->assertTrue(isset($result[$key]), 'Array value not set from object property');
			$this->assertEquals($result[$key], $val);
		}
	}

	/**
	 * Tests determining if array is associative array
	 *
	 * @covers  \Hubzero\Utility\Arr::isAssociative
	 * @return  void
	 **/
	public function testIsAssociative()
	{
		$data = array(
			'one' => '1',
			'two' => '322',
			'three' => 55
		);

		$this->assertTrue(Arr::isAssociative($data), 'Value is an associative array');

		$data = array(
			133,
			675,
			744
		);

		$this->assertFalse(Arr::isAssociative($data), 'Value is not an associative array');

		$data = new stdClass;
		$data->one = 'foo';
		$data->two = 'bar';

		$this->assertFalse(Arr::isAssociative($data), 'Value is not an associative array');
	}

	/**
	 * Tests mapping an array to a string
	 *
	 * @covers  \Hubzero\Utility\Arr::toString
	 * @return  void
	 **/
	public function testToString()
	{
		$data = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			)
		);

		$result = Arr::toString($data, '=', '&');

		$this->assertTrue(is_string($result), 'Value is not a string');
		$this->assertEquals($result, 'one="1"&two="322"&three="55"&a="foo"&b="bar"');

		$result = Arr::toString($data, '=', '&', true);
		$this->assertEquals($result, 'one="1"&two="322"&three="55"&four&a="foo"&b="bar"');
	}

	/**
	 * Tests returning a value from a named array
	 *
	 * @covers  \Hubzero\Utility\Arr::getValue
	 * @return  void
	 **/
	public function testGetValue()
	{
		$data = array(
			'one' => '1',
			'two' => '322',
			'three' => 55,
			'four' => array(
				'a' => 'foo',
				'b' => 'bar'
			),
			'six' => '!! good123',
			'seven' => '5.5'
		);

		$result = Arr::getValue($data, 'one');

		$this->assertEquals($result, '1');

		$result = Arr::getValue($data, 'one', null, 'integer');

		$this->assertTrue(is_int($result), 'Value is not an integer');
		$this->assertEquals($result, 1);

		$result = Arr::getValue($data, 'three', null, 'string');

		$this->assertTrue(is_string($result), 'Value is not a string');
		$this->assertEquals($result, '55');

		$result = Arr::getValue($data, 'two', null, 'array');

		$this->assertTrue(is_array($result), 'Value is not an array');
		$this->assertEquals($result, array('322'));

		$result = Arr::getValue($data, 'four', null, 'array');

		$this->assertTrue(is_array($result), 'Value is not an array');
		$this->assertEquals($result, array(
			'a' => 'foo',
			'b' => 'bar'
		));

		$result = Arr::getValue($data, 'five');

		$this->assertTrue(is_null($result), 'Value is not null');

		$result = Arr::getValue($data, 'five', 'glorp');

		$this->assertEquals($result, 'glorp');

		$result = Arr::getValue($data, 'one', null, 'bool');

		$this->assertTrue($result);

		$result = Arr::getValue($data, 'six', null, 'word');

		$this->assertEquals($result, 'good123');

		$result = Arr::getValue($data, 'seven', null, 'float');

		$this->assertTrue(is_float($result), 'Value is not a float');
		$this->assertEquals($result, 5.5);
	}

	/**
	 * Tests extracting a column from an array
	 *
	 * @covers  \Hubzero\Utility\Arr::getColumn
	 * @return  void
	 **/
	public function testGetColumn()
	{
		$arrs = array(
			array(
				'id' => 1,
				'name' => 'Joe',
				'age' => 27
			),
			array(
				'id' => 2,
				'name' => 'Susan',
				'age' => 24
			),
		);

		$item = new stdClass;
		$item->id = 3;
		$item->name = 'Frank';
		$item->age = 56;

		$arrs[] = $item;

		$item = new stdClass;
		$item->id = 4;
		$item->name = 'Helen';
		$item->age = 32;

		$arrs[] = $item;

		$result = Arr::getColumn($arrs, 'id');

		$this->assertEquals($result, array(1, 2, 3, 4));

		$result = Arr::getColumn($arrs, 'name');

		$this->assertEquals($result, array('Joe', 'Susan', 'Frank', 'Helen'));
	}

	/**
	 * Tests that #filterKeys filters correctly
	 *
	 * @covers  \Hubzero\Utility\Arr::filterKeys
	 * @return  void
	 **/
	public function testFilterKeysFilters()
	{
		$unfiltered = ['a' => 0, 'b' => 1, 'c' => 2];
		$whitelist = ['b'];

		$filtered = Arr::filterKeys($unfiltered, $whitelist);

		$this->assertEquals(array_keys($filtered), $whitelist);
	}

	/**
	 * Tests that #filterKeys does not change argument array
	 *
	 * @covers  \Hubzero\Utility\Arr::filterKeys
	 * @return  void
	 **/
	public function testFilterKeysNonDestructive()
	{
		$original = ['a' => 0, 'b' => 1, 'c' => 2];
		$copy = $original;

		Arr::filterKeys($original, []);

		$this->assertEquals($original, $copy);
	}

	/**
	 * Tests that #pluck unsets the given key
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckRemovesKey()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => 2];
		$key = 'c';

		Arr::pluck($array, $key);

		$this->assertFalse(array_key_exists($key, $array));
	}

	/**
	 * Tests that #pluck returns value under the given key
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckReturnsValue()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => 2];
		$key = 'c';
		$value = $array[$key];

		$pluckValue = Arr::pluck($array, $key);

		$this->assertEquals($value, $pluckValue);
	}

	/**
	 * Tests that #pluck returns default when key is missing
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckReturnsDefaultIfKeyMissing()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => 2];
		$default = 'test';
		$key = 'd';

		$pluckValue = Arr::pluck($array, $key, $default);

		$this->assertEquals($default, $pluckValue);
	}

	/**
	 * Tests that #pluck returns default when value is null
	 *
	 * @covers  \Hubzero\Utility\Arr::pluck
	 * @return  void
	 **/
	public function testPluckReturnsDefaultIfValueNull()
	{
		$array = ['a' => 0, 'b' => 1, 'c' => null];
		$default = 'test';
		$key = 'c';

		$pluckValue = Arr::pluck($array, $key, $default);

		$this->assertEquals($default, $pluckValue);
	}
}
