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

/**
 * QueryInterface - Interface for Query Adapters
 * 
 */
interface QueryInterface
{
	/**
	 * getSuggestions  - Returns an array of suggested terms given terms
	 * 
	 * @param mixed $terms 
	 * @access public
	 * @return void
	 */
	public function getSuggestions($terms);

	/**
	 * query - Sets the query string
	 * 
	 * @param mixed $terms 
	 * @access public
	 * @return void
	 */
	public function query($terms);

	/**
	 * fields - Sets the fields to be returned by the query.
	 * 
	 * @param  array $fields 
	 * @access public
	 * @return void
	 */
	public function fields($fields);

	/**
	 * addFilter - Adds a filter to the query
	 * 
	 * @param mixed $name 
	 * @param array $query 
	 * @access public
	 * @return void
	 */
	public function addFilter($name, $query = array());

	/**
	 * addFacet - Adds a facet to the query object.
	 * 
	 * @param string $name - Used to identify facet when result is returned.
	 * @param array $query - The query array with a indexes of name, operator, and value 
	 * @access public
	 * @return void
	 */
	public function addFacet($name, $query = array());

	/**
	 * getFacetCount 
	 * 
	 * @param mixed $name - Returns an integer value of a defined facet.
	 * @access public
	 * @return void
	 */
	public function getFacetCount($name);

	/**
	 * limit - Set the number of results to be returned
	 * 
	 * @param int $limit 
	 * @access public
	 * @return void
	 */
	public function limit($limit);

	/**
	 * getResults  - Executes the query and returns an array of results.
	 * 
	 * @access public
	 * @return void
	 */
	public function getResults();

	/**
	 * getNumFound - Returns the total number of matching results, even outside of limit.
	 * 
	 * @access public
	 * @return void
	 */
	public function getNumFound();

	/**
	 * start - Offset of search index results. Warning: non-deterministic.
	 * 
	 * @param mixed $start 
	 * @access public
	 * @return void
	 */
	public function start($start);

	/**
	 * sortBy - Order results by a field in a given direction.
	 * 
	 * @param mixed $field  name of a field
	 * @param mixed $direction  (ASC or DESC)
	 * @access public
	 * @return void
	 */
	public function sortBy($field, $direction);

	/**
	 * run  - Performs the query, does not return results.
	 * 
	 * @access public
	 * @return void
	 */
	public function run();

	/**
	 * restrictAccess - Applies CMS permissions for the current user.
	 * 
	 * @access public
	 * @return void
	 */
	public function restrictAccess();

}
