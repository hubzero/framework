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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Image;

use Hubzero\Base\Obj;
use Exception;

/**
 * Helper class for image manipulation
 */
class Processor extends Obj
{
	/**
	 * Path to image
	 *
	 * @var  string
	 */
	private $source = null;

	/**
	 * Manipulated image data
	 *
	 * @var  string
	 */
	private $resource = null;

	/**
	 * Image type (png, gif, jpg)
	 *
	 * @var  string
	 */
	private $image_type = IMAGETYPE_PNG;

	/**
	 * EXIF image data
	 *
	 * @var  string
	 */
	private $exif_data = null;

	/**
	 * Configuration options
	 *
	 * @var  array
	 */
	private $config = array();

	/**
	 * Constructor
	 *
	 * @param   string  $image_source  Path to image
	 * @param   array   $config        Optional configurations
	 * @return  void
	 */
	public function __construct($image_source = null, $config = array(), $isRemoteImage = false)
	{
		$this->source = $image_source;
		$this->config = $config;

		if (!$this->checkPackageRequirements('gd'))
		{
			return;
		}

		// check to see if we have an image to work with
		if (is_null($this->source))
		{
			$this->setError(\Lang::txt('[ERROR] Image Source not set.'));
			return;
		}

		//check to make sure its a file if not remote
		if (!$isRemoteImage && !is_file($this->source))
		{
			$this->setError(\Lang::txt('[ERROR] Image doesn\'t exist on the server.'));
			return;
		}

		//open image
		if (!$this->openImage())
		{
			$this->setError(\Lang::txt('[ERROR] Invalid/corrupted image file'));
			return;
		}
	}

	/**
	 * Set the image type
	 *
	 * @param   string  $type  Image type to set
	 * @return  void
	 */
	public function setImageType($type)
	{
		if ($type)
		{
			$this->image_type = $type;
		}

		if ($this->image_type == IMAGETYPE_PNG)
		{
			imagealphablending($this->resource, false);
			imagesavealpha($this->resource, true);
		}
	}

	/**
	 * Check if a required package is installed
	 *
	 * @param   integer  $package  Package name
	 * @return  boolean  True on success
	 */
	private function checkPackageRequirements($package = '')
	{
		if ($package == '')
		{
			return false;
		}

		$installed_exts = get_loaded_extensions();
		if (!in_array($package, $installed_exts))
		{
			$this->setError(\Lang::txt('[ERROR] You are missing the required PHP package %s.', $package));
			return false;
		}

		return true;
	}

	/**
	 * Open an image and get it's type (png, jpg, gif)
	 *
	 * @return  bool
	 */
	private function openImage()
	{
		$image_atts = getimagesize($this->source);
		if (empty($image_atts))
		{
			return false;
		}

		switch ($image_atts['mime'])
		{
			case 'image/jpeg':
				$this->image_type = IMAGETYPE_JPEG;
				$this->resource   = imagecreatefromjpeg($this->source);
			break;
			case 'image/gif':
				$this->image_type = IMAGETYPE_GIF;
				$this->resource   = imagecreatefromgif($this->source);
			break;
			case 'image/png':
			case 'image/x-png':
				$this->image_type = IMAGETYPE_PNG;
				$this->resource   = imagecreatefrompng($this->source);
			break;
			default:
				return false;
			break;
		}

		if ($this->image_type == IMAGETYPE_PNG)
		{
			imagesavealpha($this->resource, true);
			imagealphablending($this->resource, false);
		}

		if (isset($this->config['auto_rotate']) && $this->config['auto_rotate'] == true)
		{
			$this->autoRotate();
		}

		if (!empty($this->resource))
		{
			return true;
		}
		return false;
	}

	/**
	 * Image Rotation
	 *
	 * @return  void
	 */
	public function autoRotate()
	{
		if (!$this->checkPackageRequirements('exif'))
		{
			$this->setError(\Lang::txt('You need the PHP exif library installed to rotate image based on Exif Orientation value.'));
			return false;
		}

		if ($this->image_type == IMAGETYPE_JPEG)
		{
			try
			{
				$this->exif_data = exif_read_data($this->source);
			}
			catch (Exception $e)
			{
				$this->exif_data = array();
			}
			if (isset($this->exif_data['Orientation']))
			{
				switch ($this->exif_data['Orientation'])
				{
					case 2:
						$this->flip(true, false);
						break;

					case 3:
						$this->rotate(180);
						break;

					case 4:
						$this->flip(false, true);
						break;

					case 5:
						$this->rotate(270);
						$this->flip(true, false);
						break;

					case 6:
						$this->rotate(270);
						break;

					case 7:
						$this-rotate(90);
						$this->flip(true, false);
						break;

					case 8:
						$this->rotate(90);
						break;
				}
			}
		}
	}

