<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database;

use Hubzero\Base\ServiceProvider;

/**
 * Database service provider
 */
class DatabaseServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['db'] = function($app)
		{
			// @FIXME: this isn't pretty, but it will shim the removal of the old mysql_* calls from php
			$driver = (Config::get('dbtype') == 'mysql') ? 'pdo' : Config::get('dbtype');

			$options = [
				'driver'   => $driver,
				'host'     => Config::get('host'),
				'user'     => Config::get('user'),
				'password' => Config::get('password'),
				'database' => Config::get('db'),
				'prefix'   => Config::get('dbprefix')
			];

			return Driver::getInstance($options);
		};
	}
}
