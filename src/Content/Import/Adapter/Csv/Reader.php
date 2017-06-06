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

namespace Hubzero\Content\Import\Adapter\Csv;

use Iterator;
use stdClass;

/**
 *  CSV Iterator Class implemeting interator
 */
class Reader implements Iterator
{
	const ROW_LENGTH = 0;

	/**
	 * CSV file
	 *
	 * @var  string
	 */
	private $file;

	/**
	 * CSV field delimiter
	 *
	 * @var  string
	 */
	private $delimiter;

	/**
	 * Current row position
	 *
	 * @var  integer
	 */
	private $position;

	/**
	 * Cached list of headers
	 *
	 * @var  array
	 */
	private $headers;

	/**
	 * CSV Iterator Constructor
	 * 
	 * @param   string  $file  CSV file we want to use
	 * @param   string  $key   CSV field delimiter
	 * @return  void
	 */
	public function __construct($file, $delimiter)
	{
		// Line endings can vary depending on what App/OS outputted the CSV
		ini_set('auto_detect_line_endings', true);

		$this->file = fopen($file, 'r');

		ini_set('auto_detect_line_endings', false);

		$this->delimiter = $delimiter;
	}

	/**
	 * Get the first row of headers
	 *
	 * @return  object  Row as a stdClass
	 */
	public function headers()
	{
		if (!$this->headers)
		{
			$this->rewind();

			$row = fgetcsv($this->file, self::ROW_LENGTH, $this->delimiter);

			$this->position++;

			// store headers for later
			if ($this->position == 1)
			{
				$this->headers = $row;
			}

			$this->rewind();
		}

		return $this->headers;
	}

	/**
	 * Get the current row
	 *
	 * @return  object  Row as a stdClass
	 */
	public function current()
	{
		$row = fgetcsv($this->file, self::ROW_LENGTH, $this->delimiter);
		$this->position++;

		// store headers for later
		if ($this->position == 1)
		{
			$this->headers = $row;
		}

		// return null for the first row and last row if empty
		// we dont want to count the headings row
		if ($this->position == 1 || $row === false)
		{
			return null;
		}

		// map headings
		$object = new stdClass;
		foreach ($this->headers as $k => $header)
		{
			$header = trim($header);
			$header = trim($header, ':');
			$header = ($header ?: 'COLUMN');

			// If a column header contains a colon, we break it
			// into a sub-object with properties.
			//
			// Address:street, Address:city
			//  ->  Address => { street = data, city = data }
			//
			if (strpos($header, ':'))
			{
				$parts = explode(':', $header);

				// Make sure we have more than one part
				if (count($parts) > 1)
				{
					if (!isset($object->$parts[0]) || !is_object($object->$parts[0]))
					{
						$object->$parts[0] = new stdClass;
					}
					$object->$parts[0]->$parts[1] = $row[$k];
				}
				else
				{
					$object->$header = $row[$k];
				}
			}
			else
			{
				$object->$header = $row[$k];
			}
		}

		// return as object
		return $object;
	}

	/**
	 * Get our current position while iterating
	 *
	 * @return  integer  Current position
	 */
	public function key()
	{
		return $this->position;
	}

	/**
	 * Go to the next row that matches our key
	 *
	 * @return  void
	 */
	public function next()
	{
		return !feof($this->file);
	}

	/**
	 * Move to the first row that matches our key
	 *
	 * @return  void
	 */
	public function rewind()
	{
		$this->position = 0;
		rewind($this->file);
	}

	/**
	 * Is our current row valid
	 *
	 * @return  boolean  Is valid?
	 */
	public function valid()
	{
		if (!$this->next())
		{
			fclose($this->file);
			return false;
		}
		return true;
	}
}