	/**
	 * Image Flip
	 *
	 * @param   integer  $rotation    Degrees to rotate
	 * @param   integer  $background  Point to rotate from
	 * @return  void
	 */
	public function rotate($rotation = 0, $background = 0)
	{
		if (empty($this->resource))
		{
			return false;
		}
		$resource = imagerotate($this->resource, $rotation, $background);
		imagedestroy($this->resource);
		$this->resource = $resource;
	}

	/**
	 * Image Flip
	 *
	 * @param   boolean  $flip_horizontal  Flip the image horizontally?
	 * @param   boolean  $flip_vertical    Flip the image vertically?
	 * @return  void
	 */
	public function flip($flip_horizontal, $flip_vertical = false)
	{
		if (empty($this->resource))
		{
			return false;
		}
		$resource = $this->resource;
		$width = imagesx($resource);
		$height = imagesy($resource);
		$new_resource = imagecreatetruecolor($width, $height);

		for ($x=0; $x<$width; $x++)
		{
			for ($y=0; $y<$height; $y++)
			{
				if ($flip_horizontal && $flip_vertical)
				{
					imagecopy($new_resource, $resource, $width-$x-1, $height-$y-1, $x, $y, 1, 1);
				}
				else if ($flip_horizontal)
				{
					imagecopy($new_resource, $resource, $width-$x-1, $y, $x, $y, 1, 1);
				}
				else if ($flip_vertical)
				{
					imagecopy($new_resource, $resource, $x, $height-$y-1, $x, $y, 1, 1);
				}
			}
		}

		$this->resource = $new_resource;
		imagedestroy($resource);
	}

	/**
	 * Image Crop
	 *
	 * @param   integer  $top     Top point to crop from
	 * @param   integer  $right   Right point to crop from
	 * @param   integer  $bottom  Bottom point to crop from
	 * @param   integer  $left    Left point to crop from
	 * @return  void
	 */
	public function crop($top, $right = 0, $bottom = 0, $left = 0)
	{
		if (empty($this->resource))
		{
			return false;
		}
		$width      = imagesx($this->resource);
		$height     = imagesy($this->resource);
		$new_width  = $width - ($left + $right);
		$new_height = $height - ($top + $bottom);

		$resource = imagecreatetruecolor($new_width, $new_height);
		imagecopy($resource, $this->resource, 0, 0, $left, $top, $new_width, $new_height);

		imagedestroy($this->resource);
		$this->resource = $resource;
	}

	/**
	 * Image Resize
	 *
	 * @param   integer  $new_dimension  Size to resize image to
	 * @param   boolean  $use_height     Use the height as the baseline? (uses width by default)
	 * @param   boolean  $squared        Make the image square?
	 * @param   boolean  $resample       Resample the image?
	 * @return  void
	 */
	public function resize($new_dimension, $use_height = false, $squared = false, $resample = true)
	{
		if (empty($this->resource))
		{
			return false;
		}
		$percent = false;
		$width   = imagesx($this->resource);
		$height  = imagesy($this->resource);
		$w       = $width;
		$h       = $height;
		$x       = 0;
		$y       = 0;

		if (($new_dimension > $width && !$use_height) || ($new_dimension > $height && $use_height))
		{
			return;
		}

		if ($new_dimension < 1)
		{
			$percent = $new_dimension;
		}
		elseif (substr($new_dimension, -1) == '%')
		{
			$percent = (substr($new_dimension, 0, -1) / 100);
		}

		if ($percent !== false)
		{
			$new_dimension = $use_height ? ($height * $percent) : ($width * $percent);
			$new_dimension = round($new_dimension);
		}

		if ($squared)
		{
			$new_w = $new_dimension;
			$new_h = $new_dimension;
			if (!$use_height)
			{
				$x = ceil(($width - $height) / 2);
				$w = $height;
				$h = $height;
			}
			else
			{
				$y = ceil(($height - $width) / 2);
				$w = $width;
				$h = $width;
			}
		}
		else
		{
			if (!empty($this->config['resize_by']) && $this->config['resize_by'] == 'largest')
			{
				// find whatever is larger and use it for resizing
				$use_height = false;
				if ($height > $width)
				{
					$use_height = true;
				}
			}

			if (!$use_height)
			{
				$new_w = $new_dimension;
				$new_h = floor($height * ($new_w / $width));
			}
			else
			{
				$new_h = $new_dimension;
				$new_w = floor($width * ($new_h / $height));
			}
		}

		$resource = imagecreatetruecolor($new_w, $new_h);

		if ($this->image_type == IMAGETYPE_PNG)
		{
			imagealphablending($resource, false);
			imagesavealpha($resource, true);
			$transparent = imagecolorallocatealpha($resource, 255, 255, 255, 127);
			imagefilledrectangle($resource, 0, 0, $new_w, $new_h, $transparent);
		}

		if ($resample)
		{
			imagecopyresampled($resource, $this->resource, 0, 0, $x, $y, $new_w, $new_h, $w, $h);
		}
		else
		{
			imagecopyresized($resource, $this->resource, 0, 0, $x, $y, $new_w, $new_h, $w, $h);
		}

		imagedestroy($this->resource);
		$this->resource = $resource;
	}

