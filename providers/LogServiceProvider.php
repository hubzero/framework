<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Framework\Providers;

use Hubzero\Log\Manager;
use Hubzero\Base\ServiceProvider;

/**
 * Event service provider
 *
 * @codeCoverageIgnore
 */
class LogServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['log'] = function($app)
		{
			$path = $app['config']->get('log_path');
			if (is_dir('/var/log/hubzero'))
			{
				$path = '/var/log/hubzero';
			}

			$dispatcher = null;
			if ($app->has('dispatcher'))
			{
				$dispatcher = $app['dispatcher'];
			}

			$manager = new Manager($path);

			$manager->register('debug', array(
				'file'       => 'cmsdebug.log',
				'dispatcher' => $dispatcher
			));

			$manager->register('auth', array(
				'file'       => 'cmsauth.log',
				'level'      => 'info',
				'format'     => "%datetime% %message%\n",
				'dispatcher' => $dispatcher
			));

			$manager->register('spam', array(
				'file'       => 'cmsspam.log',
				'dispatcher' => $dispatcher
			));

			return $manager;
		};
	}
}
