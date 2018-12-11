<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Utility;

/**
 * IP address class
 */
class Dns
{
	/**
	 * Get FQDN from the config
	 * 
	 * @return string
	 */
	private static function _getConfig()
	{
		return \Config::get('app.fqdn', '');
	}

	/**
	 * Get array of domains of FQDN
	 * 
	 * @return array
	 */
	private static function _getConfigArray()
	{
		return explode('.', self::_getConfig());
	}

	/**
	 * Get hostname from FQDN
	 * 
	 * @return string
	 */
	public static function hostname()
	{
		$arr = self::_getConfigArray();
		if (is_empty($arr))
		{
			return '';
		}
		return self::_getConfigArray()[0];
	}

	/**
	 * Get top level domain
	 * 
	 * @return string
	 */
	public static function tld()
	{
		$arr = self::_getConfigArray();
		if (is_empty($arr))
		{
			return '';
		}
		$tld = end($arr);
		return $tld;
	}

	/**
	 * Get FQDN
	 * 
	 * @return string
	 */
	public static function fqdn()
	{
		return self::_getConfig();
	}

	/**
	 * Get parent domain
	 * 
	 * @return string
	 */
	public static function domain()
	{
		$arr = self::_getConfigArray();
		array_shift($arr);
		if (is_null($arr))
		{
			$arr = [];
		}

		$domain = implode('.', $arr);
		return $domain;
	}

	/**
	 * Get subdomains *excluding* the hostname and TLD
	 * 
	 * @return array
	 */
	public static function subdomains()
	{
		$domains = self::_getConfigArray();
		array_pop($domains);
		// Config contained only a single domain (".com")
		if (is_null($domains))
		{
			$domains = [];
			return $domains;
		}

		// Config contained no subdomains ("example.com")
		array_shift($domains);
		if (is_null($domains))
		{
			$domains = [];
			return $domains;
		}

		return $domains;
	}
}
