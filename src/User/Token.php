<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\User;

/**
 * User password reset token database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Token extends \Hubzero\Database\Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xprofiles';

	/**
	 * Fields and their validation criteria
	 *
	 * @var    array
	 * @since  2.1.0
	 */
	protected $rules = array(
		'user_id' => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);
}
