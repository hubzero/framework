<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Notification;

/**
 * Message store
 */
interface MessageStore
{
	/**
	 * Store a message
	 *
	 * @param   array   $data
	 * @param   string  $domain
	 * @return  void
	 */
	public function store($data, $domain);

	/**
	 * Return a list of messages
	 *
	 * @param   array   $data
	 * @param   string  $domain
	 * @return  array
	 */
	public function retrieve($domain);

	/**
	 * Clear all messages
	 *
	 * @param   string  $domain
	 * @return  void
	 */
	public function clear($domain);

	/**
	 * Return a count of messages
	 *
	 * @param   string  $domain
	 * @return  integer
	 */
	public function total($domain);
}
