<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Framework\Providers;

use Hubzero\Database\Driver;
use Hubzero\Base\ServiceProvider;

/**
 * Database service provider
 *
 * @codeCoverageIgnore
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
			$driver = ($app['config']->get('dbtype') == 'pdo') ? 'mysql' : $app['config']->get('dbtype');

			$options = [
				'driver'   => $driver,
				'host'     => $app['config']->get('host'),
				'user'     => $app['config']->get('user'),
				'password' => $app['config']->get('password'),
				'database' => $app['config']->get('db'),
				'prefix'   => $app['config']->get('dbprefix')
			];

			return Driver::getInstance($options);
		};
	}
}
