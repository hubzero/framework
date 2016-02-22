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
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Tests\Mock;

use Hubzero\Content\Auditor\Test;
use Hubzero\Content\Auditor\Result;

/**
 * Random test class
 */
class Checker implements Test
{
	/**
	 * Register a test
	 *
	 * @return  string
	 */
	public function name()
	{
		return 'Mock Checker';
	}

	/**
	 * Base examing method.
	 * Randomly decides if something passed (1), skipped (0), or failed (-1).
	 *
	 * @param   string|array  $data
	 * @return  object
	 */
	public function examine(array $data, array $options = array())
	{
		$status = rand(-1, 1);
		$meta   = array();

		$result = new Result();
		$result->set([
			'scope_id' => $data['id'],
			'status'   => $status,
			'notes'    => json_encode($meta)
		]);

		return $result;
	}
}
