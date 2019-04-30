<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Geocode\Result;

use Geocoder\Result\ResultFactoryInterface;

/**
 * Countries result factory
 */
class CountriesResultFactory implements ResultFactoryInterface
{
	/**
	 * {@inheritDoc}
	 */
	final public function createFromArray(array $data)
	{
		$result = new \SplObjectStorage();
		foreach ($data as $row)
		{
			$instance = $this->newInstance();
			$instance->fromArray($row);
			$result->attach($instance);
		}

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
