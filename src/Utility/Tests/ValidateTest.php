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
use Hubzero\Utility\Validate;

/**
 * Validate utility test
 */
class ValidateTest extends Basic
{
	/**
	 * Tests if a value is a boolean integer or true/false
	 *
	 * @covers  \Hubzero\Utility\Validate::boolean
	 * @return  void
	 **/
	public function testBoolean()
	{
		$tests = array(
			0 => true,
			1 => true,
			'foo' => false,
			'1' => true,
			'0' => true,
			'true' => false,
			3543 => false
		);

		foreach ($tests as $test => $result)
		{
			$this->assertEquals(Validate::boolean($test), $result);
		}

		$this->assertTrue(Validate::boolean(true));
		$this->assertTrue(Validate::boolean(false));
	}

	/**
	 * Tests if a value is within a specified range.
	 *
	 * @covers  \Hubzero\Utility\Validate::between
	 * @return  void
	 **/
	public function testBetween()
	{
		$tests = array(
			array(
				'str' => 'Donec id elit non mi porta gravida at eget metus.',
				'min' => 3,
				'max' => 100,
				'val' => true
			),
			array(
				'str' => 'Vehicula Sit Dolor',
				'min' => 1,
				'max' => 7,
				'val' => false
			),
			array(
				'str' => '123456789',
				'min' => 0,
				'max' => 10,
				'val' => true
			),
			array(
				'str' => 'dolo',
				'min' => 5,
				'max' => 8,
				'val' => false
			),
		);

		foreach ($tests as $test)
		{
			$this->assertEquals(Validate::between($test['str'], $test['min'], $test['max']), $test['val']);
		}
	}

	/**
	 * Tests if a value is numeric.
	 *
	 * @covers  \Hubzero\Utility\Validate::numeric
	 * @return  void
	 **/
	public function testNumeric()
	{
		$tests = array(
			"42" => true,
			1337 => true,
			0x539 => true,
			02471 => true,
			0b10100111001 => true,
			1337e0 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::numeric($value), $result);
		}

		$this->assertFalse(Validate::numeric(array()));
	}

	/**
	 * Tests if value is an integer
	 *
	 * @covers  \Hubzero\Utility\Validate::integer
	 * @return  void
	 **/
	public function testInteger()
	{
		$tests = array(
			"42" => true,
			'+51' => true,
			-16 => true,
			1337 => true,
			0x539 => false,
			02471 => true,
			1337e0 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::integer($value), $result);
		}

		$this->assertFalse(Validate::integer(array()));
	}

	/**
	 * Tests if value is a positive integer
	 *
	 * @covers  \Hubzero\Utility\Validate::positiveInteger
	 * @return  void
	 **/
	public function testPositiveInteger()
	{
		$tests = array(
			0 => false,
			"42" => true,
			'+51' => true,
			-16 => false,
			1337 => true,
			0x539 => true,
			02471 => true,
			1337e0 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::positiveInteger($value), $result);
		}

		$this->assertFalse(Validate::positiveInteger(array()));
	}

	/**
	 * Tests if value is a non-negative integer
	 *
	 * @covers  \Hubzero\Utility\Validate::nonNegativeInteger
	 * @return  void
	 **/
	public function testNonNegativeInteger()
	{
		$tests = array(
			0 => true,
			"42" => true,
			'+51' => true,
			-16 => false,
			1337 => true,
			"not numeric" => false,
			9.1 => true,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::nonNegativeInteger($value), $result);
		}

		$this->assertFalse(Validate::nonNegativeInteger(array()));
	}

	/**
	 * Tests if value is a negative integer
	 *
	 * @covers  \Hubzero\Utility\Validate::negativeInteger
	 * @return  void
	 **/
	public function testNegativeInteger()
	{
		$tests = array(
			0 => false,
			"42" => false,
			'+51' => false,
			-16 => true,
			1337 => false,
			"not numeric" => false,
			9.1 => false,
			null => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::negativeInteger($value), $result);
		}

		$this->assertFalse(Validate::negativeInteger(array()));
	}

	/**
	 * Tests if value is an orcid
	 *
	 * @covers  \Hubzero\Utility\Validate::orcid
	 * @return  void
	 **/
	public function testOrcid()
	{
		$tests = array(
			'0000-0000-0000-0000' => true,
			'123-45635-7891-0112' => false,
			'123A-45B6-7CD1-E190' => false,
			'1234567891011112' => false,
			'1234-4567-8910-1112' => true,
			'1234-4567-8910' => false,
			'1234-4567' => false,
			'1234' => false,
			'A123-4567-8910-1112' => false
		);

		foreach ($tests as $value => $result)
		{
			$this->assertEquals(Validate::orcid($value), $result);
		}
	}
}
