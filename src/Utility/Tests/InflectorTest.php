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
use Hubzero\Utility\Inflector;

/**
 * Inflector utility test
 */
class InflectorTest extends Basic
{
	// Words that should not be inflected.
	protected static $uncountable_words = array(
		'equipment',
		'information',
		'rice',
		'money',
		'species',
		'series',
		'fish',
		'meta',
		'metadata',
		'buffalo',
		'elk',
		'rhinoceros',
		'salmon',
		'bison',
		'headquarters'
	);

	protected static $strings = array(
		// -en
		'ox' => 'oxen',
		// -ices
		'mouse' => 'mice',
		'louse' => 'lice',
		// -es
		'search' => 'searches',
		'switch' => 'switches',
		'fix' => 'fixes',
		'box' => 'boxes',
		'process' => 'processes',
		// -ies
		'query' => 'queries',
		'ability' => 'abilities',
		'agency' => 'agencies',
		// -s
		'hive' => 'hives',
		'archive' => 'archives',
		// -ves
		'half' => 'halves',
		'safe' => 'saves',
		'wife' => 'wives',
		// -ses
		'basis' => 'bases',
		'diagnosis' => 'diagnoses',
		// -a
		'datum' => 'data',
		'medium' => 'media',
		// -eople
		'person' => 'people',
		'salesperson' => 'salespeople',
		// -en
		'man' => 'men',
		'woman' => 'women',
		'spokesman' => 'spokesmen',
		// hildren
		'child' => 'children',
		// -oes
		//'buffalo' => 'buffaloes',
		'tomato' => 'tomatoes',
		// -ses
		'bus' => 'buses',
		'campus' => 'campuses',
		// -es
		'alias' => 'aliases',
		'status' => 'statuses',
		'virus' => 'viruses',
		// -i
		'octopus' => 'octopi',
		// -es
		'axis' => 'axes',
		'crisis' => 'crises',
		'testis' => 'testes',
		// -s
		'cat' => 'cats',
		'dog' => 'dogs',
		'cup' => 'cups',
		'car' => 'cars'
	);

	/**
	 * Tests is_countable
	 *
	 * @covers  \Hubzero\Utility\Inflector::is_countable
	 * @return  void
	 **/
	public function testIsCountable()
	{
		foreach (self::$uncountable_words as $word)
		{
			$result = Inflector::is_countable($word);

			$this->assertFalse($result);
		}

		foreach (self::$strings as $word)
		{
			$result = Inflector::is_countable($word);

			$this->assertTrue($result);
		}
	}

	/**
	 * Tests pluralizing words
	 *
	 * @covers  \Hubzero\Utility\Inflector::pluralize
	 * @return  void
	 **/
	public function testPluralize()
	{
		foreach (self::$strings as $singular => $plural)
		{
			$result = Inflector::pluralize($singular);

			$this->assertEquals($result, $plural);
		}

		foreach (self::$uncountable_words as $word)
		{
			$result = Inflector::pluralize($word);

			$this->assertEquals($result, $word);
		}
	}

	/**
	 * Tests singularizing words
	 *
	 * @covers  \Hubzero\Utility\Inflector::singularize
	 * @return  void
	 **/
	public function testSingularize()
	{
		foreach (self::$strings as $singular => $plural)
		{
			$result = Inflector::singularize($plural);

			$this->assertEquals($result, $singular);
		}

		foreach (self::$uncountable_words as $word)
		{
			$result = Inflector::singularize($word);

			$this->assertEquals($result, $word);
		}
	}
}
