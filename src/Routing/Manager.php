<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Routing;

/**
 * Router manager
 */
class Manager
{
	/**
	 * The application instance.
	 *
	 * @var  object
	 */
	protected $app;

	/**
	 * List of paths to route rules
	 *
	 * @var  array
	 */
	protected $paths = array();

	/**
	 * The array of created "drivers".
	 *
	 * @var  array
	 */
	protected $routers = array();

	/**
	 * Create a new manager instance.
	 *
	 * @param   object  $app
	 * @param   array   $paths
	 * @return  void
	 */
	public function __construct($app, $paths = array())
	{
		$this->app   = $app;
		$this->paths = (array)$paths;
	}

	/**
	 * Get the default client name.
	 *
	 * @return string
	 */
	public function getDefaultClient()
	{
		return $this->app['client']->name;
	}

	/**
	 * Get a client instance.
	 *
	 * @param   string  $client
	 * @return  object
	 */
	public function client($client = null)
	{
		$client = $client ?: $this->getDefaultClient();

		// If the given driver has not been created before, we will create the instances
		// here and cache it so we can return it next time very quickly. If there is
		// already a driver created by this name, we'll just return that instance.
		if (!isset($this->routers[$client]))
		{
			$this->routers[$client] = $this->createRouter($client);
		}

		return $this->routers[$client];
	}

	/**
	 * Create a new client instance.
	 *
	 * @param   string  $client
	 * @return  object
	 */
	protected function createRouter($client)
	{
		$prefix = $this->app['request']->getHttpHost();

		$router = new Router(array(), $prefix);

		$routes = array();

		foreach ($this->paths as $path)
		{
			$routes[] = $path . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . $client;
			$routes[] = $path . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . ucfirst($client);
		}

		foreach ($routes as $route)
		{
			$path = $route . DIRECTORY_SEPARATOR . 'routes.php';

			if (file_exists($path))
			{
				require $path;
			}
		}

		return $router;
	}

	/**
	 * Get all of the created "routers".
	 *
	 * @return array
	 */
	public function getRouters()
	{
		return $this->routers;
	}

	/**
	 * Dynamically call the router instance.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return call_user_func_array(array($this->client(), $method), $parameters);
	}
}
