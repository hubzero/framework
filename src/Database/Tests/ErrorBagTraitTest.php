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

namespace Hubzero\Database\Tests;

use Hubzero\Test\Basic;
use Hubzero\Database\Traits\ErrorBag;
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
		$this->obj = $this->getObjectForTrait('Hubzero\Database\Traits\ErrorBag');

		parent::setUp();
	}

	/**
	 * Test ErrorBag methods
	 *
	 * @covers  \Hubzero\Database\Traits\ErrorBag::addError
	 * @covers  \Hubzero\Database\Traits\ErrorBag::setErrors
	 * @covers  \Hubzero\Database\Traits\ErrorBag::getError
	 * @covers  \Hubzero\Database\Traits\ErrorBag::getErrors
	 * @return  void
	 **/
	public function testErrorBag()
	{
		// Test that an array is returned
		$errors = $this->obj->getErrors();

		// Test that the array is empty
		$this->assertTrue(is_array($errors));
		$this->assertCount(0, $errors);

		// Set some errors
		$this->obj->addError('Donec sed odio dui.');
		$this->obj->addError(new Exception('Aenean lacinia bibendum.'));
		$this->obj->addError('Nulla sed consectetur.');

		// Get the list of set errors
		$errors = $this->obj->getErrors();

		// Make sure:
		//    - the list of errors matches the number of errors set
		//    - getError() returns the first error set
		$this->assertCount(3, $errors);
		$this->assertEquals($this->obj->getError(), 'Donec sed odio dui.');

		// Test setting the entire list
		$newerrors = array(
			'Integer posuere erat',
			'Ante venenatis dapibus',
			'Posuere velit aliquet.'
		);

		$this->obj->setErrors($newerrors);

		$this->assertEquals($this->obj->getErrors(), $newerrors);
	}
}
