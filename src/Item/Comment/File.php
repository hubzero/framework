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

namespace Hubzero\Item\Comment;

use Hubzero\Database\Relational;
use Hubzero\Filesystem\Util;

/**
 * Class for comment files (attachments)
 */
class File extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'item_comment';

	/**
	 * Upload path
	 *
	 * @var  string
	 */
	protected $uploadDir = '/site/comments';

	/**
	 * File size
	 *
	 * @var  string
	 */
	protected $size = null;

	/**
	 * Diemnsions for file (must be an image)
	 *
	 * @var  array
	 */
	protected $dimensions = null;

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'comment_id' => 'positive|nonzero',
		'filename'   => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 **/
	public $always = array(
		'filename'
	);

	/**
	 * Set the upload path
	 *
	 * @param   string  $path  Path to set to
	 * @return  object
	 */
	public function setUploadDir($path)
	{
		$path = str_replace(' ', '_', trim($path));
		$path = Util::normalizePath($path);

		$this->uploadDir = ($path ? $path : $this->uploadDir);

		return $this;
	}

	/**
	 * Get the upload path
	 *
	 * @return  string
	 */
	public function getUploadDir()
	{
		return PATH_APP . $this->uploadDir;
	}

	/**
	 * Ensure no invalid characters
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function automaticFilename($data)
	{
		$filename = $data['filename'];
		$filename = preg_replace("/[^A-Za-z0-9.]/i", '-', $filename);

		$data['filename'] = $filename;

		return $data['filename'];
	}

	/**
	 * Ensure no conflicting file names by
	 * renaming the incoming file if the name
	 * already exists
	 *
	 * @param   array  $data
	 * @return  string
	 */
	public function uniqueFilename($data)
	{
		$filename = $this->automaticFilename($data);

		if (file_exists($this->getUploadDir() . DS . $data['comment_id'] . DS . $filename))
		{
			$ext = strrchr($filename, '.');
			$prefix = substr($filename, 0, -strlen($ext));

			$i = 1;

			while (is_file($this->getUploadDir() . DS . $data['comment_id'] . DS . $filename))
			{
				$filename = $prefix . ++$i . $ext;
			}
		}

		$data['filename'] = $filename;

		return $data['filename'];
	}

	/**
	 * Delete record
	 *
	 * @return  boolean  True if successful, False if not
	 */
	public function destroy()
	{
		$path = $this->path();

		if (file_exists($path))
		{
			if (!\Filesystem::delete($path))
			{
				$this->setError('Unable to delete file.');

				return false;
			}
		}

		return parent::destroy();
	}

	/**
	 * Upload file
	 *
	 * @param   string  $name
	 * @param   string  $temp
	 * @return  bool
	 */
	public function upload($name, $temp)
	{
		$destination = $this->getUploadDir() . DS . $this->get('comment_id');

		if (!is_dir($destination))
		{
			if (!\Filesystem::makeDirectory($destination))
			{
				$this->setError('Unable to create upload path.');

				return false;
			}
		}

		$filename = $this->uniqueFilename(array(
			'filename'   => $name,
			'comment_id' => $this->get('comment_id')
		));

		$destination .= DS . $filename;

		if (!\Filesystem::upload($temp, $destination))
		{
			$this->setError('Unable to upload file.');

			return false;
		}

		$this->set('filename', $filename);

		return true;
	}

	/**
	 * File path
	 *
	 * @return  integer
	 */
	public function path()
	{
		return $this->getUploadDir() . DS . $this->get('comment_id') . DS . $this->get('filename');
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function isImage()
	{
		return preg_match("/\.(bmp|gif|jpg|jpe|jpeg|png)$/i", $this->get('filename'));
	}

	/**
	 * Is the file an image?
	 *
	 * @return  boolean
	 */
	public function size()
	{
		if ($this->size === null)
		{
			$this->size = 0;

			$path = $this->path();

			if (file_exists($path))
			{
				$this->size = filesize($path);
			}
		}

		return $this->size;
	}

	/**
	 * File width and height
	 *
	 * @return  array
	 */
	public function dimensions()
	{
		if (!$this->dimensions)
		{
			$this->dimensions = array(0, 0);

			if ($this->isImage() && file_exists($this->path()))
			{
				$this->dimensions = getimagesize($this->path());
			}
		}

		return $this->dimensions;
	}

	/**
	 * File width
	 *
	 * @return  integer
	 */
	public function width()
	{
		$dimensions = $this->dimensions();

		return $dimensions[0];
	}

	/**
	 * File height
	 *
	 * @return  integer
	 */
	public function height()
	{
		$dimensions = $this->dimensions();

		return $dimensions[1];
	}
}
