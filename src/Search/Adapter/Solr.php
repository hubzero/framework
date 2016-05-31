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

use Hubzero\Search\AdapterInterface;
use Solarium;

/**
 * Hubzero class for manipulating and reading the filesystem.
 */
class Solr implements AdapterInterface
{

	/**
	 * Constructor.
	 *
	 * @param   string  $command
	 * @return  void
	 */
	public function __construct()
	{
	}

	public function status()
	{
		return $this->ping();
	}

	public function createQuery()
	{
	}



	public function getConfig()
	{
		$config = array('endpoint' => array(
							'localhost' => array(
							'host' => '127.0.1.1',
							'port' =>8983 ,
							'path' => '/solr/hubsearch',
							'log_path' => '/opt/solr/server/logs/solr.log')));

		return $config;
	}

	public function start()
	{
		$cmd = "/opt/solr/bin/solr start -s /srv/example/solr/data/";
		shell_exec($cmd);
	}

	public function ping()
	{
		$config = $this->getConfig();
		$solr = new Solarium\Client($config);
		$ping = $solr->createPing();
		try
		{
			$ping = $solr->ping($ping);
			$ping = $ping->getData();
			$alive = false;

			if (isset($ping['status']) && $ping['status'] === "OK")
			{
				$alive = true;
			}
		} catch (\Solarium\Exception $e) {
			return false;
		}
		return $alive;
	}

	/** Query Operations **/
	// Create query object
	public function setQuery($queryString = '')
	{
		$config = $this->getConfig();
		$this->solr = new Solarium\Client($config);
		$this->query = $this->solr->createSelect();
		$this->queryString = $queryString;
		$this->query->setQuery($queryString);
		return $this;
	}
	public function addFacet($label, $facetQueryString)
	{
		$this->facetSet = $this->query->getFacetSet();
		$this->facetSet->createFacetQuery($label)->setQuery($facetQueryString);
		return $this;
	}
	public function getFacetCount($label)
	{
		$count = $this->result->getFacetSet()->getFacet($label)->getValue();
		return $count;
	}
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
	}
	public function runQuery()
	{
		$this->result = $this->solr->select($this->query);
		return $this->result;
	}
	public function limit($number = 10)
	{
		$this->query->setRows($number);
		return $this;
	}
	public function orderBy($field, $subject, $direction)
	{
		$this->query->addSort($field, $direction);
		return $this;
	}
	public function lastInsert()
	{
		$this->search('*:*');
		$this->setFields('timestamp');
		$this->limit(1);
		$this->orderBy('timestamp', 'desc');
		$result = $this->getResult();
		if (isset($result->getDocuments()[0]))
		{
			return $result->getDocuments()[0]->getFields()['timestamp'];
		}
		else
		{
			return false;
		}
	}
	/* Update */
	public function deleteById($id = NULL)
	{
		// @FIXME Perhaps consider using addDeleteById(1234)?
		$config = $this->getConfig();
		$this->solr = new Solarium\Client($config);

		if ($id != NULL)
		{
			$update = $this->solr->createUpdate();
			$update->addDeleteQuery('id:'.$id);
			$update->addCommit();
			$response = $this->solr->update($update);

			// @FIXME: logical fallicy
			// Wild assumption that the update was successful
			return TRUE;
		}
		else
		{
			return Lang::txt('No record specified.');
		}
	}
	public function addDocument($document, $docID = NULL)
	{
			$config = $this->getConfig();
			$this->solr = new Solarium\Client($config);

			$update = $this->solr->createUpdate();
			$solrDoc = $update->createDocument();
			foreach ($document as $key => $value)
			{
				$solrDoc->$key = $value;
			}
			if ($docID == NULL)
			{
				$solrDoc->id = hash('md5', time()*rand());
			}
			else
			{
				$solrDoc->id = $docID;
			}
			$update->addDocuments(array($solrDoc));
			$update->addCommit();
			$this->solr->update($update);

			return true;
	}

	/* Administrative & Mantainace */
	//public function cleanup($a
	public function getLog()
	{
		$config = $this->getConfig();
		$log = Filesystem::read($config['endpoint']['localhost']['log_path']);
		$levels = array();
		$this->logs = explode("\n",$log);

		return $this;
	}
}
