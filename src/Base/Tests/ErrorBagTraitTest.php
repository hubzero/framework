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
use Hubzero\Base\Traits\ErrorBag;
use Exception;

/**
 * ErrorBag Trait test
 */
class ErrorBagTraitTest extends Basic
{
	/**
	 * The object under test.
	 *
	 * @var  object
	 */
	private $traitObject;

	/**
	 * Sets up the fixture.
	 *
	 * @return  void
	 */
	public function setUp()
	{
		$this->obj = $this->getObjectForTrait('Hubzero\Base\Traits\ErrorBag');

		parent::setUp();
	}

	/**
	 * Test ErrorBag methods
	 *
	 * @covers  \Hubzero\Base\Object::setError
	 * @covers  \Hubzero\Base\Object::setErrors
	 * @covers  \Hubzero\Base\Object::getError
	 * @covers  \Hubzero\Base\Object::getErrors
	 * @return  void
	 **/
	public function testErrorBag()
	{
		$errors = $this->obj->getErrors();

		$this->assertTrue(is_array($errors));
		$this->assertCount(0, $errors);

		$this->obj->setError('Donec sed odio dui.');
		$this->obj->setError(new Exception('Aenean lacinia bibendum.'));
		$this->obj->setError('Nulla sed consectetur.');

		$errors = $this->obj->getErrors();

		$this->assertCount(3, $errors);
		$this->assertEquals($this->obj->getError(), 'Nulla sed consectetur.');
		$this->assertTrue(is_string($this->obj->getError(1)));
		$this->assertInstanceOf('Exception', $this->obj->getError(1, false));

		$this->obj->setError('Aenean lacinia bibendum.', 0);
		$err = $this->obj->getErrors();

		$this->assertEquals($this->obj->getError(0), 'Aenean lacinia bibendum.');
	}
}
