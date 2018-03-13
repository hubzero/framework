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
use Hubzero\Utility\Str;

/**
 * Str utility test
 */
class StrTest extends Basic
{
	/**
	 * Tests extracting key/value pairs out of a string with XML style attributes
	 *
	 * @covers  \Hubzero\Utility\Str::parseAttributes
	 * @return  void
	 **/
	public function testParseAttributes()
	{
		$strings = array(
			'a href="http://hubzero.org" title="HUBzero"' => array(
				'href'  => 'http://hubzero.org',
				'title' => 'HUBzero'
			),
			'<field description="Duis mollis, est non commodo luctus." default=0 height=55 width="35" type = "list">' => array(
				'description' => 'Duis mollis, est non commodo luctus.',
				//'default'     => '0',
				//'height'      => '55',
				'width'       => '35',
				'type'        => 'list'
			),
			'Sed posuere consectetur est at lobortis.' => array()
		);

		foreach ($strings as $string => $pairs)
		{
			$result = Str::parseAttributes($string);

			$this->assertTrue(is_array($result), 'Value returned was not an array');
			$this->assertEquals($result, $pairs);
		}
	}

	/**
	 * Tests converting a string to snake case
	 *
	 * @covers  \Hubzero\Utility\Str::snake
	 * @return  void
	 **/
	public function testSnake()
	{
		$start = 'this text is snake case';

		$result = Str::snake($start, '_');

		$this->assertEquals($result, 'this_text_is_snake_case');

		$result = Str::snake($start, '+');

		$this->assertEquals($result, 'this+text+is+snake+case');

		$result = Str::snake('thistextissnakecase');

		$this->assertEquals($result, 'thistextissnakecase');
	}

	/**
	 * Tests splitting a string in camel case format
	 *
	 * @covers  \Hubzero\Utility\Str::camel
	 * @return  void
	 **/
	public function testCamel()
	{
		$start = 'this text is camel case';
		$end   = 'ThisTextIsCamelCase';

		$result = Str::camel($start);

		$this->assertEquals($result, $end);

		$start = 'This-text_Is camelcase';
		$end   = 'ThisTextIsCamelcase';

		$result = Str::camel($start);

		$this->assertEquals($result, $end);
	}

	/**
	 * Tests splitting a string in camel case format
	 *
	 * @covers  \Hubzero\Utility\Str::splitCamel
	 * @return  void
	 **/
	public function testSplitCamel()
	{
		$start = 'ThisTextIsCamelCase';
		$end   = array('This', 'Text', 'Is', 'Camel', 'Case');

		$result = Str::splitCamel($start);

		$this->assertEquals($result, $end);

		$start = 'ThisTextisCamelCase';
		$end   = array('This', 'Textis', 'Camel', 'Case');

		$result = Str::splitCamel($start);

		$this->assertEquals($result, $end);
	}

	/**
	 * Tests if a given string contains a given substring.
	 *
	 * @covers  \Hubzero\Utility\Str::contains
	 * @return  void
	 **/
	public function testContains()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';

		$this->assertTrue(Str::contains($string, 'purus sit amet'), 'String does not contain given value.');

		$strings = array(
			'lorem ipsum',
			'mattis Cras',
			'mattis consectetur'
		);

		$this->assertTrue(Str::contains($string, $strings), 'String does not contain given value.');

		$this->assertFalse(Str::contains($string, 'felis euismod'), 'String does not contain given value.');

		$strings = array(
			'Donec id',
			'elit non mi',
			'porta gravida.'
		);

		$this->assertFalse(Str::contains($string, $strings), 'String does not contain given value.');
	}

	/**
	 * Tests if a given string starts with a given substring.
	 *
	 * @covers  \Hubzero\Utility\Str::startsWith
	 * @return  void
	 **/
	public function testStartsWith()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';

		$this->assertTrue(Str::startsWith($string, 'Cras mattis consectetur'), 'String does not start with given value.');

		$strings = array(
			'mattis consectetur',
			'Cras',
			'amet fermentum.'
		);

		$this->assertTrue(Str::startsWith($string, $strings), 'String does not start with given value.');

		$this->assertFalse(Str::startsWith($string, 'consectetur purus'), 'String does not start with given value.');

		$strings = array(
			'cras mattis',
			'consectetur purus',
			'amet fermentum.'
		);

		$this->assertFalse(Str::startsWith($string, $strings), 'String does not start with given value.');
	}

	/**
	 * Tests if a given string ends with a given substring.
	 *
	 * @covers  \Hubzero\Utility\Str::endsWith
	 * @return  void
	 **/
	public function testEndsWith()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';

		$this->assertTrue(Str::endsWith($string, 'sit amet fermentum.'), 'String does not end with given value.');

		$strings = array(
			'Cras mattis',
			'consectetur purus',
			'amet fermentum.'
		);

		$this->assertTrue(Str::endsWith($string, $strings), 'String does not end with given value.');

		$this->assertFalse(Str::endsWith($string, 'consectetur purus'), 'String does not end with given value.');

		$strings = array(
			'Cras mattis',
			'consectetur purus',
			'amet fermentum'
		);

		$this->assertFalse(Str::endsWith($string, $strings), 'String does not end with given value.');
	}

	/**
	 * Tests prefixing a string to a specificed length.
	 *
	 * @covers  \Hubzero\Utility\Str::pad
	 * @return  void
	 **/
	public function testPad()
	{
		$string = '5';

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 4);
		$this->assertEquals(substr($result, 0, 3), '000');
		$this->assertEquals(substr($result, -1), $string);

		$string = '05';

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 4);
		$this->assertEquals(substr($result, 0, 3), '000');
		$this->assertEquals(substr($result, -1), $string);

		$string = '12345';

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 5);
		$this->assertEquals($result, $string);
	}

	/**
	 * Tests prefixing a string to a specificed length.
	 *
	 * @covers  \Hubzero\Utility\Str::obfuscate
	 * @return  void
	 **/
	public function testObfuscate()
	{
		$string = 'test@example.com';

		$result = Str::obfuscate($string);

		$this->assertNotEquals($result, $string);

		preg_match('/&#/', $result, $matches);

		$this->assertTrue(count($matches) > 0);
	}
}
