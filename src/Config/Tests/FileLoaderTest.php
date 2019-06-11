<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config\Tests;

use Hubzero\Test\Basic;
use Hubzero\Config\FileLoader;

/**
 * FileLoader tests
 */
class FileLoaderTest extends Basic
{
	/**
	 * Tests constructor
	 *
	 * @covers  \Hubzero\Config\FileLoader::__construct
	 * @covers  \Hubzero\Config\FileLoader::getDefaultPath
	 * @covers  \Hubzero\Config\FileLoader::getPaths
	 * @covers  \Hubzero\Config\FileLoader::getParser
	 * @covers  \Hubzero\Config\FileLoader::load
	 * @return  void
	 **/
	public function testLoad()
	{
		$expected = array(
			'app' => array(
				'application_env' => 'development',
				'editor' => 'ckeditor',
				'list_limit' => '25',
				'helpurl' => 'English (GB) - HUBzero help',
				'debug' => '1',
				'debug_lang' => '0',
				'sef' => '1',
				'sef_rewrite' => '1',
				'sef_suffix' => '0',
				'sef_groups' => '0',
				'feed_limit' => '10',
				'feed_email' => 'author'
			),
			'seo' => array(
				'sef' => '1',
				'sef_groups' => '0',
				'sef_rewrite' => '1',
				'sef_suffix' => '0',
				'unicodeslugs' => '0',
				'sitename_pagetitles' => '0'
			)
		);

		$path = __DIR__ . '/Files/Repository';

		$loader = new FileLoader($path);

		$this->assertEquals($path, $loader->getDefaultPath());

		$data = $loader->load();

		$this->assertEquals($expected, $data);

		$expected['app']['application_env'] = 'production';
		$expected['app']['editor'] = 'none';
		$expected['app']['debug'] = '0';

		$data = $loader->load('api');

		$this->assertEquals($expected, $data);

		// Try with a bad path
		$expected = array();
		$path = __DIR__ . '/Foo';

		$loader = new FileLoader($path);

		$data = $loader->load();

		$this->assertEquals($expected, $data);
	}
}
