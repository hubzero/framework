<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Framework\Providers;

use Hubzero\Base\ServiceProvider;

/**
 * Joomla handler service provider
 * 
 * This loads in the core Joomla framework and instantiates
 * the base application class.
 *
 * @codeCoverageIgnore
 */
class JoomlaServiceProvider extends ServiceProvider
{
	/**
	 * Register the exception handler.
	 *
	 * @return  void
	 */
	public function boot()
	{
		require_once PATH_CORE . DS . 'libraries' . DS . 'import.php';
		require_once PATH_CORE . DS . 'libraries' . DS . 'cms.php';

		if ($this->app->isAdmin() || $this->app->isSite())
		{
			jimport('joomla.application.menu');
		}

		jimport('joomla.environment.uri');
		jimport('joomla.utilities.utility');
		jimport('joomla.event.dispatcher');
		jimport('joomla.utilities.arrayhelper');

		if ($this->app->isAdmin())
		{
			jimport('joomla.html.parameter');

			require_once PATH_CORE . DS . 'bootstrap' . DS . $this->app['client']->name . DS . 'helper.php';
			require_once PATH_CORE . DS . 'bootstrap' . DS . $this->app['client']->name . DS . 'toolbar.php';
		}

		$app = \JFactory::getApplication($this->app['client']->name);
	}
}
