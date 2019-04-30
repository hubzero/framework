<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Config;

use Hubzero\Config\Exception\UnsupportedFormatException;
use Hubzero\Config\Exception\EmptyDirectoryException;

/**
 * File loader class
 */
class FileLoader
{
	/**
	 * The default configuration path.
	 *
	 * @var  string
	 */
	protected $defaultPath;

	/**
	 * Create a new file configuration loader.
	 *
	 * @param   string  $defaultPath
	 * @return  void
	 */
	public function __construct($defaultPath)
	{
		$this->defaultPath  = $defaultPath;
	}

	/**
	 * Load the given configuration group.
	 *
	 * @param   string  $client
	 * @return  array
	 */
	public function load($client = null)
	{
		$data = array();

		// First we'll get the root configuration path for the environment which is
		// where all of the configuration files live for that namespace, as well
		// as any environment folders with their specific configuration items.
		try
		{
			$paths = $this->getPath($this->defaultPath);

			if (empty($paths))
			{
				throw new EmptyDirectoryException("Configuration directory: [" . $this->defaultPath . "] is empty");
			}

			foreach ($paths as $path)
			{
				// Get file information
				$info      = pathinfo($path);
				$group     = isset($info['filename'])  ? strtolower($info['filename'])  : '';
				$extension = isset($info['extension']) ? strtolower($info['extension']) : '';
				if (!$extension || $extension == 'html')
				{
					continue;
				}
				$parser    = $this->getParser($extension);

				$data[$group] = $parser->parse($path);
			}

			if (empty($data))
			{
				throw new EmptyDirectoryException("Configuration directory: [" . $this->defaultPath . "] is empty");
			}

			if ($client)
			{
				$paths = $this->getPath($this->defaultPath . DIRECTORY_SEPARATOR . $client);

				foreach ($paths as $path)
				{
					// Get file information
					$info      = pathinfo($path);
					$group     = isset($info['filename'])  ? strtolower($info['filename'])  : '';
					$extension = isset($info['extension']) ? strtolower($info['extension']) : '';
					if (!$extension || $extension == 'html')
					{
						continue;
					}
					$parser    = $this->getParser($extension);

					if (!isset($data[$group]))
					{
						$data[$group] = array();
					}
					$data[$group] = array_replace_recursive(
						$data[$group],
						$parser->parse($path)
					);
				}
			}
		}
		catch (\Exception $e)
		{
			$loader = new Legacy();
			$loader->split();

			$data = $loader->toArray();
		}

		return $data;
	}

	/**
	 * Merge the items in the given file into the items.
	 *
	 * @param   array   $items
	 * @param   string  $file
	 * @return  array
	 */
	protected function mergeEnvironment(array $items, $file)
	{
		return array_replace_recursive($items, $this->getRequire($file));
	}

	/**
	 * Gets a parser for a given file extension
	 *
	 * @param   string  $extension
	 * @return  object
	 * @throws  UnsupportedFormatException  If `$extension` is an unsupported file format
	 */
	protected function getParser($extension)
	{
		$parser = null;

		$extension = strtolower($extension);

		foreach (Processor::all() as $fileParser)
		{
			if (in_array($extension, $fileParser->getSupportedExtensions()))
			{
				$parser = $fileParser;
				break;
			}
		}

		// If none exist, then throw an exception
		if ($parser === null)
		{
			throw new UnsupportedFormatException(sprintf('Unsupported configuration format "%s"', $extension));
		}

		return $parser;
	}

	/**
	 * Checks `$path` to see if it is either an array, a directory, or a file
	 *
	 * @param   mixed  $path
	 * @return  array
	 */
	protected function getPath($path)
	{
		$paths = array();

		// If `$path` is an array
		if (is_array($path))
		{
			foreach ($path as $unverifiedPath)
			{
				$paths = array_merge($paths, $this->getPath($unverifiedPath));
			}

			return $paths;
		}

		// If `$path` is a directory
		if (is_dir($path))
		{
			$paths = glob($path . '/*.*');

			return $paths;
		}

		// If `$path` is a file
		if (file_exists($path))
		{
			$paths[] = $path;
		}

		return $paths;
	}
}
