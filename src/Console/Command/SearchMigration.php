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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command;

use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Components\Search\Models\Solr\SearchComponent;

require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

/**
 * Migration class
 **/
class SearchMigration extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just executes run
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->run();
	}

	/**
	 * Run migration
	 *
	 * @museDescription  Adds components to solr index
	 *
	 * @return  void
	 **/
	public function run()
	{
		$newComponents = SearchComponent::getNewComponents();
		$newComponents->save();
		$components = SearchComponent::all();
		if (!$this->arguments->getOpt('all'))
		{
			$componentArgs = array();
			if ($this->arguments->getOpt('components'))
			{
				$componentArgs = explode(',', $this->arguments->getOpt('components'));
				$componentArgs = array_map('trim', $componentArgs);
			}

			if (empty($componentArgs))
			{
				$this->output->error('Error: No components specified.');
			}
			else
			{
				$components = $components->whereIn('name', $componentArgs);
			}
		}

		if (!$this->arguments->getOpt('rebuild'))
		{
			$components = $components->whereEquals('state', 0);
		}
		$url = $this->arguments->getOpt('url');
		if (empty($url))
		{
			$this->output->error('Error: no URL provided.');
		}
		foreach ($components as $compObj)
		{
			$offset = 0;
			$batchSize = $compObj->getBatchSize();
			$batchNum = 1;
			$compName = ucfirst($compObj->get('name'));
			$startMessage = 'Indexing ' . $compName . '...' . PHP_EOL;
			$this->output->addLine($startMessage);
			while ($indexResults = $compObj->indexSearchResults($offset, $url))
			{
				$batchMessage = 'Indexed ' . $compName . ' batch ' . $batchNum . ' of ' . $batchSize . PHP_EOL;
				$this->output->addLine($batchMessage);
				$offset = $indexResults['offset'];
				$batchNum++;
			}
			if ($compObj->state != 1)
			{
				$compObj->set('state', 1);
				$compObj->save();
			}
		}
	}

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
				'Run a solr search migration. Searches for and indexes models with the searcable interface.'
			)
			->addTasks($this)
			->addArgument(
				'-url: the base url of the site being indexed.',
				'Example: -url=\'https://localhost\''
			)
			->addArgument(
				'-components: component(s) that should be indexed.',
				'If multiple, separate each component name with a comma.',
				'Example: -components=\'blog, content, kb, resources\''
			)
			->addArgument(
				'--all: index all searchable components',
				'Any component that contains a model that implements Searchable will be added to the solr index.',
				'Example: --all'
			)
			->addArgument(
				'--rebuild: include components that have already had a full index run previously.',
				'this will overwrite any existing search documents with a new version.',
				'example: --rebuild'
			);
	}
}
