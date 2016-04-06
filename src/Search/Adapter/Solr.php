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

namespace Hubzero\Search\Adapter;

use Hubzero\Search\Search;
use Hubzero\Search\Query;
use Hubzero\Search\SearchResult as Result;
use Hubzero\Search\SearchDocument as Document;
use Hubzero\Filesystem;
use Hubzero\Error;
use Solarium;

/**
 * Hubzero class for manipulating and reading the filesystem.
 */
class Solr implements Search 
{

	/**
	 * name - The name of the Adapter
	 *
	 * @var string
	 * @access protected
	 */
	protected $name = 'Solr';

	/**
	 * Constructor.
	 *
	 * @param   string  $command
	 * @return  void
	 */
	public function __construct()
	{
		// Establish a connection
		$this->connect();
	}

	/**
	 * status  - Detemines whether the search service is responsive.
	 *
	 * @access public
	 * @return void
	 */
	public function status()
	{
		$ping = $this->connection->createPing();
		try
		{
			$ping = $this->connection->ping($ping);
			$pong = $ping->getData();
			$alive = false;

			if (isset($pong['status']) && $pong['status'] === "OK")
			{
				$alive = true;
			}
		}
		catch (\Solarium\Exception $e)
		{
			return false;
		}

		return $alive;
	}

	/**
	 * getConfig - Loads the configuration file, creates dummy if not present
	 *
	 * @access public
	 * @return void
	 */
	public function getConfig()
	{
		/**
		 * Configuration
		 * in the /app/config/search/<adapter>/config.php directory
		 **/
		$searchDir = PATH_APP . DS . 'config' . DS . 'search' . DS . $this->name;
		$configSample = dirname(__DIR__) . DS . 'samples' . DS . strtolower($this->name) . '.config.php.sample';

		if (Filesystem::isFile($searchDir . DS . 'config.php'))
		{
			// Load configuration
			try
			{
				$this->config = require $searchDir . DS . 'config.php';
			}
			catch (FatalError $e) // Cannot catch a fatal error
			{
				// Warn the user to check the configuration file.
			}

			return $this;
		}
		else
		{
			// Create the configuration file.
			if (!Filesystem::exists($searchDir))
			{
				// Create the Adapter Config Directory
				Filesystem::makeDirectory($searchDir);
			}

			// Create the file
			if (!Filesystem::isFile($searchDir . DS . 'config.php'))
			{
				if (!Filesystem::isFile($configSample))
				{
					throw new \Exception(Lang::txt('Unable to load the sample configuration file.'));
				}
				else
				{
					Filesystem::copy($configSample, $searchDir. DS . 'config.php');
				}
			}

			// Load the configuration file, after creation
			if (Filesystem::isFile($searchDir . DS . 'config.php'))
			{
				// Load configuration
				try
				{
					$this->config = require_once $searchDir . DS . 'config.php';
				}
				catch (\Exception $e)
				{
					// Warn to check configuration file.
					ddie($e);
				}

				return $this;
			}
		}
		return false;
	}

	/**
	 * connect - Instantiates a connection to Solr using
	 * a provided configuration
	 *
	 * @access public
	 * @return void
	 */
	public function connect()
	{
		// Get the configuration
		$this->getConfig();

		// Open a new Solarium client
		$this->connection = new Solarium\Client($this->config);

		return $this;
	}

	/**********************
	 * Query Operations
	 *********************/

	/**
	 * setQuery - Create the query object and set the main string
	 *
	 * @param mixed $queryString
	 * @access public
	 * @return void
	 */
	public function query($query)
	{
		$queryString = $query->get('queryString');

		// Assume searching everything
		if ($queryString == '')
		{
			// Perhaps check for filters?
			$queryString = '*:*';
		}

		if ($queryString != '')
		{
			// Set the query terms
			$this->query = $this->connection->createSelect();
			$this->queryString = $queryString;
			$this->query->setQuery($queryString);

			// Add facets
			if (count($query->get('facets')) > 0)
			{
				$facets = $query->get('facets');
				foreach ($facets as $facet)
				{
					$this->addFacet($facet);
				}
			}

			// Raw filter
			$rawFilter = $query->get('rawFilter');
			if (isset($rawFilter))
			{
				$this->query->createFilterQuery($rawFilter['name'])->setQuery($rawFilter['rawString']);
			}

			// Apply any filters
			$filters = $query->get('filters');
			foreach ($filters as $filter)
			{
				$parsedQueryString = $this->parseQuery($filter);
				$this->query->createFilterQuery($filter['name'])->setQuery($parsedQueryString);
			}

			// Apply any limits
			$limit = $query->get('limit');
			$offset = $query->get('offset');
			if (isset($limit))
			{
				$this->limit($limit, $offset);
			}

			// Apply an order
			$order = $query->get('orderBy');
			if (isset($order))
			{
				$this->orderBy($order['field'], $order['direction']);
			}

			// Apply Fields
			$fields = $query->get('fields');
			if (isset($fields))
			{
				$this->setFields($fields);
			}

			// Setup Spellcheck
			$this->spellcheck($queryString);

			return $this;
		}
		else
		{
			return false;
		}
	}

	public function spellcheck($queryString)
	{
		$this->spellcheck = $this->query->getSpellcheck();
		$this->spellcheck->setQuery($queryString);
		$this->spellcheck->setCount(10);
		$this->spellcheck->setBuild(true);
		$this->spellcheck->setCollate(true);

		//ddie($this->spellcheck);

		return $this;
	}

