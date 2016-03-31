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
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Spam\Tests;

use Hubzero\Test\Basic;
use Hubzero\Spam\StringProcessor\NoneStringProcessor;
use Hubzero\Spam\StringProcessor\NativeStringProcessor;

/**
 * Spam StringProcessor tests
 */
class StringProcessorTest extends Basic
{
	/**
	 * Tests for setting and getting a StringProcessor
	 *
	 * @covers  \Hubzero\Spam\StringProcessor\NoneStringProcessor::prepare
	 * @return  void
	 **/
	public function testNoneStringProcessor()
	{
		$processor = new NoneStringProcessor();

		$text   = 'Curabitur blandit tempus porttitor.';
		$result = $processor->prepare($text);

		$this->assertEquals($result, $text);
	}

	/**
	 * Tests for setting and getting a StringProcessor
	 *
	 * @covers  \Hubzero\Spam\StringProcessor\NativeStringProcessor::__construct
	 * @covers  \Hubzero\Spam\StringProcessor\NativeStringProcessor::prepare
	 * @return  void
	 **/
	public function testNativeStringProcessor()
	{
		$text   = " Curabitur foo @ blandit up......er tempus porttitor[dot]\nLorem ipsum dolor sit \tamet, consectetur & adipiscing elit.";

		$processor = new NativeStringProcessor();
		$result    = $processor->prepare($text);
		$expected  = "curabitur foo @ blandit up......er tempus porttitor[dot]lorem ipsum dolor sit amet, consectetur & adipiscing elit.";

		$this->assertEquals($result, $expected);

		$processor = new NativeStringProcessor(array('aggressive' => true));
		$result    = $processor->prepare($text);
		$expected  = "curabiturfooatblanditup.ertempusporttitor.loremipsumdolorsitametconsecteturadipiscingelit.";

		$this->assertEquals($result, $expected);
	}
}
