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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Console\Config;
use Hubzero\Search\Query;
use Hubzero\Search\Index;
use Hubzero\Utility\Sanitize;
use stdClass;

/**
 * Repository class
 **/
class Search extends Base implements CommandInterface
{
	/**
	 * Constructor - sets output mechanism and arguments for use by command
	 *
	 * @param   \Hubzero\Console\Output    $output     The ouput renderer
	 * @param   \Hubzero\Console\Arguments $arguments  The command arguments
	 * @return  void
	 **/
	public function __construct(Output $output, Arguments $arguments)
	{
		parent::__construct($output, $arguments);
	}

	/**
	 * Default (required) command - just run check command
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
	}

	/**
	 * Check the current status of the configured search engine 
	 *
	 * @museDescription  Checks the current status of the configured search engine
	 *
	 * @return  void
	 **/
	public function status()
	{
		$config = Component::params('com_search');
		$index = new Index($config);
		$status = $index->status();

		if ($status)
		{
			$this->output->addLine('Service is responding.');
		}
		else
		{
			$this->output->addLine('Service is NOT responding');
		}

		return $status;
	}

	/**
	 * Process the index queue
	 *
	 * @museDescription  Processes the index queue
	 *
	 * @return  void
	 **/
	public function processQueue()
	{
		require_once(PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'indexqueue.php');
		require_once(PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'noindex.php');

		// Get the type needed to be indexed;
		$item = \Components\Search\Models\IndexQueue::all()
			->where('complete', '=', 0)
			->order('created', 'ASC')
			->limit(1)
			->row();

		if ($item->action == 'index')
		{
			$this->processRows($item);
		}

	}

	private function processRows($item)
	{
		// Size of chunk
		// @TODO adjust the size of the chunk for delta-indexing
		$blocksize = 5000;

		$model = Event::trigger('search.onGetModel', $item->subject)[0];
		$total = $model->total();

		// Bail early
		if ($item->start > $total)
		{
			// Mark as complete
			$item->set('complete', 1);
			$item->save();

			return 1;
			exit();
		}

		$rows = $model::all()->start($item->start)->limit($blocksize);
		$mapping = Event::trigger('search.onGetMapping', $item->subject)[0];

		$config = Component::params('com_search');
		$index = new Index($config);

		// Process Rows
		foreach ($rows as $row)
		{
			// Instantiate a new Search Document
			$document = new stdClass;

			// Process the mapping
			// @TODO move this to class itself
			foreach ($mapping as $searchField => $dbField)
			{
				$dbField = ltrim($dbField, "{");
				$dbField = rtrim($dbField, "}");

				if (method_exists($row, 'get'))
				{
					$document->hubid = $row->get('id', 0);
					$data = $row->get($dbField, '');
				}
				else
				{
					$row = (object) $row;
					$data = isset($row->$dbField) ? $row->$dbField : '';
					if (isset($row->id))
					{
						$document->hubid = $row->id;
					}
				}

				// Scrub the input
				$data = Sanitize::clean($data);
				$data = Sanitize::stripAll($data);
				$data = Sanitize::stripTags($data);
				$data = strip_tags($data);

				// Match fields
				$document->$searchField = $data;
			}

			// Mandatory field
			$document->hubtype = $item->subject;
			$document->id = $item->subject . '-' . $row->id;

			// Build out URL for easy locating
			$path = Event::trigger('search.onBuildPath', array($item->subject, $row))[0];
			$document->url = $path;

			// Calculate Permission Set
			$permissionSet = Event::trigger('search.onCalculatePermissions', array($item->subject, $row))[0];

			$document->owner_type = $permissionSet->owner->owner_type;
			$document->owner = $permissionSet->owner->id;
			$document->access_level = $permissionSet->access;

			// Processed fields
			$processedFields = Event::trigger('search.onProcessFields', array($item->subject, $row))[0];

			foreach ($processedFields as $key => $value)
			{
				$document->$key = $value;
			}

			// Index the document
			$index->index($document);
		} // end foreach

		$item->set('start', $item->start + $blocksize);
		$item->save();
	} // end processRows()

	/**
	 * Output help documentation
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this
			->output
			->addOverview(
				'Search Engine Managment Services.'
			)
			->addTasks($this);
	}
}
