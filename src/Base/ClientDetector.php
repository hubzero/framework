<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

use Hubzero\Http\Request;

/**
 * Client detector
 *
 * Inspired by Laravel's environment detector
 * http://laravel.com
 */
class ClientDetector
{
	/**
	 * Request URI
	 */
	private $request = null;

	/**
	 * Create a new application instance.
	 *
	 * @return  void
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param   array|string  $environments
	 * @param   array|null    $consoleArgs
	 * @return  string
	 */
	public function detect($environments, $consoleArgs = null)
	{
		if ($consoleArgs)
		{
			return $this->detectConsoleEnvironment($environments, $consoleArgs);
		}

		return $this->detectWebEnvironment($environments);
	}

	/**
	 * Set the application environment for a web request.
	 *
	 * @param   mixed   $environments  array|string
	 * @return  string
	 * @todo    Base off URI instead of Joomla path constant
	 */
	protected function detectWebEnvironment($environments)
	{
		$default = ClientManager::client('site', true);

		foreach ($environments as $environment => $url)
		{
			if ($client = ClientManager::client($environment, true))
			{
				$const = 'JPATH_' . strtoupper($environment);

				// To determine the current environment, we'll simply iterate through the possible
				// environments and look for the host that matches the host for this request we
				// are currently processing here, then return back these environment's names.
				if ((defined($const) && JPATH_BASE == constant($const)) || $this->request->segment(1) == $url)
				{
					return $client;
				}
			}
		}

		return $default;
	}

	/**
	 * Set the application environment from command-line arguments.
	 *
	 * @param   mixed   $environments
	 * @param   array   $args
	 * @return  string
	 */
	protected function detectConsoleEnvironment($environments, array $args)
	{
		// First we will check if an environment argument was passed via console arguments
		// and if it was that automatically overrides as the environment. Otherwise, we
		// will check the environment as a "web" request like a typical HTTP request.
		if (!is_null($value = $this->getEnvironmentArgument($args)))
		{
			return reset(array_slice(explode('=', $value), 1));
		}

		return $this->detectWebEnvironment($environments);
	}

	/**
	 * Get the enviornment argument from the console.
	 *
	 * @param   array  $args
	 * @return  mixed  string|null
	 */
	protected function getEnvironmentArgument(array $args)
	{
		foreach ($args as $k => $v)
		{
			if (substr($v, 0, strlen('--env')) == '--env')
			{
				return $v;
			}
		}

		return null;
	}
}
