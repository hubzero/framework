<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Browser\Tests\Fixtures;

/**
 * User Agent String
 */
class UserAgentString
{
	/**
	 * @var  string
	 */
	private $browser;

	/**
	 * @var  string
	 */
	private $browserVersion;

	/**
	 * @var  string
	 */
	private $os;

	/**
	 * @var  string
	 */
	private $osVersion;

	/**
	 * @var  string
	 */
	private $device;

	/**
	 * @var  string
	 */
	private $deviceVersion;

	/**
	 * @var  string
	 */
	private $string;

	/**
	 * @return  string
	 */
	public function getBrowser()
	{
		return $this->browser;
	}

	/**
	 * @param   string  $browser
	 * @return  object  $this
	 */
	public function setBrowser($browser)
	{
		$this->browser = $browser;

		return $this;
	}

	/**
	 * @return  string
	 */
	public function getOs()
	{
		return $this->os;
	}

	/**
	 * @param   string  $os
	 * @return  object  $this
	 */
	public function setOs($os)
	{
		$this->os = $os;

		return $this;
	}

	/**
	 * @return  string
	 */
	public function getosVersion()
	{
		return $this->osVersion;
	}

	/**
	 * @param   string  $osVersion
	 * @return  object  $this
	 */
	public function setosVersion($osVersion)
	{
		$this->osVersion = $osVersion;

		return $this;
	}

	/**
	 * @return  string
	 */
	public function getString()
	{
		return $this->string;
	}

	/**
	 * @param   string  $string
	 * @return  object  $this
	 */
	public function setString($string)
	{
		$this->string = $string;

		return $this;
	}

	/**
	 * @return  string
	 */
	public function getbrowserVersion()
	{
		return $this->browserVersion;
	}

	/**
	 * @param   string  $browserVersion
	 * @return  object  $this
	 */
	public function setbrowserVersion($browserVersion)
	{
		$this->browserVersion = $browserVersion;

		return $this;
	}

	/**
	 * @return  string
	 */
	public function getDevice()
	{
		return $this->device;
	}

	/**
	 * @param   string  $device
	 * @return  object  $this
	 */
	public function setDevice($device)
	{
		$this->device = $device;

		return $this;
	}

	/**
	 * @return  string
	 */
	public function getDeviceVersion()
	{
		return $this->deviceVersion;
	}

	/**
	 * @param   string  $deviceVersion
	 * @return  object  $this
	 */
	public function setDeviceVersion($deviceVersion)
	{
		$this->deviceVersion = $deviceVersion;

		return $this;
	}
}
