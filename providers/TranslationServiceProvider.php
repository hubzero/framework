<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Framework\Providers;

use Hubzero\Language\Translator;
use Hubzero\Base\ServiceProvider;

/**
 * Language translation service provider
 *
 * @codeCoverageIgnore
 */
class TranslationServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
		$this->app['language'] = function($app)
		{
			$locale = $app['config']->get('locale', 'en-GB');
			$debug  = $app['config']->get('debug_lang', false);

			return new Translator($locale, $debug, $app['client']->name);
		};

		$this->app['language.filter'] = false;
	}

	/**
	 * Add the plugin loader to the event dispatcher.
	 *
	 * @return  void
	 */
	public function boot()
	{
		$translator = $this->app['language'];

		$language = null;

		// If a language was specified it has priority
		if (!$language && $this->app->has('request') && $this->app->isSite())
		{
			$lang = $this->app['request']->getString('language', null);

			if ($lang && $translator->exists($lang))
			{
				$language = $lang;
			}
		}

		// Detect user specified language
		if (!$language && $this->app->has('user'))
		{
			$lang = \User::getParam($this->app['client']->alias . '_language');

			if ($lang && $translator->exists($lang))
			{
				$language = $lang;
			}
		}

		// Detect browser language
		if (!$language && $this->app->has('browser') && $this->app->isSite())
		{
			$lang = $translator->detectLanguage();

			if ($lang && $translator->exists($lang))
			{
				$language = $lang;
			}
		}

		// Detect default language
		if (!$language && $this->app->has('component'))
		{
			$params = $this->app['component']->params('com_languages');

			$language = $params->get(
				$this->app['client']->name,
				$this->app['config']->get('language', 'en-GB')
			);
		}

		// One last check to make sure we have something
		if (!$language || !$translator->exists($language))
		{
			$lang = $this->app['config']->get('language', 'en-GB');

			if ($translator->exists($lang))
			{
				$language = $lang;
			}
		}

		if ($language)
		{
			$translator->setLanguage($language);
		}

		$boot = DS . 'bootstrap' . DS . ucfirst($this->app['client']->name);

		$translator->load('lib_hubzero', PATH_APP . $boot, null, false, true) ||
		$translator->load('lib_hubzero', PATH_CORE . $boot, null, false, true);
	}
}
