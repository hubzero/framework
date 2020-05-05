<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Geocode\Result;

use Geocoder\Result\ResultFactoryInterface;

/**
 * Country result factory
 */
class CountryResultFactory implements ResultFactoryInterface
{
	/**
	 * {@inheritDoc}
	 */
	final public function createFromArray(array $data)
	{
		$result = $this->newInstance();
		$result->fromArray(isset($data[0]) ? $data[0] : $data);

		return $result;
	}

	/**
	 * {@inheritDoc}
	 */
	public function newInstance()
	{
		return new Country();
	}
}
