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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Template\Tests;

use Hubzero\Test\Database;
use Hubzero\Template\Loader;
use Hubzero\Base\Application;

/**
 * Template loader test
 */
class LoaderTest extends Database
{
	/**
	 * Hubzero\Template\Loader
	 *
	 * @var  object
	 */
	private $instance;

	/**
	 * Sets up the tests...called prior to each test
	 *
	 * @return  void
	 */
	public function setUp()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());

		$app = new Application();
		$app['client'] = new \Hubzero\Base\Client\Site();
		$app['db']     = $this->getMockDriver();
		$app['config'] = \App::get('config');

		$this->loader = new Loader($app, [
			'path_app'  => __DIR__ . '/Mock/app',
			'path_core' => __DIR__ . '/Mock/core'
		]);
	}

	/**
	 * Test the getPath() method.
	 *
	 * @covers  \Hubzero\Template\Loader::getPath
	 * @return  void
	 */
	public function testGetPath()
	{
		$this->assertEquals($this->loader->getPath('core'), __DIR__ . '/Mock/core');
		$this->assertEquals($this->loader->getPath('app'), __DIR__ . '/Mock/app');

		$this->assertNotEquals($this->loader->getPath('core'), $this->loader->getPath('app'));
	}

	/**
	 * Test the setPath() method.
	 *
	 * @covers  \Hubzero\Template\Loader::setPath
	 * @return  void
	 */
	public function testSetPath()
	{
		$core = $this->loader->getPath('core');
		$app  = $this->loader->getPath('app');

		$this->assertInstanceOf('Hubzero\Template\Loader', $this->loader->setPath('core', __DIR__ . '/core'));
		$this->assertEquals($this->loader->getPath('core'), __DIR__ . '/core');

		$this->assertInstanceOf('Hubzero\Template\Loader', $this->loader->setPath('app', __DIR__ . '/app'));
		$this->assertEquals($this->loader->getPath('app'), __DIR__ . '/app');

		$this->assertNotEquals($this->loader->getPath('core'), $this->loader->getPath('app'));

		$this->loader->setPath('core', $core);
		$this->loader->setPath('app', $app);
	}

	/**
	 * Test that the system template is built and returned properly
	 *
	 * @covers  \Hubzero\Template\Loader::getSystemTemplate
	 * @return  void
	 */
	public function testGetSystemTemplate()
	{
		$template = $this->loader->getSystemTemplate();

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'system');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 0);
		$this->assertEquals($template->home, 0);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);
	}

	/**
	 * Test the setStyle() and getStyle() methods.
	 *
	 * @covers  \Hubzero\Template\Loader::setStyle
	 * @covers  \Hubzero\Template\Loader::getStyle
	 * @return  void
	 */
	public function testSetGetStyle()
	{
		$this->loader->setStyle(1);

		$this->assertEquals($this->loader->getStyle(), 1);

		$this->loader->setStyle('0012');

		$this->assertEquals($this->loader->getStyle(), 12);
	}

	/**
	 * Test the setLang() and getLang() methods.
	 *
	 * @covers  \Hubzero\Template\Loader::setLang
	 * @covers  \Hubzero\Template\Loader::getLang
	 * @return  void
	 */
	public function testSetGetLang()
	{
		$this->loader->setLang('de-DE');

		$this->assertEquals($this->loader->getLang(), 'de-DE');

		$this->loader->setLang('en-US');

		$this->assertEquals($this->loader->getLang(), 'en-US');
	}

	/**
	 * Test the the constructor is properly setting all optional values
	 *
	 * @covers  \Hubzero\Template\Loader::__construct
	 * @return  void
	 */
	public function testConstructor()
	{
		\Hubzero\Database\Relational::setDefaultConnection($this->getMockDriver());

		$app = new Application();
		$app['client'] = new \Hubzero\Base\Client\Site();
		$app['db']     = $this->getMockDriver();
		$app['config'] = \App::get('config');

		$loader = new Loader($app, [
			'path_app'  => __DIR__ . '/Mock/app',
			'path_core' => __DIR__ . '/Mock/core',
			'style'     => 5,
			'lang'      => 'en-US'
		]);

		$this->assertEquals($loader->getPath('core'), __DIR__ . '/Mock/core');
		$this->assertEquals($loader->getPath('app'), __DIR__ . '/Mock/app');
		$this->assertEquals($loader->getStyle(), 5);
		$this->assertEquals($loader->getLang(), 'en-US');
	}

	/**
	 * Test that the system template is built and returned properly
	 *
	 * @covers  \Hubzero\Template\Loader::getTemplate
	 * @return  void
	 */
	public function testGetTemplate()
	{
		$template = $this->loader->getTemplate(0);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'sitefoo');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 3);
		$this->assertEquals($template->home, 1);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);

		$template = $this->loader->getTemplate(1);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'adminfoo');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 1);
		$this->assertEquals($template->home, 1);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);

		$template = $this->loader->getTemplate(0, 4);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'sitebar');
		$this->assertEquals($template->protected, 0);
		$this->assertEquals($template->id, 4);
		$this->assertEquals($template->home, 0);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('app') . DIRECTORY_SEPARATOR . $template->template);

		$template = $this->loader->getTemplate(1, 2);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'adminbar');
		$this->assertEquals($template->protected, 0);
		$this->assertEquals($template->id, 2);
		$this->assertEquals($template->home, 0);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('app') . DIRECTORY_SEPARATOR . $template->template);

		$template = $this->loader->getTemplate(1, 7);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'system');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 0);
		$this->assertEquals($template->home, 0);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);

		$template = $this->loader->getTemplate(0, 8);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'system');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 0);
		$this->assertEquals($template->home, 0);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);
	}

	/**
	 * Test that the system template is built and returned properly
	 *
	 * @covers  \Hubzero\Template\Loader::load
	 * @return  void
	 */
	public function testLoad()
	{
		// Load tmeplate by current client (site)
		$template = $this->loader->load();

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'sitefoo');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 3);
		$this->assertEquals($template->home, 1);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);

		// Load site tmeplate by client ID
		$template = $this->loader->load(0);

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'sitefoo');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 3);
		$this->assertEquals($template->home, 1);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);

		// Load admin template by client name
		$template = $this->loader->load('administrator');

		$this->assertTrue(is_object($template));
		$this->assertEquals($template->template, 'adminfoo');
		$this->assertEquals($template->protected, 1);
		$this->assertEquals($template->id, 1);
		$this->assertEquals($template->home, 1);
		$this->assertInstanceOf('Hubzero\Config\Registry', $template->params);
		$this->assertEquals($template->path, $this->loader->getPath('core') . DIRECTORY_SEPARATOR . $template->template);
	}
}