	/**
	 * addFacet - Add a facet to the search
	 *
	 * @param array $facet
	 * @access public
	 * @return void
	 */
	public function addFacet($facet)
	{
		// Brand new clean string
		$string = '';

		// Apply the field
		$string .= $facet['field'];

		// Apply the operator
		switch ($facet['operator'])
		{
			case '=':
				$string .= ':';
				$string .= $facet['value'];
			break;
			default:
				$string .= ':';
				$string .= $facet['value'];
			break;
		}

		$this->facetSet = $this->query->getFacetSet();
		$this->facetSet->createFacetQuery($facet['label'])->setQuery($string);
		return $this;
	}

	/**
	 * getFacetCount  - Get the number of items matching a given facet
	 *
	 * @param mixed $label
	 * @access public
	 * @return void
	 */
	public function getFacetCount($label)
	{
		$count = $this->result->getFacetSet()->getFacet($label)->getValue();
		return $count;
	}

	/**
	 * setFields - Set the fields returned in the result
	 *
	 * @param array $fields
	 * @access public
	 * @return void
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
			$this->query->setFields($this->fields);
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
			$this->query->setFields($this->fields);
		}
		return $this;
	}

	/**
	 * runQuery - Execute the query
	 *
	 * @access public
	 * @return void
	 */
	public function runQuery()
	{
		$this->result = $this->connection->select($this->query);

		// Perform spell-checking
		$this->spellResults = $this->result->getSpellcheck();
		if ($this->spellResults)
		{
			// Create an empty array, to hold suggestions
			$this->spellings = array();

			// Extract the words
			foreach ($this->spellResults as $spell)
			{
				foreach ($spell->getWords() as $suggestion)
				{
					// @TODO make smarter by including frequencies for later processing
					array_push($this->spellings, $suggestion['word']);
				}
			}
		}

		return $this;
	}

	/**
	 * getResult - Returns documents matching the query
	 *
	 * @access public
	 * @return void
	 */
	public function getResult()
	{
		$this->runQuery();

		$documents = array();
		foreach ($this->result->getDocuments() as $document)
		{
			$searchDocument = new Document;
			$searchDocument->normalize($document->getFields('fields'));
			array_push($documents, $searchDocument);
		}

		return $documents;
	}

	/**
	 * limit - Limits the number of results returned from the query
	 *
	 * @param int $number
	 * @access public
	 * @return void
	 */
	public function limit($limit = 10, $offset = 0)
	{
		$this->query->setRows($limit);
		$this->query->setStart($offset);

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
		$this->query->addSort($field, $direction);
		return $this;
	}

	/**
	 * lastInsert - Returns the timestamp of the latest indexed document
	 *
	 * @access public
	 * @return void
	 */
	public function lastInsert()
	{
		$this->connection;

		$query = new Query;
		$query->setQueryString('*:*');
		$query->setFields(array('timestamp'));
		$query->orderBy('timestamp', 'DESC');
		$query->limit(1,0);

		$result = $this->query($query)->getResult();

		if (isset($result[0]))
		{
			return $result[0]->timestamp;
		}
		else
		{
			return false;
		}
	}

	/* Update */
	/**
	 * deleteById - Removes a single document from the search index
	 *
	 * @param mixed $id
	 * @access public
	 * @return void
	 */
	public function deleteById($id = NULL)
	{
		// @FIXME Perhaps consider using addDeleteById(1234)?

		if ($id != NULL)
		{
			$update = $this->connection->createUpdate();
			$update->addDeleteQuery('id:'.$id);
			$update->addCommit();
			$response = $this->connection->update($update);

			// @FIXME: logical fallicy
			// Wild assumption that the update was successful
			return TRUE;
		}
		else
		{
			return Lang::txt('No record specified.');
		}
	}

	/**
	 * index - Adds a document to the search engine index
	 *
	 * @param mixed $document
	 * @access public
	 * @return void
	 */
	public function index($document)
	{
			// Instantiate an update object
			$update = $this->connection->createUpdate();

			// Create the document for updating
			$solrDoc = $update->createDocument();

			// Iterate through and set the appropriate fields
			foreach ($document as $key => $value)
			{
				$solrDoc->$key = $value;
			}

			// Generate a unique ID, hopefully
			$solrDoc->id = hash('md5', time()*rand());

			// Add the document to the update
			$update->addDocuments(array($solrDoc));

			// Create a commit
			$update->addCommit();

			// Run the update query
			if ($this->connection->update($update))
			{
				return true;
			}
			else
			{
				return false;
			}
	}

	/**
	 * updateIndex - Updates a document existing in the search index
	 *
	 * @param mixed $document
	 * @param mixed $id
	 * @access public
	 * @return void
	 */
	public function updateIndex($document, $id)
	{
			$update = $this->connection->createUpdate();

			$solrDoc = $update->createDocument();

			foreach ($document as $key => $value)
			{
				$solrDoc->$key = $value;
			}

			$solrDoc->id = $id;

			$update->addDocuments(array($solrDoc));
			$update->addCommit();
			$this->connection->update($update);

			return true;
	}

	/**
	 * getLog - Returns a plaintext log for administrative purposes
	 *
	 * @access public
	 * @return void
	 */
	public function getLog()
	{
		$log = Filesystem::read($this->config['endpoint']['localhost']['log_path']);
		$levels = array();
		$this->logs = explode("\n",$log);

		return $this->logs;
	}

	private function parseQuery($query)
	{
		switch ($query['operator'])
		{
			case '=':
				$string = $query['field'] . ':' . $query['value'];
			break;
		}
		return $string;
	}
}
