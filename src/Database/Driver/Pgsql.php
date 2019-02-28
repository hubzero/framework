<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2019 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since     Class available since release 2.2.14
 */

namespace Hubzero\Database\Driver;

use Hubzero\Database\Driver\Pdo as PdoDriver;
use Hubzero\Database\Exception\ConnectionFailedException;
use Hubzero\Database\Exception\QueryFailedException;

/**
 * Postgres (Pdo) database driver
 */
class Pgsql extends PdoDriver
{
	/**
	 * Constructs a new database object based on the given params
	 *
	 * @param   array  $options  The database connection params
	 * @return  void
	 */
	public function __construct($options)
	{
		// Add "extra" options as needed
		if (!isset($options['extras']))
		{
			$options['extras'] = [];
		}

		// Establish connection string
		if (!isset($options['dsn']))
		{
			$host = isset($options['host']) && $options['host'] ? "host={$options['host']};" : '';

			$options['dsn'] = "pgsql:{$host}dbname={$options['database']}";

			if (isset($options['port']))
			{
				$options['dsn'] .= ";port={$options['port']}";
			}

			foreach (['sslmode', 'sslcert', 'ssl_ca', 'sslkey', 'sslrootcert'] as $option)
			{
				if (isset($options[$option]))
				{
					$key = ($option == 'ssl_ca' ? 'sslcert' : $option);
					$val = $options[$option];

					$options['dsn'] .= ";{$key}={$val}";
				}
			}
		}

		if (substr($options['dsn'], 0, 7) != 'pgsql:')
		{
			throw new ConnectionFailedException('Postgres DSN for PDO connection does not appear to be valid.', 500);
		}

		// Call parent construct
		parent::__construct($options);
	}
}
