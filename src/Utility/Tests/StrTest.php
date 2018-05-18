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

		$string = -5;

		$result = Str::pad($string, 4);

		$this->assertEquals(strlen($result), 5);
		$this->assertEquals($result, 'n0005');
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

	/**
	 * Tests if a given string is truncated starting from the end.
	 *
	 * @covers  \Hubzero\Utility\Str::tail
	 * @return  void
	 **/
	public function testTail()
	{
		$string = 'Cras mattis consectetur purus sit amet fermentum.';
		$options = array();

		$result = Str::tail($string, 200, $options);

		$this->assertEquals($result, $string);

		$result = Str::tail($string, 25, $options);

		$this->assertEquals($result, '...us sit amet fermentum.');
		$this->assertEquals(strlen($result), 25);

		$options['ellipsis'] = '!!!';
		$result = Str::tail($string, 25, $options);

		$this->assertEquals($result, '!!!us sit amet fermentum.');

		$options['exact'] = false;
		$result = Str::tail($string, 25, $options);

		$this->assertEquals($result, '!!!sit amet fermentum.');
	}

	/**
	 * Tests if a given string is truncated starting from the end.
	 *
	 * @covers  \Hubzero\Utility\Str::insert
	 * @return  void
	 **/
	public function testInsert()
	{
		$result = Str::insert(':name is :age years old.', array('name' => 'Bob', 'age' => '65'));

		$this->assertEquals($result, 'Bob is 65 years old.');

		$result = Str::insert('*name is *age years old.', array('name' => 'Bob', 'age' => '65'), array('before' => '*'));

		$this->assertEquals($result, 'Bob is 65 years old.');

		$result = Str::insert('*name* is *age* years *old.', array('name' => 'Bob', 'age' => '65'), array('before' => '*', 'after' => '*'));

		$this->assertEquals($result, 'Bob is 65 years *old.');
	}

	/**
	 * Tests replacing &amp; with & for XHTML compliance
	 *
	 * @covers  \Hubzero\Utility\Str::ampReplace
	 * @return  void
	 **/
	public function testAmpReplace()
	{
		$result = Str::ampReplace('foo=bar&one=two');

		$this->assertEquals($result, 'foo=bar&amp;one=two');

		$result = Str::ampReplace('Cras mattis &#f0c2; consectetur & purus &amp; sit &&amp; amet &amp;amp; fermentum.');

		$this->assertEquals($result, 'Cras mattis &#f0c2; consectetur &amp; purus &amp; sit &&amp; amet &amp; fermentum.');
	}

	/**
	 * Tests truncating a block of text
	 *
	 * @covers  \Hubzero\Utility\Str::truncate
	 * @return  void
	 **/
	public function testTruncate()
	{
		$str = 'Cras mattis consectetur purus sit amet fermentum.';

		$result = Str::truncate($str, 30);

		$this->assertEquals($result, 'Cras mattis consectetur...');

		$result = Str::truncate($str, 30, array('ellipsis' => '!!!'));

		$this->assertEquals($result, 'Cras mattis consectetur!!!');

		$result = Str::truncate($str, 30, array('exact' => true));

		$this->assertEquals($result, 'Cras mattis consectetur pur...');
		$this->assertEquals(strlen($result), 30);

		$str = '<p>Cras <strong>mattis</strong> consectetur purus sit amet fermentum.</p>';

		$result = Str::truncate($str, 30, array('html' => true));

		$this->assertEquals($result, '<p>Cras <strong>mattis</strong> consectetur…</p>');

		$result = Str::truncate($str, 30, array('html' => true, 'exact' => true));

		$this->assertEquals($result, '<p>Cras <strong>mattis</strong> consectetur purus…</p>');
	}

	/**
	 * Tests extracting an excerpt from text
	 *
	 * @covers  \Hubzero\Utility\Str::excerpt
	 * @return  void
	 **/
	public function testExcerpt()
	{
		$str = 'Cras mattis consectetur purus sit amet fermentum.';

		$result = Str::excerpt($str, 'sit', 3);

		$this->assertEquals($result, '...us sit am...');

		$result = Str::excerpt($str, 'fermentum.', 3);

		$this->assertEquals($result, '...et fermentum.');

		$result = Str::excerpt($str, 'purus sit', 2, '!!!');

		$this->assertEquals($result, '!!!r purus sit a!!!');

		$str = 'Cras mattis consectetur purus sit amet fermentum.';

		$result = Str::excerpt($str, '', 10);

		$this->assertEquals($result, 'Cras mattis...');
	}
}
