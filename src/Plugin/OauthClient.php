<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Plugin;

/**
 * Extended Plugin for OAuth clients
 */
abstract class OauthClient extends Plugin
{
	/**
	 * Perform logout
	 *
	 * @return  void
	 */
	abstract public function logout();

	/**
	 * Check login status of current user with regards to provider
	 *
	 * @return  array  $status
	 */
	abstract public function status();

	/**
	 * Method to call when redirected back from provider after authentication
	 * Grab the return URL if set and handle denial of app privileges from provider
	 *
	 * @param   object  $credentials
	 * @param   object  $options
	 * @return  void
	 */
	abstract public function login(&$credentials, &$options);

	/**
	 * Method to setup params and redirect to auth URL
	 *
	 * @param   object  $view  view object
	 * @param   object  $tpl   template object
	 * @return  void
	 */
	abstract public function display($view, $tpl);

	/**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @param   array    $credentials  Array holding the user credentials
	 * @param   array    $options      Array of extra options
	 * @param   object   $response     Authentication response object
	 * @return  boolean
	 */
	abstract public function onUserAuthenticate($credentials, $options, &$response);

	/**
	 * Similar to onAuthenticate, except we already have a logged in user, we're just linking accounts
	 *
	 * @param   array  $options
	 * @return  void
	 */
	abstract public function link($options=array());

	/**
	 * Builds the redirect URI based on the current URI and a few other assumptions
	 *
	 * @param   string  $name  The plugin name
	 * @return  string
	 **/
	protected static function getRedirectUri($name)
	{
		// Get the hub url
		$service = trim(\Request::base(), '/');

		$task = 'login';

		if (\App::isSite())
		{
			// If someone is logged in already, then we're linking an account
			$task  = (\User::isGuest()) ? 'login' : 'link';
		}

		$scope = '/index.php?option=com_login&task=' . $task . '&authenticator=' . $name;

		return $service . $scope;
	}
}
