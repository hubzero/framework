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

/**
 * Filesystem adapter interface.
 */
interface AdapterInterface
{
	/*************************
	 * Administrative Methods
	 *************************/

	/**
	 * status - Checks the status of the service
	 * 
	 * @access public
	 * @return boolean true if the service is responding 
	 */
	public function status();

	/**
	 * getLog - Returns the logfile of the service
	 * 
	 * @access public
	 * @return string logfile contents 
	 */
	public function getLog();

	/**
	 * lastInsert - Returns the last indexing addition 
	 * 
	 * @access public
	 * @return void
	 */
	public function lastInsert();

	/***********************
	 * Query Operations
	 **********************/

	/**
	 * createQuery - Create a search query
	 * 
	 * @access public
	 * @return void
	 */
	public function createQuery();

	/**
	 * setQuery - Set the Query string
	 * 
	 * @param string $queryString 
	 * @access public
	 * @return void
	 */
	public function setQuery($queryString = '');

	/**
	 * addFacet - Add a facet to a Query
	 * 
	 * @param mixed $label 
	 * @param mixed $facetQueryString 
	 * @access public
	 * @return void
	 */
	public function addFacet($label, $facetQueryString);

	/**
	 * setFields - Set the fields to return from the Query
	 * 
	 * @param array $fields 
	 * @access public
	 * @return void
	 */
	public function setFields($fields = array());

	/**
	 * runQuery - Performs the Search Query
	 * 
	 * @access public
	 * @return void
	 */
	public function runQuery();

	/***********************
	 * Result Set Functions
	 **********************/

	/**
	 * limit - Limit the number of results
	 * 
	 * @param int $number 
	 * @access public
	 * @return void
	 */
	public function limit($number = 10);

	/**
	 * orderBy - Order the Results
	 * 
	 * @param mixed $field 
	 * @param mixed $subject
	 * @param mixed $direction 
	 * @access public
	 * @return void
	 */
	public function orderBy($field, $subject, $direction);

	/*****************************
	 * Indexing & Document Methods
	 ****************************/

	/**
	 * deleteById - Delete a document by ID 
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return void
	 */
	public function deleteById($id = NULL);

	/**
	 * addDocument - Add a document to the Index 
	 * 
	 * @param object Document $document 
	 * @param mixed $docID 
	 * @access public
	 * @return void
	 */
	public function addDocument($document, $docID = NULL);

}
