<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

$app['config'] = new \Hubzero\Config\Repository('test', new \Hubzero\Config\FileLoader('config'));

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
	'Hubzero\Base\JoomlaServiceProvider',
	'Hubzero\Events\EventServiceProvider',
	'Hubzero\Language\TranslationServiceProvider',
	'Hubzero\Database\DatabaseServiceProvider',
	'Hubzero\Plugin\PluginServiceProvider',
	'Hubzero\Debug\ProfilerServiceProvider',
	'Hubzero\Log\LogServiceProvider',
	'Hubzero\Routing\RouterServiceProvider',
	'Hubzero\Filesystem\FilesystemServiceProvider',
];

foreach ($services as $service)
{
	$app->register($service);
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