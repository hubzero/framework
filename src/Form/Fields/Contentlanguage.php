<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Contentlanguage as ContentLang;

/**
 * Provides a list of content languages
 */
class Contentlanguage extends Select
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Contentlanguage';

	/**
	 * Method to get the field options for content languages.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), ContentLang::existing());
	}
}
