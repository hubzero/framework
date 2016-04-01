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
 * Notification handler test
 */
class HandlerTest extends Basic
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
			'domain'  => null
		),
		array(
			'message' => 'This is a success message!',
			'type'    => 'success',
			'domain'  => null
		),
		array(
			'message' => 'This is a warning message!',
			'type'    => 'warning',
			'domain'  => null
		),
		array(
			'message' => 'This is an error message.',
			'type'    => 'error',
			'domain'  => null
		)
	);

	/**
	 * Test that the lit of messages returned by the handler
	 *
	 * @covers  \Hubzero\Notification\Handler::message
	 * @return  void
	 **/
	public function testMessage()
	{
		$handler = new Handler(new Memory);

		$item = $this->data[0];

		$this->assertInstanceOf('Hubzero\Notification\Handler', $handler->message($item['message']));

		$messages = $handler->messages();

		$this->assertTrue(is_array($messages), 'Getting all messages should return an array');
		$this->assertCount(1, $messages, 'Total messages returned does not equal number added');
	}

	/**
	 * Test that the lit of messages returned by the handler
	 *
	 * @covers  \Hubzero\Notification\Handler::messages
	 * @return  void
	 **/
	public function testMessages()
	{
		$handler = new Handler(new Memory);

		foreach ($this->data as $item)
		{
			$handler->message($item['message'], $item['type'], $item['domain']);
		}

		$messages = $handler->messages();

		$this->assertTrue(is_array($messages), 'Getting all messages should return an array');
		$this->assertCount(count($this->data), $messages, 'Total messages returned does not equal number added');
	}

	/**
	 * Tests clear() empties the message bag
	 *
	 * @covers  \Hubzero\Notification\Handler::clear
	 * @return  void
	 **/
	public function testClear()
	{
		$handler = new Handler(new Memory);

		foreach ($this->data as $item)
		{
			$handler->message($item['message'], $item['type'], 'one');
		}

		foreach ($this->data as $item)
		{
			$handler->message($item['message'], $item['type'], 'two');
		}

		$handler->clear('one');

		$this->assertTrue($handler->isEmpty('one'));
		$this->assertFalse($handler->any('one'));
		$m = $handler->messages('one');
		$this->assertCount(0, $m, 'Total messages returned does not equal number added');

		$this->assertFalse($handler->isEmpty('two'));
		$this->assertTrue($handler->any('two'));
		$m = $handler->messages('two');
		$this->assertCount(count($this->data), $m, 'Total messages returned does not equal number added');
	}

	/**
	 * Test that messages added with info() are
	 * assigned the appropriate type.
	 *
	 * @covers  \Hubzero\Notification\Handler::info
	 * @return  void
	 **/
	public function testInfo()
	{
		$handler = new Handler(new Memory);
		$handler->info('Lorem ipsum dol.');

		$m = $handler->messages();
		$message = array_pop($m);

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'info');
	}

	/**
	 * Test that messages added with success() are
	 * assigned the appropriate type.
	 *
	 * @covers  \Hubzero\Notification\Handler::success
	 * @return  void
	 **/
	public function testSuccess()
	{
		$handler = new Handler(new Memory);
		$handler->success('Lorem ipsum dol.');

		$m = $handler->messages();
		$message = array_pop($m);

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'success');
	}

	/**
	 * Test that messages added with warning() are
	 * assigned the appropriate type.
	 *
	 * @covers  \Hubzero\Notification\Handler::warning
	 * @return  void
	 **/
	public function testWarning()
	{
		$handler = new Handler(new Memory);
		$handler->warning('Lorem ipsum dol.');

		$m = $handler->messages();
		$message = array_pop($m);

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'warning');
	}

	/**
	 * Test that messages added with error() are
	 * assigned the appropriate type.
	 *
	 * @covers  \Hubzero\Notification\Handler::error
	 * @return  void
	 **/
	public function testError()
	{
		$handler = new Handler(new Memory);
		$handler->error('Lorem ipsum dol.');

		$m = $handler->messages();
		$message = array_pop($m);

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'error');
	}

	/**
	 * Test that any() returns FALSE if there are no
	 * messages and TRUE if there.
	 *
	 * @covers  \Hubzero\Notification\Handler::any
	 * @return  void
	 **/
	public function testAny()
	{
		$handler = new Handler(new Memory);

		$this->assertFalse($handler->any());

		$handler->error('Lorem ipsum dol.');

		$this->assertTrue($handler->any());
	}

	/**
	 * Test that isEmpty() returns TRUE if there are no
	 * messages and FALSE if there.
	 *
	 * @covers  \Hubzero\Notification\Handler::isEmpty
	 * @return  void
	 **/
	public function testIsEmpty()
	{
		$handler = new Handler(new Memory);

		$this->assertTrue($handler->isEmpty());

		$handler->error('Lorem ipsum dol.');

		$this->assertFalse($handler->isEmpty());
	}

	/**
	 * Test that toArray() returns an array of messages
	 *
	 * @covers  \Hubzero\Notification\Handler::toArray
	 * @return  void
	 **/
	public function testToArray()
	{
		$handler = new Handler(new Memory);

		foreach ($this->data as $item)
		{
			$handler->message($item['message'], $item['type'], $item['domain']);
		}

		$messages = $handler->toArray();

		$this->assertTrue(is_array($messages), 'Getting all messages should return an array');
		$this->assertCount(count($this->data), $messages, 'Total messages returned does not equal number added');
	}

	/**
	 * Test that toJson() returns a JSON string
	 *
	 * @covers  \Hubzero\Notification\Handler::toJson
	 * @return  void
	 **/
	public function testToJson()
	{
		$handler = new Handler(new Memory);

		foreach ($this->data as $item)
		{
			$handler->message($item['message'], $item['type'], $item['domain']);
		}

		$messages = $handler->toJson();

		$this->assertTrue(is_string($messages));
		$this->assertJson($messages);
	}

	/**
	 * Test __toString
	 *
	 * @covers  \Hubzero\Notification\Handler::__toString
	 * @return  void
	 **/
	public function testToString()
	{
		$handler = new Handler(new Memory);

		foreach ($this->data as $item)
		{
			$handler->message($item['message'], $item['type'], $item['domain']);
		}

		$messages = (string) $handler;

		$this->assertTrue(is_string($messages));
		$this->assertJson($messages);
	}
}