	/**
	 * Image Geo Location Data
	 *
	 * @return  void
	 */
	public function getGeoLocation()
	{
		if (!$this->checkPackageRequirements('exif'))
		{
			$this->setError(\Lang::txt('You need the PHP exif library installed to rotate image based on Exif Orientation value.'));
			return false;
		}

		try
		{
			$this->exif_data = exif_read_data($this->source);
		}
		catch (Exception $e)
		{
			$this->exif_data = array();
		}

		if (isset($this->exif_data['GPSLatitude']))
		{
			$lat      = $this->exif_data['GPSLatitude'];
			$lat_dir  = $this->exif_data['GPSLatitudeRef'];
			$long     = $this->exif_data['GPSLongitude'];
			$long_dir = $this->exif_data['GPSLongitudeRef'];

			$latitude  = $this->geo_single_fracs2dec($lat);
			$longitude = $this->geo_single_fracs2dec($long);
			$latitude_formatted  = $this->geo_pretty_fracs2dec($lat) . $lat_dir;
			$longitude_formatted = $this->geo_pretty_fracs2dec($long) . $long_dir;

			if ($lat_dir == 'S')
			{
				$latitude *= -1;
			}

			if ($long_dir == 'W')
			{
				$longitude *= -1;
			}

			$geo = array(
				'latitude'  => $latitude,
				'longitude' => $longitude,
				'latitude_formatted'  => $latitude_formatted,
				'longitude_formatted' => $longitude_formatted
			);
		}
		else
		{
			$geo = array();
		}

		return $geo;
	}

	/**
	 * Convert a fraction to decimal
	 *
	 * @param   string   $str  Fraction to convert
	 * @return  integer
	 */
	private function geo_frac2dec($str)
	{
		list($n, $d) = explode('/', $str);

		if (!empty($d))
		{
			return $n / $d;
		}

		return $str;
	}

	/**
	 * Convert fractions to decimals with formatting
	 *
	 * @param   array   $fracs  Fractions to convert
	 * @return  string
	 */
	private function geo_pretty_fracs2dec($fracs)
	{
		return $this->geo_frac2dec($fracs[0]) . '&deg; ' . $this->geo_frac2dec($fracs[1]) . '&prime; ' . $this->geo_frac2dec($fracs[2]) . '&Prime; ';
	}

	/**
	 * Convert fractions to decimals
	 *
	 * @param   array    $fracs  Fractions to convert
	 * @return  integer
	 */
	private function geo_single_fracs2dec($fracs)
	{
		return $this->geo_frac2dec($fracs[0]) + $this->geo_frac2dec($fracs[1]) / 60 + $this->geo_frac2dec($fracs[2]) / 3600;
	}

	/**
	 * Display an image
	 *
	 * @return  void
	 */
	public function display()
	{
		$image_atts = getimagesize($this->source);
		header('Content-type: ' . $image_atts['mime']);
		$this->output(null);
	}

	/**
	 * Display an image inline
	 *
	 * @return  string
	 **/
	public function inline()
	{
		// Start buffer and grab output
		ob_start();
		$this->output(null);
		$image_data = ob_get_contents();
		ob_end_clean();

		// Encode and build data uri
		$base64 = base64_encode($image_data);
		$image_atts = getimagesize($this->source);

		return 'data:' . $image_atts['mime'] . ';base64,' . $base64;
	}

	/**
	 * Save an image
	 *
	 * @param   string   $save_path   Path to save image
	 * @param   boolean  $make_paths  Allow for path generation?
	 * @return  void
	 */
	public function save($save_path = null, $make_paths = false)
	{
		$path = $this->source;

		if (!is_null($save_path))
		{
			$info = pathinfo($save_path);

			if ($make_paths)
			{
				\App::get('filesystem')->makeDirectory($info['dirname']);
			}

			if (!is_dir($info['dirname']) && $make_paths == false)
			{
				$this->setError(\Lang::txt('You must supply a valid path or allow save function to create recursive path'));
				return;
			}

			$path = $save_path;
		}

		$this->output($path);
	}

	/**
	 * Generate an image and save to a location
	 *
	 * @param   string  $save_path  Path to save image
	 * @return  void
	 */
	private function output($save_path)
	{
		if ($this->resource != null)
		{
			switch ($this->image_type)
			{
				case IMAGETYPE_PNG:
					imagepng($this->resource, $save_path);
					break;

				case IMAGETYPE_GIF:
					imagegif($this->resource, $save_path);
					break;

				case IMAGETYPE_JPEG:
					imagejpeg($this->resource, $save_path);
					break;
			}
		}
	}
}
