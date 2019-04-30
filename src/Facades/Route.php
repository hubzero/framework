<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades;

/**
 * Router facade
 */
class Route extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getAccessor()
	{
		return 'router';
	}

	/**
	 * Get the router for a specific client
	 *
	 * @param   string  $client  The name of the application.
	 * @param   string   $url    Absolute or Relative URI to resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             0: Leave URI in the same secure state as it was passed to the function.
	 *                            -1: Make URI unsecure using the global unsecure site URI.
	 * @return  The translated humanly readible URL.
	 */
	public static function urlForClient($client, $url, $xhtml = true, $ssl = null)
	{
		if (!$client)
		{
			return static::getRoot()->url($url, $xhtml, $ssl);
		}

		return self::$app['router']->client($client)->url($url, $xhtml, $ssl);
	}
}
