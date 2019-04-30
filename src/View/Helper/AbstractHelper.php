<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\View\View;

/**
 * Abstract view helper class
 */
abstract class AbstractHelper implements HelperInterface
{
	/**
	 * View object instance
	 *
	 * @var  object
	 */
	protected $_view = null;

	/**
	 * Set the View object
	 *
	 * @param   object  $view
	 * @return  object
	 */
	public function setView(View $view)
	{
		$this->_view = $view;

		return $this;
	}

	/**
	 * Get the view object
	 *
	 * @return  null|Renderer
	 */
	public function getView()
	{
		return $this->_view;
	}
}
