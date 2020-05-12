<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Framework\Providers;

use Hubzero\Plugin\Loader;
use Hubzero\Events\DispatcherInterface;
use Hubzero\Base\ServiceProvider;

/**
 * Plugin loader service provider
 *
 * @codeCoverageIgnore
 */
class PluginServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['plugin'] = function($app)
		{
			return new Loader();
		};
	}

	/**
	 * Add the plugin loader to the event dispatcher.
	 *
	 * @return  void
	 */
	public function boot()
	{
		if ($this->app->has('dispatcher'))
		{
			$this->app['dispatcher']->addListenerLoader($this->app['plugin']);
		}
	}
}
