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
 * Repository class for adding and removing composer packages
 **/
class Package extends Base implements CommandInterface
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
	 * @museDescription Shows a list of active packages
	 *
	 * @return  void
	 **/
	public function show()
	{
		$package = $this->arguments->getOpt(3) ? $this->arguments->getOpt(3) : $this->arguments->getOpt('package');
		if (!empty($package))
		{
			$versions = Composer::findRemotePackages($package, '*');
			$this->output->addRawFromAssocArray($versions);
		}
		else
		{
			$installed = Composer::getLocalPackages();
			$this->output->addRawFromAssocArray($installed);
		}
	}

	/**
	 * Show available packages
	 * 
	 * @museDescription Shows a list of available remote packages
	 * 
	 * @return void
	 **/
	public function available()
	{
		$package = $this->arguments->getOpt(3) ? $this->arguments->getOpt(3) : $this->arguments->getOpt('package');
		if (!empty($package))
		{
			$versions = Composer::findRemotePackages($package, '*');
			$this->output->addRawFromAssocArray($versions);
		}
		else
		{
			$available = Composer::getAvailablePackages();
			$this->output->addRawFromAssocArray($available);
		}
	}

	/**
	 * Add a package
	 * 
	 * @museDescription Installs a package
	 *
	 * @return  void
	 **/
	public function install()
	{
		if ($this->arguments->getOpt('package'))
		{
			$package = $this->arguments->getOpt('package');
		}
		if ($this->arguments->getOpt('version'))
		{
			$version = $this->arguments->getOpt('version');
		}
		if (!isset($package) || !isset($version))
		{
			$this->output->error('A package name and version is required');
		}

		try
		{
			Composer::installPackage($package, $version);
		}
		catch (Exception $e)
		{

		}
		$this->output->addLine("Done.  $package($version) installed.");
	}

	/**
	 * Update a package
	 * 
	 * @museDescription Updates a package according to version constraints
	 * 
	 * @return void
	 **/
	public function update()
	{
		$package = $this->arguments->getOpt(3);
		if (empty($package))
		{
			$this->output->error('A package name is required');
		}

		Composer::updatePackage($package);
		$this->output->addLine("Done. $package updated to latest version");
	}

	/**
	 * Remove a package
	 * 
	 * @museDescription Removes a package
	 *
	 * @return  void
	 **/
	public function remove()
	{
		$package = $this->arguments->getOpt(3);
		if (empty($package))
		{
			$this->output->error('A package name is required');
		}

		Composer::removePackage($package);
		$this->output->addLine("Done. $package has been removed.");
	}

	/**
	 * Shows help text for package command
	 *
	 * @return  void
	 **/
	public function help()
	{
		$this->output->addOverview('Add, remove, and update packages')
			->addTasks($this);
	}
}
