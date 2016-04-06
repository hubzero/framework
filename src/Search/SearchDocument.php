<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;
use Hubzero\User\Group;


/**
 * Hubzero class for performing Search and Indexing Operations.
 */

class SearchDocument extends \Hubzero\Base\Object
{
	public $id;
	public $title;
	public $description;
	public $fulltext;
	public $author;
	public $path;
	public $hubtype;
	public $hubid;
	public $created;
	public $scope;
	public $scope_id;
	public $created_by;
	public $state;
	public $tags;
	public $access_level;

	protected $optionalFields = array(
				'doi',
				'isbn',
				'modified', // According to the DB
				'abstract',
				'location',
				'uid',
				'gid',
				'child_id',
				'parent_id',
				'publish_up',
				'publish_down',
				'type',
				'note',
				'keywords', // Could be tags if nothing, or differ
				'language',
				'tags',
				'badge',
				'date',
				'year',
				'month',
				'day',
				'address',
				'organization',
				'name',
				'organization',
				'url', // if the content is off-site
				'cms-ranking',
				'cms-rating',
				'params',
				'meta',
				'timestamp',
			);

	public function normalize($document)
	{
		$requiredFields = array_keys(get_class_vars($this));

		foreach ($requiredFields as $field)
		{
			if (isset($document[$field]))
			{
				if ($field == 'title' && is_array($document[$field]))
				{
					$value = $document[$field][0];
					$this->set($field, $value);
				}
				else
				{
					$this->set($field, $document[$field]);
				}
			}
		}

		// Optional fields
		foreach ($this->optionalFields as $field)
		{
			if(isset($document[$field]))
			{
				$this->set($field, $document[$field]);
			}
		}
		return $this;
	}

	public function calculatePermissions($type)
	{
		$permissionSets = Event::trigger('search.onCalculatePermissions', $type);
		ddie($permissionSets);
	}

	/**
	 * parse
	 * Borrowed from Plugin\Content\Formathtml\Parser
	 * Used to do some processing on config file 'macros'
	 *
	 * @param mixed $text
	 * @param mixed $row
	 * @access public
	 * @return void
	 */
/*	public function parsePath($text, $row)
	{
		// Remove any trailing whitespace
		$text = rtrim($text);

		// Prepend a line break
		// Makes block parsing a little easier
		$text = "\n" . $text;

		// Clean out any carriage returns.
		// These can screw up some block parsing, such as tables
		$text = str_replace("\r", '', $text);
		$text = preg_replace('/<p>\s*?(\[\[[^\]]+\]\])\s*?<\/p>/i', "\n$1\n", $text);
		$text = preg_replace('/<p>(\[\[[^\]]+\]\])\n/i', "$1\n<p>", $text);
		$matches = array();
		preg_match_all('/\[\[(?P<macroname>[\w.]+)(\]\]|\((?P<macroargs>.*)\)\]\])/U',$text, $matches);

		// Copy the original string
		$path = $text;

		// Build out the path
		foreach ($matches['macroname'] as $k => $match)
		{
			$macroname = $match;
			$argument = $matches['macroargs'][$k];
			$replacement = $this->$macroname($argument, $row);
			$path = trim(preg_replace("(\\[\[".$macroname."\(".$argument."\)\\]\])", $replacement, $path));
		}
		return $path;
	}

*/
	/**
	 * processPaths
	 *
	 * @param string $type 
	 * @param mixed $row 
	 * @access public
	 * @return void
	 */
	/*
	public function processPaths($type = '', $row)
	{
		$config = $this->loadConfig($type);
		foreach ($config['paths'] as $scope => $path)
		{
			if ($scope == $row->scope)
			{
				$parsed = $this->parse($path, $row);
				return $parsed;
			}
		}
	}
	*/

	/**
	 * Year 
	 * 
	 * @param string $date 
	 * @param mixed $row 
	 * @access private
	 * @return void
	 */
	private function Year($date = '', $row)
	{
		$date = $row->$date;
		$year = Date::of(strtotime($date))->toLocal('Y');
		return $year;
	}

	/**
	 * Month
	 *
	 * @param string $date
	 * @param mixed $row
	 * @access private
	 * @return void
	 */
	private function Month($date = '', $row)
	{
		$date = $row->$date;
		$month = Date::of(strtotime($date))->toLocal('m');
		return $month;
	}

	/**
	 * Field 
	 * 
	 * @param string $argument 
	 * @param mixed $row 
	 * @access private
	 * @return void
	 */
	private function Field($argument = '', $row)
	{
		$argument = $row->$argument;
		return $argument;
	}

	/**
	 * Group_cn 
	 * 
	 * @param mixed $id 
	 * @param mixed $row 
	 * @access private
	 * @return void
	 */
	private function Group_cn($id, $row)
	{
			if (method_exists($row, 'get'))
			{
				$gid = $row->get($id);
			}
			else
			{
				$gid = $row->id;
			}

			$group = Group::getInstance($gid);
			if (is_object($group) && isset($group))
			{
				$cn = $group->get('cn');
				return $cn;
			}
			else
			{
				return;
			}
	}

	/**
	 * Member_id 
	 * 
	 * @param mixed $id 
	 * @param mixed $row 
	 * @access private
	 * @return void
	 */
	private function Member_id($id, $row)
	{
		$id = $row->scope_id;
		return $id;
	}
}
