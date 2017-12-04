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

namespace Hubzero\User\Picture;

use Hubzero\Content\Moderator;
use Hubzero\Utility\Str;
use Hubzero\User\Profile;
use Hubzero\Image\Processor;

/**
 * User picture resolver for files that were uploaded
 * and retained their original name (i.e., weren't
 * renamed to 'profile' and 'thumb')
 */
class Namedfile implements Resolver
{
	/**
	 * File path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * File name
	 *
	 * @var  string
	 */
	protected $pictureName = 'profile.png';

	/**
	 * Thumbnail name
	 *
	 * @var  string
	 */
	protected $thumbnailName = 'thumb.png';

	/**
	 * Constructor
	 *
	 * @param   array  $config
	 * @return  void
	 */
	public function __construct($config=array())
	{
		if (array_key_exists('pictureName', $config))
		{
			$this->pictureName = $config['pictureName'];
		}

		if (array_key_exists('thumbnailName', $config))
		{
			$this->thumbnailName = $config['thumbnailName'];
		}

		if (array_key_exists('path', $config))
		{
			$this->path = $config['path'];
		}
	}

	/**
	 * Get a path or URL to a user pciture
	 *
	 * @param   integer  $id
	 * @param   string   $name
	 * @param   string   $email
	 * @param   bool     $thumbnail
	 * @return  string
	 */
	public function picture($id, $name, $email, $thumbnail = true)
	{
		$member = Profile::getInstance($id);

		if (!$member)
		{
			return '';
		}

		// If member has a picture set
		if ($file = $member->get('picture'))
		{
			$path = $this->path . DIRECTORY_SEPARATOR . Str::pad($id, 5) . DIRECTORY_SEPARATOR;
			$file = ltrim($file, DIRECTORY_SEPARATOR);

			// Does the file exist?
			if ($file != 'profile.png' && file_exists($path . $file))
			{
				try
				{
					// Attempt to rename and resize to 'profile.png'
					$hi = new Processor($path . $file);
					if (count($hi->getErrors()) == 0)
					{
						$hi->autoRotate();
						$hi->resize(400);
						$hi->setImageType(IMAGETYPE_PNG);
						$hi->save($path . $this->pictureName);
					}

					// If we sucessfully made a 'profile.png',
					// attempt to rename and resize to 'thumb.png'
					if (file_exists($path . $this->pictureName))
					{
						$hi = new Processor($path . $this->pictureName);
						if (count($hi->getErrors()) == 0)
						{
							$hi->resize(50, false, true, true);
							$hi->save($path . $this->thumbnailName);
						}
					}
				}
				catch (\Exception $e)
				{
					return '';
				}
			}

			$file = $this->pictureName;

			if ($thumbnail)
			{
				$file = $this->thumbnailName;
			}

			$path .= $file;

			if (file_exists($path))
			{
				return with(new Moderator($path))->getUrl();
			}
		}

		return '';
	}

	/**
	 * Generate a thumbnail file name format
	 * example.jpg -> example_thumb.jpg
	 *
	 * @param   string  $thumb  Filename to get thumbnail of
	 * @return  string
	 */
	public static function thumbit($thumb)
	{
		$dot = strrpos($thumb, '.') + 1;
		$ext = substr($thumb, $dot);

		return preg_replace('#\.[^.]*$#', '', $thumb) . '_thumb.' . $ext;
	}
}
