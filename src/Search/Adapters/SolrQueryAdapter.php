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

namespace Hubzero\Search\Adapters;

use Hubzero\Search\QueryInterface;
use Solarium;
use GuzzleHttp\Client;

class SolrQueryAdapter implements QueryInterface
{
	/**
	 * __construct 
	 * 
	 * @param mixed $config 
	 * @access public
	 * @return void
	 */
	public function __construct($config)
	{
		// Some setup information
		$core = $config->get('solr_core');
		$port = $config->get('solr_port');
		$host = $config->get('solr_host');
		$path = $config->get('solr_path');

		$this->logPath = $config->get('solr_log_path');

		// Build the Solr config object
		$solrConfig = array( 'endpoint' =>
			array( $core  =>
				array('host' => $host,
							'port' => $port,
							'path' => $path,
							'core' => $core,
							)
						)
					);

		// Create the client
		$this->connection = new Solarium\Client($solrConfig);

		// Make config accessible
		$this->config = $solrConfig;

		// Create the Solr Query object
		$this->query = $this->connection->createSelect();
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
	 * query 
	 * 
	 * @param mixed $terms 
	 * @access public
	 * @return void
	 */
	public function query($terms)
	{
		$this->query->setQuery($terms);
		return $this;
	}

	/**
	 * run 
	 * 
	 * @access public
	 * @return void
	 */
	public function run()
	{
		$this->resultset = $this->connection->execute($this->query);
		return $this->getResults();
	}

	/**
	 * getNumFound 
	 * 
	 * @access public
	 * @return void
	 */
	public function getNumFound()
	{
		return $this->numFound;
	}

	/**
	 * getFacetCount 
	 * 
	 * @param mixed $name 
	 * @access public
	 * @return void
	 */
	public function getFacetCount($name)
	{
		$count = $this->resultset->getFacetSet()->getFacet($name)->getValue();
		return $count;
	}

	/**
	 * addFacet 
	 * 
	 * @param mixed $name 
	 * @param array $query 
	 * @access public
	 * @return void
	 */
	public function addFacet($name, $query = array())
	{
		$this->facetSet = $this->query->getFacetSet();

		$string = $this->makeQueryString($query);
		$this->facetSet->createFacetQuery($name)->setQuery($string);

		return $this;
	}

	/**
	 * addFilter 
	 * 
	 * @param mixed $name 
	 * @param array $query 
	 * @access public
	 * @return void
	 */
	public function addFilter($name, $query = array())
	{
		$string = $this->makeQueryString($query);
		$this->query->createFilterQuery($name)->setQuery($string);
		return $this;
	}

	/**
	 * fields 
	 * 
	 * @param mixed $fieldArray 
	 * @access public
	 * @return void
	 */
	public function fields($fieldArray)
	{
		$this->query->setFields($fieldArray);
		return $this;
	}

	/**
	 * sortBy 
	 * 
	 * @param mixed $field 
	 * @param mixed $direction 
	 * @access public
	 * @return void
	 */
	public function sortBy($field, $direction)
	{
		$this->query->addSort($field, $direction);
		return $this;
	}

	/**
	 * limit 
	 * 
	 * @param mixed $limit 
	 * @access public
	 * @return void
	 */
	public function limit($limit)
	{
		$this->query->setRows($limit);
		return $this;
	}

	/**
	 * start 
	 * 
	 * @param mixed $offset 
	 * @access public
	 * @return void
	 */
	public function start($offset)
	{
		$this->query->setStart($offset);
		return $this;
	}

	/**
	 * restrictAccess 
	 * 
	 * @access public
	 * @return void
	 */
	public function restrictAccess()
	{
		if (User::isGuest())
		{
			$accessFilter = "(access_level:public)";
		}
		else
		{
			$user = User::get('id');
			$userGroups = \Hubzero\User\Helper::getGroups($user);

			$groupFilter = '(access_level:private AND owner_type:group AND (owner:';
			$i = 0;
			foreach ($userGroups as $group)
			{
				$groupFilter .= $group->gidNumber;
				if ($i >= count($userGroups) - 1)
				{
					$groupFilter .= '))';
				}
				else
				{
					$groupFilter .= ' ';
				}
				$i++;
			}

			$userFilter = '(access_level:private AND owner_type:user AND owner:' . $user . ')';

			$accessFilter = "(access_level:public) (access_level:registered)" . $userFilter . ' ' . $groupFilter;
		}

		$this->query->createFilterQuery('userPerms')->setQuery($accessFilter);
	}

	/**
	 * getResults 
	 * 
	 * @access public
	 * @return void
	 */
	public function getResults()
	{
		if (!isset($this->resultset))
		{
			$this->run();
		}

		$documents = array();
		foreach($this->resultset as $document)
		{
			array_push($documents, $document);
		}

		return $documents;
	}

	/**
	 * makeQueryString 
	 * 
	 * @param array $query 
	 * @access private
	 * @return void
	 */
	private function makeQueryString($query = array())
	{
		$subject = $query[0];
		$operator = $query[1];
		$operand = $query[2];

		switch ($operator)
		{
			case '=':
				$string = $subject . ':' . $operand;
			break;
		}

		return $string;
	}


	/**
	 * lastInsert - Returns the timestamp of the latest indexed document
	 *
	 * @access public
	 * @return void
	 */
	public function lastInsert()
	{
		$query = $this->connection->createSelect();
		$query->setQuery('*:*');
		$query->setFields(array('timestamp'));
		$query->addSort('timestamp', 'DESC');
		$query->setRows(1);
		$query->setStart(0);

		$results = $this->connection->execute($query);
		foreach ($results as $document)
		{
			foreach ($document as $field => $value)
			{
				$result = $value;
				return $result;
			}
		}
	}
}
