<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Config\Tests\Processor;

use Hubzero\Test\Basic;
use Hubzero\Config\Processor\Xml;
use stdClass;

/**
 * Xml Processor tests
 */
class XmlTest extends Basic
{
	/**
	 * Format processor
	 *
	 * @var  object
	 */
	private $processor = null;

	/**
	 * Expected datain object form
	 *
	 * @var  object
	 */
	private $obj = null;

	/**
	 * Expected data as an array
	 *
	 * @var  array
	 */
	private $arr = null;

	/**
	 * Expected data as a string
	 *
	 * @var  string
	 */
	private $str = '<?xml version="1.0"?>
<config>
	<setting name="app" type="object">
		<setting name="application_env" type="string">development</setting>
		<setting name="editor" type="string">ckeditor</setting>
		<setting name="list_limit" type="integer">25</setting>
		<setting name="helpurl" type="string">English (GB) - HUBzero help</setting>
		<setting name="debug" type="integer">1</setting>
		<setting name="debug_lang" type="integer">0</setting>
		<setting name="sef" type="integer">1</setting>
		<setting name="sef_rewrite" type="integer">1</setting>
		<setting name="sef_suffix" type="integer">0</setting>
		<setting name="sef_groups" type="integer">0</setting>
		<setting name="feed_limit" type="integer">10</setting>
		<setting name="feed_email" type="string">author</setting>
	</setting>
	<setting name="seo" type="object">
		<setting name="sef" type="integer">1</setting>
		<setting name="sef_groups" type="integer">0</setting>
		<setting name="sef_rewrite" type="integer">1</setting>
		<setting name="sef_suffix" type="integer">0</setting>
		<setting name="unicodeslugs" type="integer">0</setting>
		<setting name="sitename_pagetitles" type="integer">0</setting>
	</setting>
</config>';

	/**
	 * Test setup
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		$data = new stdClass();

		$data->app = new stdClass();
		$data->app->application_env = "development";
		$data->app->editor = "ckeditor";
		$data->app->list_limit = 25;
		$data->app->helpurl = "English (GB) - HUBzero help";
		$data->app->debug = 1;
		$data->app->debug_lang = 0;
		$data->app->sef = 1;
		$data->app->sef_rewrite = 1;
		$data->app->sef_suffix = 0;
		$data->app->sef_groups = 0;
		$data->app->feed_limit = 10;
		$data->app->feed_email = "author";

		$data->seo = new stdClass();
		$data->seo->sef = 1;
		$data->seo->sef_groups = 0;
		$data->seo->sef_rewrite = 1;
		$data->seo->sef_suffix = 0;
		$data->seo->unicodeslugs = 0;
		$data->seo->sitename_pagetitles = 0;

		$this->obj = $data;
		$this->arr = array(
			'app' => (array)$data->app,
			'seo' => (array)$data->seo
		);

		$this->processor = new Xml();

		parent::setUp();
	}

	/**
	 * Tests the getSupportedExtensions() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::getSupportedExtensions
	 * @return  void
	 **/
	public function testGetSupportedExtensions()
	{
		$extensions = $this->processor->getSupportedExtensions();

		$this->assertTrue(is_array($extensions));
		$this->assertCount(1, $extensions);
		$this->assertTrue(in_array('xml', $extensions));
	}

	/**
	 * Tests the canParse() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::canParse
	 * @return  void
	 **/
	public function testCanParse()
	{
		$this->assertFalse($this->processor->canParse('Cras justo odio, dapibus ac facilisis in, egestas eget quam.'));
		$this->assertFalse($this->processor->canParse('{"application_env":"development","editor":"ckeditor","list_limit":"25"}'));
		$this->assertTrue($this->processor->canParse($this->str));
	}

	/**
	 * Tests the parse() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::parse
	 * @return  void
	 **/
	public function testParse()
	{
		$result = $this->processor->parse(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Files' . DIRECTORY_SEPARATOR . 'test.xml');

		$this->assertEquals($this->arr, $result);
	}

	/**
	 * Tests the objectToString() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::objectToString
	 * @return  void
	 **/
	public function testObjectToString()
	{
		$result = $this->processor->objectToString($this->obj, array(
			'name'     => 'config',
			'nodeName' => 'setting'
		));

		$str = str_replace(array("\n", "\t"), '', $this->str);
		$str = str_replace('<?xml version="1.0"?>', "<?xml version=\"1.0\"?>\n", $str);

		$this->assertEquals($str, trim($result));
	}

	/**
	 * Tests the stringToObject() method.
	 *
	 * @covers  \Hubzero\Config\Processor\Xml::stringToObject
	 * @return  void
	 **/
	public function testStringToObject()
	{
		$result = $this->processor->stringToObject($this->str);

		$this->assertEquals($this->obj, $result);
	}
}
