<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Auth;

use Hubzero\Database\Relational;

/**
 * Factors database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Factor extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'auth';

	/**
	 * Gets one result or fails by domain and user_id
	 *
	 * @param   string  $domain  The domain of interest
	 * @return  mixed   static|bool
	 */
	public static function currentOrFailByDomain($domain)
	{
		$factor = static::all()->whereEquals('user_id', User::get('id'))
		                       ->whereEquals('domain', $domain)
		                       ->row();

		return ($factor->isNew()) ? false : $factor;
	}
}
