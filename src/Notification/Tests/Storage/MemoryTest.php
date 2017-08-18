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

namespace Hubzero\Notification\Tests;

use Hubzero\Test\Basic;
use Hubzero\Notification\Handler;
use Hubzero\Notification\Storage\Memory;

/**
 * Notification Memory handler test
 */
class MemoryTest extends Basic
{
	/**
	 * Test data
	 *
	 * @var  array
	 */
	private $data = array(
		array(
			'message' => 'This is an info message.',
			'type'    => 'info',
			'domain'  => 'test'
		),
		array(
			'message' => 'This is a success message!',
			'type'    => 'success',
			'domain'  => 'test'
		),
		array(
			'message' => 'This is a warning message!',
			'type'    => 'warning',
			'domain'  => 'test'
		),
		array(
			'message' => 'This is an error message.',
			'type'    => 'error',
			'domain'  => 'test'
		)
	);

	/**
	 * Test that the constructor provides an empty message bag
	 *
	 * @covers  \Hubzero\Notification\Storage\Memory::__construct
	 * @return  void
	 **/
	public function testConstructor()
	{
		$memory = new Memory;

		$this->assertInstanceOf('Hubzero\Notification\MessageStore', $memory);
		$this->assertEquals(0, $memory->total('test'), 'Total messages returned does not equal number added');
	}

	/**
	 * Test that the store() method adds to the internal list
	 *
	 * @covers  \Hubzero\Notification\Storage\Memory::store
	 * @return  void
	 **/
	public function testStore()
	{
		$memory = new Memory;

		foreach ($this->data as $item)
		{
			$memory->store($item, $item['domain']);
		}

		$messages = $memory->retrieve('test');
		$this->assertCount(count($this->data), $messages, 'Total messages returned does not equal number added');

		foreach ($messages as $i => $message)
		{
			$this->assertEquals($this->data[$i]['message'], $message['message']);
			$this->assertEquals($this->data[$i]['type'], $message['type']);
		}
	}

	/**
	 * Test that the lit of messages returned by the handler
	 *
	 * @covers  \Hubzero\Notification\Storage\Memory::retrieve
	 * @return  void
	 **/
	public function testRetrieve()
	{
		$memory = new Memory;

		foreach ($this->data as $item)
		{
			$memory->store($item, $item['domain']);
		}

		$messages = $memory->retrieve('test');

		$this->assertTrue(is_array($messages), 'Getting all messages should return an array');
		$this->assertCount(count($this->data), $messages, 'Total messages returned does not equal number added');
	}

	/**
	 * Tests clear() empties the message bag
	 *
	 * @covers  \Hubzero\Notification\Storage\Memory::clear
	 * @return  void
	 **/
	public function testClear()
	{
		$memory = new Memory;

		foreach ($this->data as $item)
		{
			$memory->store($item, 'one');
		}

		foreach ($this->data as $item)
		{
			$memory->store($item, 'two');
		}

		$memory->clear('one');

		$messages = $memory->retrieve('one');

		$this->assertCount(0, $messages, 'Total messages returned does not equal number added');

		$messages = $memory->retrieve('two');

		$this->assertCount(count($this->data), $messages, 'Total messages returned does not equal number added');
	}

	/**
	 * Test that the total() method returns the correct count
	 *
	 * @covers  \Hubzero\Notification\Storage\Memory::total
	 * @return  void
	 **/
	public function testTotal()
	{
		$memory = new Memory;

		foreach ($this->data as $item)
		{
			$memory->store($item, 'one');
		}

		foreach ($this->data as $item)
		{
			$memory->store($item, 'two');
		}

		$this->assertEquals(count($this->data), $memory->total('one'), 'Total messages returned does not equal number added');
	}

	/**
	 * Test that the key() method generates keys correctly
	 *
	 * @covers  \Hubzero\Notification\Storage\Memory::key
	 * @return  void
	 **/
	public function testKey()
	{
		$memory = new Memory;

		$reflection = new \ReflectionClass(get_class($memory));
		$method = $reflection->getMethod('key');
		$method->setAccessible(true);

		$result = $method->invokeArgs($memory, array('one'));

		$this->assertEquals('one.application.queue', $result, 'Key should be of pattern {domain}.application.queue');

		$result = $method->invokeArgs($memory, array(''));

		$this->assertEquals('application.queue', $result, 'Key should just be application.queue');
	}
}
