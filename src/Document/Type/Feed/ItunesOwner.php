<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Document\Type\Feed;

use Hubzero\Base\Obj;

/**
 * Class for storing iTunes owner information
 */
class ItunesOwner extends Obj
{
	/**
	 * Email attribute
	 *
	 * @var  string
	 */
	public $email = '';

	/**
	 * Name attribute
	 *
	 * @var  string
	 */
	public $name = '';
}
