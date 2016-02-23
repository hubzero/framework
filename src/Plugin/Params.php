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

namespace Hubzero\Plugin;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Class for custom plugin parameters
 */
class Params extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'plugin';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__plugin_params';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'folder';

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
		'object_id' => 'positive|nonzero',
		'folder'    => 'notempty',
		'element'   => 'notempty'
	);

	/**
	 * Load a record and binf to $this
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  boolean  True on success
	 */
	public static function oneByPlugin($oid=null, $folder=null, $element=null)
	{
		return self::all()
			->whereEquals('object_id', (int) $oid)
			->whereEquals('folder', (int) $folder)
			->whereEquals('element', (int) $element)
			->row();
	}

	/**
	 * Get the custom parameters for a plugin
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  object
	 */
	public static function getCustomParams($oid=null, $folder=null, $element=null)
	{
		$result = self::oneByPlugin($oid, $folder, $element);

		return new Registry($result->get('params'));
	}

	/**
	 * Get the default parameters for a plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin name
	 * @return  object
	 */
	public static function getDefaultParams($folder=null, $element=null)
	{
		$plugin = \Plugin::byType($folder, $element);

		return new Registry($plugin->params);
	}

	/**
	 * Get the parameters for a plugin
	 * Merges default params and custom params (take precedence)
	 *
	 * @param   integer  $oid      Object ID (eg, group ID)
	 * @param   string   $folder   Plugin folder
	 * @param   string   $element  Plugin name
	 * @return  object
	 */
	public static function getParams($oid=null, $folder=null, $element=null)
	{
		$custom = self::getCustomParams($oid, $folder, $element);

		$params = self::getDefaultParams($folder, $element);
		$params->merge($custom);

		return $params;
	}
}
