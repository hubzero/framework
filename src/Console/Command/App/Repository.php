<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @author    Zach Weidner <zweidner@purdue.edu>
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\App;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Hubzero\Utility\Composer;

/**
 * Repository class for adding and removing composer package repositories
 **/
class Repository extends Base implements CommandInterface
{
	/**
	 * Default (required) command - just call help
	 *
	 * @return  void
	 **/
	public function execute()
	{
		$this->output = $this->output->getHelpOutput();
		$this->help();
		$this->output->render();
		return;
	}

	/**
	 * Show packages
	 * 
	 * @museDescription Shows a list of active repositories
	 *
	 * @return  void
	 **/
	public function show()
	{
		$repositories = Composer::getRepositoryConfigs();
		$this->output->addRawFromAssocArray($repositories);
	}

	/**
	 * Add a repository
	 * 
	 * @museDescription Adds a repository
	 *
	 * @return  void
	 **/
	public function add()
	{
		//Add via composer.json for now
	}

	/**
	 * Remove a repository
	 * 
	 * @museDescription Removes a repository
	 *
	 * @return  void
	 **/
	public function remove()
	{
		//Remove via composer.json for now
	}

	/**
	 * Shows help text for repository command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output->addOverview('Add, remove, and update repositories for packages')
			->addTasks($this);
	}
}
