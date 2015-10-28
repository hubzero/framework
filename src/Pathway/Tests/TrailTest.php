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
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Pathway\Tests;

use Hubzero\Test\Basic;
use Hubzero\Pathway\Trail;
use Hubzero\Pathway\Item;

/**
 * Pathway trail tests
 */
class TrailTest extends Basic
{
	/**
	 * Tests:
	 *  1. the append() method is chainable
	 *  2. append() adds to the items list
	 *  3. append() adds an Hubzero\Pathway\Item object to the items list
	 *  4. append() adds to the END of the items list
	 *
	 * @return  void
	 **/
	public function testAppend()
	{
		$pathway = new Trail();

		$this->assertInstanceOf('Hubzero\Pathway\Trail', $pathway->append('Crumb 1', 'index.php?option=com_lorem'));

		$this->assertCount(1, $pathway->items(), 'List of crumbs should have returned one Item');

		$name = 'Crumb 2';
		$link = 'index.php?option=com_ipsum';

		$pathway->append($name, $link);

		$items = $pathway->items();
		$item = array_pop($items);

		$this->assertInstanceOf('Hubzero\Pathway\Item', $item);
		$this->assertEquals($item->name, $name);
		$this->assertEquals($item->link, $link);
	}

	/**
	 * Tests:
	 *  1. the prepend() method is chainable
	 *  2. prepend() adds to the items list
	 *  3. prepend() adds an Hubzero\Pathway\Item object to the items list
	 *  4. prepend() adds to the BEGINNING of the items list
	 *
	 * @return  void
	 **/
	public function testPrepend()
	{
		$pathway = new Trail();

		$this->assertInstanceOf('Hubzero\Pathway\Trail', $pathway->prepend('Crumb 1', 'index.php?option=com_lorem'));

		$this->assertCount(1, $pathway->items(), 'List of crumbs should have returned one Item');

		$name = 'Crumb 2';
		$link = 'index.php?option=com_ipsum';

		$pathway->prepend($name, $link);

		$items = $pathway->items();
		$item = array_shift($items);

		$this->assertInstanceOf('Hubzero\Pathway\Item', $item);
		$this->assertEquals($item->name, $name);
		$this->assertEquals($item->link, $link);
	}

	/**
	 * Tests:
	 *  1. the names() method returns an array
	 *  2. the number of items in the array matches the number of items added
	 *  3. the array returned contains just the names of the items added
	 *
	 * @return  void
	 **/
	public function testNames()
	{
		$data = [
			'Crumb 1',
			'Crumb 2'
		];

		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');

		$names = $pathway->names();

		$this->assertTrue(is_array($names), 'names() should return an array');
		$this->assertCount(2, $names, 'names() returned incorrect number of entries');
		$this->assertEquals($names, $data);
	}

	/**
	 * Tests:
	 *  1. the items() method returns an array
	 *  2. the number of items in the array matches the number of items added
	 *  3. the array returned contains a Hubzero\Pathway\Item object for each entry added
	 *
	 * @return  void
	 **/
	public function testItems()
	{
		$data = [
			new Item('Crumb 1', 'index.php?option=com_lorem'),
			new Item('Crumb 2', 'index.php?option=com_ipsum')
		];

		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');

		$items = $pathway->items();

		$this->assertTrue(is_array($items), 'items() should return an array');
		$this->assertCount(2, $items, 'items() should have returned two Items');
		$this->assertEquals($items, $data);
	}

	/**
	 * Tests:
	 *  1. the names() method returns an array
	 *  2. the number of items in the array matches the number of items added
	 *  3. the array returned contains just the names of the items added
	 *
	 * @return  void
	 **/
	public function testClear()
	{
		$pathway = new Trail();
		$pathway->append('Crumb 1', 'index.php?option=com_lorem');
		$pathway->append('Crumb 2', 'index.php?option=com_ipsum');
		$pathway->clear();

		$items = $pathway->items();

		$this->assertTrue(empty($items), 'items() should return an empty array after calling clear()');
	}
}
