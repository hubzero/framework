<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

define('DS', DIRECTORY_SEPARATOR);

/*
|--------------------------------------------------------------------------
| Register The Composer Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for the application. We'll require it here so that we do not have to 
| worry about the loading of any of the classes "manually".
|
*/

require __DIR__ . DS . 'vendor' . DS . 'autoload.php';

/*
|--------------------------------------------------------------------------
| Include Helper Functions
|--------------------------------------------------------------------------
|
| Include some helper functions. There's really no other good spot to do
| this so it happens here.
|
*/

require __DIR__ . DS . 'src' . DS . 'Base' . DS . 'helpers.php';

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new application instance which
| serves as the "glue" for all the parts of a hub, and is the IoC container
| for the system binding all of the various parts.
|
*/

$app = new Hubzero\Base\Application;

// Explicitly set the client type to testing as some libs do require this info
$app['client'] = Hubzero\Base\ClientManager::client('testing', true);

/*
|--------------------------------------------------------------------------
| Bind The Application In The Container
|--------------------------------------------------------------------------
|
| This may look strange, but we actually want to bind the app into itself
| in case we need to Facade test an application. This will allow us to
| resolve the "app" key out of this container for this app's facade.
|
*/

$app['app'] = $app;

/*
|--------------------------------------------------------------------------
| Register The Configuration Repository
|--------------------------------------------------------------------------
|
| The configuration repository is used to lazily load in the options for
| this application from the configuration files. The files are easily
| separated by their concerns so they do not become really crowded.
|
*/

$app['config'] = new Hubzero\Config\Repository('test', new Hubzero\Config\FileLoader('config'));

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
|
| Register all of the core pieces of the framework including session, 
| caching, and more. First, we'll load the core bootstrap list of services
| and then we'll give the app a chance to modify that list.
|
*/

$services = [
	'EventServiceProvider',
	'TranslationServiceProvider',
	'DatabaseServiceProvider',
	'PluginServiceProvider',
	'ProfilerServiceProvider',
	'LogServiceProvider',
	'RouterServiceProvider',
	'FilesystemServiceProvider',
];

foreach ($services as $service)
{
	require_once __DIR__ . '/providers/' . $service . '.php';

	$app->register('Framework\\Providers\\' . $service);
}

/*
|--------------------------------------------------------------------------
| Load The Aliases
|--------------------------------------------------------------------------
|
| The alias loader is responsible for lazy loading the class aliases setup
| for the application.
|
*/

$app->registerFacades([
	'App'        => 'Hubzero\Facades\App',
	'Config'     => 'Hubzero\Facades\Config',
	'Request'    => 'Hubzero\Facades\Request',
	'Response'   => 'Hubzero\Facades\Response',
	'Event'      => 'Hubzero\Facades\Event',
	'Route'      => 'Hubzero\Facades\Route',
	'User'       => 'Hubzero\Facades\User',
	'Lang'       => 'Hubzero\Facades\Lang',
	'Log'        => 'Hubzero\Facades\Log',
	'Date'       => 'Hubzero\Facades\Date',
	'Plugin'     => 'Hubzero\Facades\Plugin',
	'Filesystem' => 'Hubzero\Facades\Filesystem',
]);
