<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;

use Hubzero\Search\Adapter\Solr;
/*use Hubzero\Filesystem\Exception\FileNotFoundException;
use Hubzero\Filesystem\Exception\FileExistsException;
use FilesystemIterator;
use DirectoryIterator;
*/

/**
 * Hubzero class for performing Search and Indexing Operations.
 */
class Search
{
	/**
	 * AdapterInterface
	 *
	 * @var string 
	 */
	protected $adapter;

	/**
	 * Macros list
	 *
	 * @var  array
	 */
	protected $macros = array();

	/**
	 * Constructor.
	 *
	 * @param   object  $adapter  AdapterInterface
	 * @return  void
	 */
	public function __construct($adapter = 'Solr')
	{
		$adapterClass = "Hubzero\Search\Adapter\\".$adapter;
		$this->adapter = new $adapterClass;
	}

	/**
	 * Get the Adapter.
	 *
	 * @return  object  AdapterInterface
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * Set the Adapter.
	 *
	 * @param   object  $adapter  AdapterInterface
	 * @return  object
	 */
	public function setAdapter($adapter = 'Solr')
	{
		$adapterClass = "Hubzero\Search\Adapter\\".$adapter;
		$this->adapter = new $adapter;

		return $this;
	}

	public function test()
	{
		return $this->adapter->test();
	}

	public function getConfig()
	{
		return $this->adapter->getConfig();
	}

	public function status()
	{
		return $this->adapter->status();
	}
}
