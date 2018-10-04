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
use Hubzero\Base\ClientManager;

/**
 * ClientManager test
 */
class ClientManagerTest extends Basic
{
	/**
	 * Sample data
	 *
	 * @var  array
	 */
	protected $data = array(
		'one'   => 'for the money',
		'two'   => 'for the show',
		'three' => 'to get ready',
		'four'  => 'to go'
	);

	/**
	 * Test client()
	 *
	 * @covers  \Hubzero\Base\ClientManager::client
	 * @return  void
	 **/
	public function testClient()
	{
		$clients = ClientManager::client();

		$this->assertCount(7, $clients);

		$client = ClientManager::client(1);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'administrator');

		$client = ClientManager::client('api', true);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'api');
		$this->assertEquals($client->id, 4);

		$client = ClientManager::client('site');

		$this->assertFalse(is_object($client));
	}

	/**
	 * Test modify()
	 *
	 * @covers  \Hubzero\Base\ClientManager::modify
	 * @return  void
	 **/
	public function testModify()
	{
		ClientManager::modify(1, 'name', 'adminstuff');

		$client = ClientManager::client(1);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'adminstuff');

		ClientManager::modify(1, 'name', 'administrator');
	}

	/**
	 * Test append()
	 *
	 * @covers  \Hubzero\Base\ClientManager::append
	 * @return  void
	 **/
	public function testAppend()
	{
		$clients = ClientManager::client();

		$foo = array(
			'id' => 9,
			'name' => 'foo',
			'url' => 'foo'
		);

		$bar = new \stdClass;
		$bar->id = 10;
		$bar->name = 'bar';
		$bar->url = 'bar';

		$tur = new \stdClass;
		$tur->name = 'tur';
		$tur->url = 'tur';

		$glu = 'foobar';

		ClientManager::append($tur);
		ClientManager::append($foo);
		ClientManager::append($bar);

		$this->assertFalse(ClientManager::append($glu));

		$client = ClientManager::client('tur', true);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'tur');
		$this->assertEquals($client->id, count($clients));

		$client = ClientManager::client(9);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'foo');

		$client = ClientManager::client(10);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'bar');

		$client = ClientManager::client('foo', true);

		$this->assertTrue(is_object($client));
		$this->assertEquals($client->name, 'foo');
		$this->assertEquals($client->id, 9);
	}
}
