<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Framework\Providers;

use Hubzero\Debug\Profiler;
use Hubzero\Base\ServiceProvider;

/**
 * Profiler service provider
 *
 * @codeCoverageIgnore
 */
class ProfilerServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['profiler'] = function($app)
		{
			if ($app['config']['debug'] || $app['config']['profile'])
			{
				return new Profiler($app['client']->name);
			}

			return null;
		};
	}
}
