<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base\Client;

/**
 * Site client
 */
class Testing implements ClientInterface
{
	/**
	 * ID
	 *
	 * @var  integer
	 */
	public $id = 5;

	/**
	 * Name
	 *
	 * @var  string
	 */
	public $name = 'testing';

	/**
	 * A url to init this client.
	 *
	 * @var  string
	 */
	public $url = 'testing';
}
