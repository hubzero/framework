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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Spam\Tests\Mock;

use Hubzero\Spam\Detector\Service;
use Exception;

/**
 * Mock spam detector that throws an exception
 *
 * @codeCoverageIgnore
 */
class DetectorException extends Service
{
	/**
	 * Run content through spam detection
	 *
	 * @codeCoverageIgnore
	 * @param   array  $data
	 * @return  bool
	 * @throws  Exception
	 */
	public function detect($data)
	{
		if (!is_array($data) || !isset($data['text']))
		{
			return false;
		}

		throw new Exception('I always throw an exception.');
	}
}
