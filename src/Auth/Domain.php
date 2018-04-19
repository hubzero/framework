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

namespace Hubzero\Auth;

use Hubzero\Database\Relational;
use Date;

/**
 * Authentication Link data
 */
class Domain extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'auth';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__auth_domain';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'authenticator' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'type'
	);

	/**
	 * Generates automatic authenticator field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAuthenticator($data)
	{
		$alias = $data['authenticator'];
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 255)
		{
			$alias = substr($alias . ' ', 0, 255);
			$alias = substr($alias, 0, strrpos($alias,' '));
		}

		return preg_replace("/[^a-zA-Z0-9]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticType($data)
	{
		return (isset($data['type']) && $data['type'] ? $data['type'] : 'authentication');
	}

	/**
	 * Get associated links
	 *
	 * @return  object
	 */
	public function links()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Link', 'auth_domain_id');
	}

		/**
	 * Read a record
	 *
	 * @return  boolean  True on success, False on failure
	 */
	public function read()
	{
		if ($this->get('id'))
		{
			$row = self::oneOrNew($this->get('id'));
		}
		else
		{
			$row = self::all()
				->whereEquals('type', $this->get('type'))
				->whereEquals('authenticator', $this->get('authenticator'))
				->whereEquals('domain', $this->get('domain'))
				->row();
		}

		if (!$row || !$row->get('id'))
		{
			return false;
		}

		foreach (array_keys($this->getAttributes()) as $key)
		{
			$this->set($key, $row->get($key));
		}

		return true;
	}

	/**
	 * Create a record
	 *
	 * @return  boolean  True on success, False on failure
	 */
	public function create()
	{
		return $this->save();
	}

	/**
	 * Update a record
	 *
	 * @param   boolean  $all  Update all properties?
	 * @return  boolean
	 */
	public function update($all = false)
	{
		return $this->save();
	}

	/**
	 * Delete a record
	 *
	 * @return  boolean
	 */
	public function delete()
	{
		return $this->destroy();
	}

	/**
	 * Get a Domain instance
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @return  mixed   Object on success, False on failure
	 */
	public static function getInstance($type, $authenticator, $domain)
	{
		$query = self::all()
			->whereEquals('type', $type)
			->whereEquals('authenticator', $authenticator);
		if ($domain)
		{
			$query->whereEquals('domain', $domain);
		}
		$row = $query->row();

		if (!$row || !$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Create a new instance and return it
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @return  mixed
	 */
	public static function createInstance($type, $authenticator, $domain = null)
	{
		if (empty($type) || empty($authenticator))
		{
			return false;
		}

		$row = self::blank();
		$row->set('type', $type);
		$row->set('authenticator', $authenticator);
		if ($domain)
		{
			$row->set('domain', $domain);
		}
		$row->save();

		if (!$row->get('id'))
		{
			return false;
		}

		return $row;
	}

	/**
	 * Find a record by ID
	 *
	 * @param   integer  $id
	 * @return  mixed    Object on success, False on failure
	 */
	public static function find_by_id($id)
	{
		$hzad = self::oneOrNew($id);

		if (empty($hzad->authenticator))
		{
			return false;
		}

		return $hzad;
	}

	/**
	 * Fine a specific record, or create it
	 * if not found
	 *
	 * @param   string  $type
	 * @param   string  $authenticator
	 * @param   string  $domain
	 * @return  mixed
	 */
	public static function find_or_create($type, $authenticator, $domain=null)
	{
		$query = self::all()
			->whereEquals('type', $type)
			->whereEquals('authenticator', $authenticator);
		if ($domain)
		{
			$query->whereEquals('domain', $domain);
		}
		$row = $query->row();

		if (!$row || !$row->get('id'))
		{
			$row = self::blank();
			$row->set('type', $type);
			$row->set('authenticator', $authenticator);
			if ($domain)
			{
				$row->set('domain', $domain);
			}
			$row->save();
		}

		if (!$row->get('id'))
		{
			return false;
		}

		return $row;
	}
}
