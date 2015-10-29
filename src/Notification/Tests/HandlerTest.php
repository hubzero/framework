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
			'domain'  => null,
		),
		array(
			'message' => 'This is a success message!',
			'type'    => 'success',
			'domain'  => null,
		),
		array(
			'message' => 'This is a warning message!',
			'type'    => 'warning',
			'domain'  => null,
		),
		array(
			'message' => 'This is an error message.',
			'type'    => 'error',
			'domain'  => null,
		),
	);

	/**
	 * Test that the lit of messages returned by the handler
	 *
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
		$this->assertCount(0, $handler->messages('one'), 'Total messages returned does not equal number added');

		$this->assertFalse($handler->isEmpty('two'));
		$this->assertTrue($handler->any('two'));
		$this->assertCount(count($this->data), $handler->messages('two'), 'Total messages returned does not equal number added');
	}

	/**
	 * Test that messages added with info() are
	 * assigned the appropriate type.
	 *
	 * @return  void
	 **/
	public function testInfo()
	{
		$handler = new Handler(new Memory);
		$handler->info('Lorem ipsum dol.');

		$message = array_pop($handler->messages());

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'info');
	}

	/**
	 * Test that messages added with success() are
	 * assigned the appropriate type.
	 *
	 * @return  void
	 **/
	public function testSuccess()
	{
		$handler = new Handler(new Memory);
		$handler->success('Lorem ipsum dol.');

		$message = array_pop($handler->messages());

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'success');
	}

	/**
	 * Test that messages added with warning() are
	 * assigned the appropriate type.
	 *
	 * @return  void
	 **/
	public function testWarning()
	{
		$handler = new Handler(new Memory);
		$handler->warning('Lorem ipsum dol.');

		$message = array_pop($handler->messages());

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'warning');
	}

	/**
	 * Test that messages added with error() are
	 * assigned the appropriate type.
	 *
	 * @return  void
	 **/
	public function testError()
	{
		$handler = new Handler(new Memory);
		$handler->error('Lorem ipsum dol.');

		$message = array_pop($handler->messages());

		$this->assertTrue(is_array($message), 'Individual messages should be of type array');
		$this->assertEquals($message['type'], 'error');
	}
}
