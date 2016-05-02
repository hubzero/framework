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

class SolrQueryAdapter implements QueryInterface
{
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

		// Create the Solr Query object
		$this->query = $this->connection->createSelect();
	}

	public function getSuggestions($terms)
	{
		$this->suggester = $this->connection->createSuggester();
		$this->suggester->setQuery($terms);
		$this->suggester->setCount(10);
		$this->suggester->setOnlyMorePopular(true);
		$this->suggester->setCollate(true);
		$suggestions = array();

		$resultset = $this->connection->suggester($this->suggester);

		$dicts = (array) json_decode($resultset->getResponse()->getBody())->suggest;

		foreach ($dicts as $dict)
		{
			if ($dict->$terms->numFound > 0)
			{
				foreach ($dict->$terms->suggestions as $suggest)
				{
					array_push($suggestions, $suggest->term);
				}
			}
		}
		return $suggestions;
	}

	public function query($terms)
	{
		$this->query->setQuery($terms);
		return $this;
	}

	public function run()
	{
		$this->resultset = $this->connection->execute($this->query);
		return $this->getResults();
	}

	public function getNumFound()
	{
		return $this->numFound;
	}

	public function getFacetCount($name)
	{
		$count = $this->resultset->getFacetSet()->getFacet($name)->getValue();
		return $count;
	}

	public function addFacet($name, $query = array())
	{
		$this->facetSet = $this->query->getFacetSet();

		$string = $this->makeQueryString($query);
		$this->facetSet->createFacetQuery($name)->setQuery($string);

		return $this;
	}

	public function addFilter($name, $query = array())
	{
		$string = $this->makeQueryString($query);
		$this->query->createFilterQuery($name)->setQuery($string);
		return $this;
	}

	public function fields($fieldArray)
	{
		$this->query->setFields($fieldArray);
		return $this;
	}

	public function sortBy($field, $direction)
	{
		$this->query->addSort($field, $direction);
		return $this;
	}

	public function limit($limit)
	{
		$this->query->setRows($limit);
		return $this;
	}

	public function start($offset)
	{
		$this->query->setStart($offset);
		return $this;
	}

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
