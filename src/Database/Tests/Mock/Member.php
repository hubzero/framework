<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\Mock;

use Hubzero\Database\Relational;

/**
 * Member mock model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Member extends Relational
{
	/**
	 * Inverse relationship with the shifter object
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 **/
	public function memberable()
	{
		return $this->shifter();
	}

	/**
	 * Many shifts to many relationship with permissions
	 *
	 * @return  \Hubzero\Database\Relationship\ManyShiftsToMany
	 **/
	public function permissions()
	{
		return $this->manyShiftsToMany('Permission');
	}
}
