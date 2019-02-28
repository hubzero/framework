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
 * @package   framework
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Rules;

use Hubzero\Form\Form;
use Hubzero\Form\Rule;
use Exception;

/**
 * Form Rule class for testing a value equals another.
 */
class Equals extends Rule
{
	/**
	 * Method to test for a valid color in hexadecimal.
	 *
	 * @param   object   &$element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed    $value     The form field value to validate.
	 * @param   string   $group     The field name group control value. This acts as as an array container for the field.
	 *                              For example if the field has name="foo" and the group value is set to "bar" then the
	 *                              full field name would end up being "bar[foo]".
	 * @param   object   &$input    An optional Registry object with the entire data set to validate against the entire form.
	 * @param   object   &$form     The form object for which the field is being tested.
	 * @return  boolean  True if the value is valid, false otherwise.
	 */
	public function test(&$element, $value, $group = null, &$input = null, &$form = null)
	{
		// Initialize variables.
		$field = (string) $element['field'];

		// Check that a validation field is set.
		if (!$field)
		{
			return new Exception('JLIB_FORM_INVALID_FORM_RULE' . get_class($this));
		}

		// Check that a valid Form object is given for retrieving the validation field value.
		if (!($form instanceof Form))
		{
			return new Exception('JLIB_FORM_INVALID_FORM_OBJECT' . get_class($this));
		}

		// Test the two values against each other.
		if ($value == $input->get($field))
		{
			return true;
		}

		return false;
	}
}
