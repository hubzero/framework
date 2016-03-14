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
use Hubzero\Pagination\Item;

/**
 * Pagination Item test
 */
class ItemTest extends Basic
{
	/**
	 * Tests that data passed in constructor is set to correct properties
	 *
	 * @covers  \Hubzero\Pagination\Item::__construct
	 * @return  void
	 */
	public function testConstructor()
	{
		$text   = 'next';
		$prefix = 'pagenav';
		$base   = 25;
		$link   = 'index.php?option=com_tests';

		$item = new Item($text, $prefix, $base, $link);

		$this->assertInstanceOf('Hubzero\Pagination\Item', $item);

		$this->assertEquals($item->text, $text);
		$this->assertEquals($item->prefix, $prefix);
		$this->assertEquals($item->base, $base);
		$this->assertEquals($item->link, $link);
	}
}
