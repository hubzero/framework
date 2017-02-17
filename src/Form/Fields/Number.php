<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Form\Field;

/**
 * Supports a numeric field.
 */
class Number extends Field
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Number';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$attributes = array(
			'type'         => 'number',
			'value'        => htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8'),
			'name'         => $this->name,
			'id'           => $this->id,
			'min'          => (!is_null($this->element['min']) ? (int) $this->element['min'] : null),
			'max'          => ($this->element['max'] ? (int) $this->element['max'] : null),
			'step'         => ($this->element['step'] ? (int) $this->element['step'] : null),
			'pattern'      => ($this->element['pattern'] ? $this->element['pattern'] : null),
			'class'        => ($this->element['class'] ? (string) $this->element['class'] : null),
			'readonly'     => ((string) $this->element['readonly'] == 'true' ? 'readonly' : null),
			'disabled'     => ((string) $this->element['disabled'] == 'true' ? 'disabled' : null),
			'onchange'     => ($this->element['onchange']  ? (string) $this->element['onchange'] : null)
		);

		$attr = array();
		foreach ($attributes as $key => $value)
		{
			if ($key != 'value' && $key != 'min' && !$value)
			{
				continue;
			}
			if ($key == 'min' && is_null($value))
			{
				continue;
			}

			$attr[] = $key . '="' . $value . '"';
		}
		$attr = implode(' ', $attr);

		return '<input ' . $attr . ' />';
	}
}
