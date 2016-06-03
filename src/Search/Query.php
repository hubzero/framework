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

	namespace Hubzero\Search;

	use Hubzero\Search\Adapters;

class Query
{
	public function __construct($config)
	{
		$engine = $config->get('engine');
		$adapter = "\\Hubzero\\Search\\Adapters\\" . ucfirst($engine) . 'QueryAdapter';
		$this->adapter = new $adapter($config);
		return $this;
	}

	public function getSuggestions($terms)
	{
		$suggestions = $this->adapter->getSuggestions($terms);
		return $suggestions;
	}

	public function query($terms)
	{
		$this->adapter->query($terms);
		return $this;
	}

	public function fields($fields)
	{
		$this->adapter->fields($fields);
		return $this;
	}

	public function addFilter($name, $query = array())
	{
		$this->adapter->addFilter($name, $query);
		return $this;
	}
	public function addFacet($name, $query = array())
	{
		$this->adapter->addFacet($name, $query);
		return $this;
	}

	public function getFacetCount($name)
	{
		return $this->adapter->getFacetCount($name);
	}

	public function limit($limit)
	{
		$this->adapter->limit($limit);
		return $this;
	}

	public function getResults()
	{
		return $this->adapter->getResults();
	}

	public function getNumFound()
	{
		return $this->adapter->getNumFound();
	}

	public function start($start)
	{
		$this->adapter->start($start);
		return $this;
	}

	public function sortBy($field, $direction)
	{
		$this->adapter->sortBy($field, $direction);
		return $this;
	}

	public function run()
	{
		return $this->adapter->run();
	}

	public function restrictAccess()
	{
		$this->adapter->restrictAccess();
		return $this;
	}

