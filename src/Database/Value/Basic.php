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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.1.0
 */

namespace Hubzero\Database\Value;

/**
 * Database basic value class
 */
class Basic
{
	/**
	 * The content of the value item
	 *
	 * @var  string
	 **/
	protected $content = null;

	/**
	 * Constructs the content of the value item
	 *
	 * @param   string  $content  The content of the given value
	 * @return  void
	 * @since   2.1.0
	 **/
	public function __construct($content)
	{
		$this->content = $content;
	}

	/**
	 * Builds the given string representation of the value object
	 *
	 * @param   object  $syntax  The syntax object with which the query is being built
	 * @return  string
	 * @since   2.1.0
	 **/
	public function build($syntax)
	{
		$syntax->bind(is_string($this->content) ? trim($this->content) : $this->content);

		return '?';
	}
}
