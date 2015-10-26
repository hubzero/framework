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

namespace Hubzero\Spam\Tests\Mock;

use Hubzero\Spam\Detector\Service;

/**
 * Mock spam detector
 */
class Detector extends Service
{
	/**
	 * {@inheritDocs}
	 */
	public function detect($data)
	{
		return false;
	}

	/**
	 * Train the service
	 *
	 * @param   string   $data
	 * @param   boolean  $isSpam
	 * @return  boolean
	 */
	public function learn($data, $isSpam)
	{
		if (!$data)
		{
			return false;
		}

		return true;
	}

	/**
	 * Forget a trained value
	 *
	 * @param   string   $data
	 * @param   boolean  $isSpam
	 * @return  boolean
	 */
	public function forget($data, $isSpam)
	{
		return true;
	}
}
