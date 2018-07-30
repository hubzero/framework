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

/**
 * Query - Class to provide search engine query functionality
 *
 */
class Query
{
	/**
	 * __construct - Set the adapter
	 * 
	 * @param mixed $config - Configuration object
	 * @access public
	 * @return void
	 */
	public function __construct($config)
	{
		$engine = $config->get('engine');
		$adapter = "\\Hubzero\\Search\\Adapters\\" . ucfirst($engine) . 'QueryAdapter';
		$this->adapter = new $adapter($config);
		return $this;
	}

	/**
	 * Get MoreLikeThis
	 *
	 * @access public
	 * @return SolariumQuery
	 */
	public function getMoreLikeThis($terms)
	{
		// Get morelikethis settings
		$mltQuery = $this->connection->createSelect();
		$mltQuery->setQuery($terms)
			->getMoreLikeThis()
			->setFields('text');
		// Executes the query and returns the result
		$resultSet = $this->connection->select($mltQuery);
		$mlt = $resultSet->getMoreLikeThis();
		return $resultSet;
	}

	/**
	 * spellCheck Returns terms suggestions
	 *
	 * @param mixed $terms
	 * @access public
	 * @return dictionary
	 */
	public function spellCheck($terms)
	{
		// Set the spellCheck Query
		$scQuery = $this->connection->createSelect();
		$scQuery->setRows(0)
				->getSpellcheck()
				->setQuery($terms)
				->setCount('5');
		// This executes the query and returns the result
		$spellcheckResults = $this->connection->select($scQuery)->getSpellcheck();
		return $spellcheckResults;
	}

	/**
	 * getSuggestions Returns indexed terms
	 *
	 * @param mixed $terms
	 * @access public
	 * @return array
	 */
	public function getSuggestions($terms)
	{
		// Rewrite for easier keyboard typing
		$config = $this->config['endpoint']['hubsearch'];
		// Create the base URL
		$url = rtrim(Request::Root(), '/\\');
		// Use the correct port
		$url .= ':' . $config['port'];
		// Use the correct core
		$url .= '/solr/' . $config['core'];
		// Perform a select operation
		$url .= '/select?fl=id';
		// Derive user permission filters
		$this->restrictAccess();
		$userPerms = $this->query->getFilterQuery('userPerms')->getQuery();
		$url .= '&fq=' . $userPerms;
		// Limit rows, not interested in results, just facets
		$url .= '&rows=0';
		// Select all, honestly doesn't matter
		$url .= '&q=*:*';
		// Enable Facets, set the mandatory field
		$url .= '&facet=true&facet.field=author_auto&facet.field=tags_auto&facet.field=title_auto';
		// Set the minimum count, could tweak to only most popular things
		$url .= '&facet.mincount=1';
		//  The actual searching part
		$url .= '&facet.prefix=' . strtolower($terms);
		// Make it JSON
		$url .= '&wt=json';
		$client = new \GuzzleHttp\Client();
		$res = $client->get($url);
		$resultSet = $res->json()['facet_counts']['facet_fields'];
		$suggestions = array();
		foreach ($resultSet as $results)
		{
			$x = 0;
			foreach ($results as $i => $result)
			{
				if ($i % 2 == 0)
				{
					// Prevents too many results from being suggested
					if ($x >= 10)
					{
						break;
					}
					array_push( $suggestions, $result);
					$x++;
				}
			}
		}
		return $suggestions;
	}

	/**
	 * query - Sets the query string
	 * 
	 * @param mixed $terms 
	 * @access public
	 * @return void
	 */
	public function query($terms)
	{
		$this->adapter->query($terms);
		return $this;
	}

	/**
	 * fields - Sets the fields to be returned by the query.
	 * 
	 * @param  array $fields 
	 * @access public
	 * @return void
	 */
	public function fields($fields)
	{
		$this->adapter->fields($fields);
		return $this;
	}

	/**
	 * addFilter - Adds a filter to the query
	 * 
	 * @param mixed $name 
	 * @param array $query 
	 * @access public
	 * @return void
	 */
	public function addFilter($name, $query = array(), $tag = 'root_type')
	{
		$this->adapter->addFilter($name, $query, $tag);
		return $this;
	}

	/**
	 * addFacet - Adds a facet to the query object.
	 * 
	 * @param string $name - Used to identify facet when result is returned.
	 * @param array $query - The query array with a indexes of name, operator, and value 
	 * @access public
	 * @return void
	 */
	public function addFacet($name, $query = array())
	{
		$this->adapter->addFacet($name, $query);
		return $this;
	}

	/**
	 * getFacetCount 
	 * 
	 * @param mixed $name - Returns an integer value of a defined facet.
	 * @access public
	 * @return void
	 */
	public function getFacetCount($name)
	{
		return $this->adapter->getFacetCount($name);
	}

	/**
	 * limit - Set the number of results to be returned
	 * 
	 * @param int $limit 
	 * @access public
	 * @return void
	 */
	public function limit($limit)
	{
		$this->adapter->limit($limit);
		return $this;
	}

	/**
	 * getResults  - Executes the query and returns an array of results.
	 * 
	 * @access public
	 * @return void
	 */
	public function getResults()
	{
		return $this->adapter->getResults();
	}

	/**
	 * getNumFound - Returns the total number of matching results, even outside of limit.
	 * 
	 * @access public
	 * @return void
	 */
	public function getNumFound()
	{
		return $this->adapter->getNumFound();
	}

	/**
	 * start - Offset of search index results. Warning: non-deterministic.
	 * 
	 * @param mixed $start 
	 * @access public
	 * @return void
	 */
	public function start($start)
	{
		$this->adapter->start($start);
		return $this;
	}

	/**
	 * sortBy - Order results by a field in a given direction.
	 * 
	 * @param mixed $field  name of a field
	 * @param mixed $direction  (ASC or DESC)
	 * @access public
	 * @return void
	 */
	public function sortBy($field, $direction)
	{
		$this->adapter->sortBy($field, $direction);
		return $this;
	}

	/**
	 * run  - Performs the query, does not return results.
	 * 
	 * @access public
	 * @return void
	 */
	public function run()
	{
		return $this->adapter->run();
	}

	/**
	 * restrictAccess - Applies CMS permissions for the current user.
	 * 
	 * @access public
	 * @return void
	 */
	public function restrictAccess()
	{
		$this->adapter->restrictAccess();
		return $this;
	}
}
