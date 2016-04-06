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
use Hubzero\User\Group;


/**
 * Hubzero class for performing Search and Indexing Operations.
 */
class Query extends \Hubzero\Base\Object
{
	protected $facets = array();
	protected $queryString;
	protected $formatOptions = array();
	protected $filters = array();
	protected $fields = array();
	protected $limit = 15;
	protected $offset = 0;

	public function setQueryString($string)
	{
		$this->set('queryString', $string);
		return $this;
	}

	/**
	 * addFacet - Add a facet to the search
	 *
	 * @param mixed $label
	 * @param mixed $facetQueryString
	 * @access public
	 * @return void
	 */
	public function addFacet($label, $field, $operator, $value)
	{
		array_push($this->facets, array('label' => $label, 'field' => $field, 'operator' => $operator, 'value' => $value));
		return $this;
	}

	/**
	 * setFields - Set the fields returned in the result
	 *
	 * @param array $fields
	 * @access public
	 * @return void
	 * @todo Test
	 */
	public function setFields($fields = array())
	{
		if (!isset($this->fields))
		{
			$this->fields = array();
		}
		if (is_array($fields))
		{
			$this->fields = array_unique(array_merge($this->fields, $fields));
		}
		elseif (is_string($fields))
		{
			if (strpos("," , $fields) === FALSE)
			{
				$this->fields = array($fields);
			}
			else
			{
				$this->fields = explode("," , $fields);
			}
		}
		return $this;
	}

	/**
	 * addFilter
	 *
	 * @param mixed $name 
	 * @param mixed $operator 
	 * @param mixed $value 
	 * @access public
	 * @return void
	 */
	public function addFilter($name, $field, $operator, $value)
	{
		$filter = array('name' => $name, 'field' => $field, 'operator' => $operator, 'value' => $value);
		array_push($this->filters, $filter);
		return $this;
	}

	/**
	 * addRawFilter 
	 * 
	 * @param mixed $name 
	 * @param mixed $string 
	 * @fixme solr specific
	 * @access public
	 * @return void
	 */
	public function addRawFilter($name, $string)
	{
		$this->rawFilter = array('name' => $name, 'rawString' => $string);
		return $this;
	}

	/**
	 * limit - Limits the number of results returned from the query
	 *
	 * @param int $number
	 * @access public
	 * @return void
	 */
	public function limit($limit, $offset)
	{
		$this->set('limit', $limit);
		$this->set('offset', $offset);
		return $this;
	}

	/**
	 * orderBy - Adds 'order' as a query parameter
	 *
	 * @param mixed $field
	 * @param mixed $subject
	 * @param mixed $direction
	 * @access public
	 * @return void
	 */
	public function orderBy($field, $direction)
	{
		$this->orderBy = array('field'=> $field, 'direction'=> $direction);
		return $this;
	}

}

