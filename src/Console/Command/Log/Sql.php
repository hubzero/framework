<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Console\Command\Log;

/**
 * Sql log class
 **/
class Sql extends Base
{
	/**
	 * Fields available in this log and their default visibility
	 *
	 * @var  array
	 **/
	protected static $fields = array(
		'timestamp'     => true,
		'file'          => true,
		'line'          => true,
		'type'          => true,
		'time'          => true,
		'query'         => true
	);

	/**
	 * If dates/times are present, how are they formatted
	 *
	 * @var  string
	 **/
	protected static $dateFormat = "Y-m-d\TH:i:s.uP";

	/**
	 * Get log path
	 *
	 * @return  string
	 **/
	public static function path()
	{
		$dir = \Config::get('log_path');

		if (is_dir('/var/log/hubzero-cms'))
		{
			$dir = '/var/log/hubzero-cms';
		}

		$path = $dir . '/sql.log';

		return $path;
	}
}
